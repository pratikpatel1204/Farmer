<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Profile;
use App\Models\State;
use App\Models\User;
use App\Support\Notify;
use App\Support\SimpleXlsx;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class MemberDetailsController extends Controller
{
    /** tab key => role */
    private const TABS = [
        'all'        => null,
        'farmer'     => 'Farmer',
        'non-farmer' => 'Non Farmer',
    ];

    private const PER_PAGE = 10;

    /** Profile "complete" maana jayega jab ye sab fields bhare hon */
    private const PROFILE_REQUIRED_FIELDS = [
        'description',
        'bank_name',
        'account_number',
        'ifsc_code',
        'aadhar_number',
        'pan_number',
        'aadhar_image',
        'personal_pan_image',
    ];

    /** Search in columns me hota hai */
    private const SEARCH_COLUMNS = ['full_name', 'unique_id', 'email', 'mobile', 'temp_password', 'sponsor_id', 'sponsor_name'];

    /* ===================== LIST ===================== */

    public function index()
    {
        $states = State::orderBy('name')->get(['id', 'name']);

        return view('admin.member_details.index', compact('states'));
    }

    /**
     * Ajax: tab + search + filters + pagination -> sirf table partial
     */
    public function table(Request $request)
    {
        $tab = $this->currentTab($request);

        // Filtered base (tab ke bina) -> isi se tab counts, taaki badge filter ke hisaab se dikhe
        $filtered = $this->filteredQuery($request);

        $countRow = (clone $filtered)
            ->selectRaw("COUNT(*) as total,
                SUM(CASE WHEN role = 'Farmer' THEN 1 ELSE 0 END) as farmer,
                SUM(CASE WHEN role = 'Non Farmer' THEN 1 ELSE 0 END) as non_farmer")
            ->toBase()
            ->first();

        $profileColumns = array_merge(['id', 'user_id', 'status', 'reject_reason'], self::PROFILE_REQUIRED_FIELDS);

        $query = (clone $filtered)
            ->with(['profile' => fn($q) => $q->select($profileColumns)])
            ->withCount('products')
            ->when(self::TABS[$tab], fn($q, $role) => $q->where('role', $role))
            ->latest('id');

        $members = (clone $query)->paginate(self::PER_PAGE);

        if ($members->isEmpty() && $members->currentPage() > 1) {
            $members = (clone $query)->paginate(self::PER_PAGE, ['*'], 'page', $members->lastPage());
        }

        // State / City ke naam ek hi query me (N+1 se bachav)
        $stateNames = State::whereIn('id', $members->pluck('state')->filter()->unique())->pluck('name', 'id');
        $cityNames  = City::whereIn('id', $members->pluck('city')->filter()->unique())->pluck('name', 'id');

        $members->getCollection()->each(function ($m) use ($stateNames, $cityNames) {
            $m->profile_complete = $this->isProfileComplete($m->profile);
            $m->state_name       = $stateNames[$m->state] ?? null;
            $m->city_name        = $cityNames[$m->city] ?? null;
        });

        return response()->json([
            'html'   => view('admin.member_details.table', compact('members'))->render(),
            'counts' => [
                'all'        => (int) $countRow->total,
                'farmer'     => (int) $countRow->farmer,
                'non-farmer' => (int) $countRow->non_farmer,
            ],
        ]);
    }

    /**
     * Excel (.xlsx) export: wahi tab + search + filters jo list par lage hain
     */
    public function export(Request $request)
    {
        $tab = $this->currentTab($request);

        $query = $this->filteredQuery($request)
            ->with(['profile' => fn($q) => $q->select(array_merge(['id', 'user_id', 'status', 'reject_reason'], self::PROFILE_REQUIRED_FIELDS))])
            ->withCount('products')
            ->when(self::TABS[$tab], fn($q, $role) => $q->where('role', $role))
            ->orderByDesc('id');

        $stateNames = State::pluck('name', 'id');

        $xlsx = new SimpleXlsx('Members');
        $xlsx->setColumnWidths([6, 14, 26, 12, 14, 24, 28, 14, 10, 18, 18, 20, 10, 40, 10, 10, 10, 14, 30, 12]);
        $xlsx->addRow([
            '#', 'Member ID', 'Name', 'Role', 'Sponsor ID', 'Sponsor Name', 'Email', 'Mobile', 'Mobile Verified',
            'State', 'City', 'Area', 'Pincode', 'Address', 'Total Products', 'Profile Complete', 'Status',
            'Profile Status', 'Reject Reason', 'Joined Date',
        ], true);

        $sr = 0;
        $query->chunk(500, function ($members) use ($xlsx, $stateNames, &$sr) {
            $cityNames = City::whereIn('id', $members->pluck('city')->filter()->unique())->pluck('name', 'id');

            foreach ($members as $m) {
                $profile = $m->profile;

                $xlsx->addRow([
                    ++$sr,
                    (string) $m->unique_id,
                    (string) $m->full_name,
                    (string) $m->role,
                    (string) ($m->sponsor_id ?? ''),
                    (string) ($m->sponsor_name ?? ''),
                    (string) ($m->email ?? ''),
                    (string) ($m->mobile ?? ''),
                    (int) $m->mobile_verified === 1 ? 'Yes' : 'No',
                    (string) ($stateNames[$m->state] ?? ''),
                    (string) ($cityNames[$m->city] ?? ''),
                    (string) ($m->area ?? ''),
                    (string) ($m->pincode ?? ''),
                    (string) ($m->address ?? ''),
                    (int) ($m->products_count ?? 0),
                    $this->isProfileComplete($profile) ? 'Yes' : 'No',
                    (int) $m->status === 1 ? 'Active' : 'Inactive',
                    $profile ? ($profile->status ?: 'Pending') : 'Not Created',
                    $profile && $profile->status === 'Rejected' ? (string) $profile->reject_reason : '',
                    optional($m->created_at)->format('d-m-Y') ?? '',
                ]);
            }
        });

        return $xlsx->download('members_' . $tab . '_' . now()->format('d-m-Y_His') . '.xlsx');
    }

    /* ===================== CREATE / EDIT ===================== */

    public function create()
    {
        $member = null;
        $states = State::orderBy('name')->get(['id', 'name']);
        $cities = collect();

        return view('admin.member_details.form', compact('member', 'states', 'cities'));
    }

    public function edit($id)
    {
        $member = User::where('role', '!=', 'Admin')->findOrFail($id);
        $states = State::orderBy('name')->get(['id', 'name']);
        $cities = $member->state
            ? City::where('state_id', $member->state)->orderBy('name')->get(['id', 'name'])
            : collect();

        return view('admin.member_details.form', compact('member', 'states', 'cities'));
    }

    public function store(Request $request)
    {
        $data    = $this->validateMember($request);
        $sponsor = User::where('unique_id', $data['sponsor_id'])->first();

        $user = new User();
        $user->forceFill([
            'full_name'      => $data['full_name'],
            'role'           => $data['role'],
            'unique_id'      => $this->generateUniqueId($data['role']),
            'sponsor_id'     => $sponsor->unique_id,
            'sponsor_name'   => $sponsor->full_name,
            'email'          => $data['email'] ?? null,
            'password'       => Hash::make($data['password']),
            'temp_password'  => $data['password'],
            'mobile'         => $data['mobile'],
            'state'          => $data['state'],
            'city'           => $data['city'],
            'area'           => $data['area'],
            'pincode'        => $data['pincode'],
            'address'        => $data['address'],
            'address_source' => $data['address_source'],
            'status'         => (int) $data['status'],
        ])->save();

        return response()->json([
            'success'  => true,
            'message'  => "Member created successfully. ID: {$user->unique_id}",
            'redirect' => route('admin.member.details'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $member = User::where('role', '!=', 'Admin')->findOrFail($id);
        $data   = $this->validateMember($request, $member);

        $fill = [
            'full_name' => $data['full_name'],
            'role'      => $data['role'],
            'email'     => $data['email'] ?? null,
            'mobile'    => $data['mobile'],
            'state'     => $data['state'],
            'city'      => $data['city'],
            'area'      => $data['area'],
            'pincode'   => $data['pincode'],
            'address'   => $data['address'],
            'address_source' => $data['address_source'],
            'status'    => (int) $data['status'],
        ];

        if (!empty($data['password'])) {
            $fill['password']      = Hash::make($data['password']);
            $fill['temp_password'] = $data['password'];
        }

        $member->forceFill($fill)->save();

        // Is member ke downline me sponsor_name bhi update
        User::where('sponsor_id', $member->unique_id)->update(['sponsor_name' => $member->full_name]);

        return response()->json([
            'success'  => true,
            'message'  => 'Member updated successfully.',
            'redirect' => route('admin.member.details'),
        ]);
    }

    /**
     * Ajax: sponsor ID check -> sponsor ka naam
     */
    public function sponsorCheck(Request $request)
    {
        $sponsor = User::where('unique_id', trim((string) $request->input('sponsor_id')))
            ->first(['full_name', 'unique_id', 'status']);

        if (!$sponsor) {
            return response()->json(['success' => false, 'message' => 'Invalid sponsor ID']);
        }

        return response()->json(['success' => true, 'name' => $sponsor->full_name]);
    }

    /**
     * Ajax: state ke cities
     */
    public function cities($stateId)
    {
        return response()->json(
            City::where('state_id', $stateId)->orderBy('name')->get(['id', 'name'])
        );
    }

    /* ===================== PROFILE VIEW ===================== */

    public function show($id)
    {
        $member = User::where('role', '!=', 'Admin')->with('profile')->withCount('products')->findOrFail($id);
        abort_if(!$member->profile, 404, 'Profile not created');

        $profile         = $member->profile;
        $stateName       = $member->state ? State::where('id', $member->state)->value('name') : null;
        $cityName        = $member->city ? City::where('id', $member->city)->value('name') : null;
        $images          = $this->decodeImages($profile->company_images);
        $descriptionHtml = $this->safeHtml($profile->description);
        $profileComplete = $this->isProfileComplete($profile);

        return view('admin.member_details.view', compact(
            'member',
            'profile',
            'stateName',
            'cityName',
            'images',
            'descriptionHtml',
            'profileComplete'
        ));
    }

    /* ===================== STATUS / DELETE / LOGIN ===================== */

    public function updateStatus(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'status'  => 'required|in:0,1',
        ]);

        $user = User::where('role', '!=', 'Admin')->find($request->user_id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Member not found.']);
        }

        $user->update(['status' => (int) $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Member ' . ($user->status ? 'activated' : 'deactivated') . ' successfully.',
        ]);
    }

    public function updateProfileStatus(Request $request)
    {
        $request->validate([
            'profile_id' => 'required|integer|exists:profiles,id',
            'status'     => 'required|in:Approved,Rejected',
            'reason'     => 'required_if:status,Rejected|nullable|string|max:500',
        ], [
            'reason.required_if' => 'Rejection reason is required.',
        ]);

        $profile = Profile::findOrFail($request->profile_id);
        $profile->update([
            'status'        => $request->status,
            'reject_reason' => $request->status === 'Rejected' ? trim($request->reason) : null,
        ]);

        // Member ko "Profile Approved" / "Profile Rejected" (reason ke saath)
        Notify::profileStatus(
            (int) $profile->user_id,
            $request->status,
            $request->status === 'Rejected' ? trim($request->reason) : null
        );

        return response()->json([
            'success' => true,
            'message' => 'Profile ' . strtolower($request->status) . ' successfully.',
        ]);
    }

    public function destroy($id)
    {
        $user = User::where('role', '!=', 'Admin')->find($id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Member not found.']);
        }

        $user->delete();

        return response()->json(['success' => true, 'message' => 'Member deleted successfully.']);
    }

    public function loginAsMember(string $uniqueId)
    {
        $member = User::where('unique_id', $uniqueId)->where('role', '!=', 'Admin')->firstOrFail();

        if ((int) $member->status !== 1) {
            return back()->with('error', 'Inactive member me login nahi ho sakta.');
        }

        Auth::guard('web')->login($member);
        request()->session()->regenerate();

        // TODO: apne member dashboard ka route name yahan lagao
        return redirect()->route('member.dashboard');
    }

    /* ===================== HELPERS ===================== */

    private function currentTab(Request $request): string
    {
        return array_key_exists($request->input('tab'), self::TABS) ? $request->input('tab') : 'all';
    }

    /**
     * Search + saare filters (tab ko chhod kar). List, tab counts aur export teeno yahi use karte hain.
     */
    private function filteredQuery(Request $request): Builder
    {
        $search         = trim((string) $request->input('search', ''));
        $stateId        = (int) $request->input('state_id');
        $cityId         = (int) $request->input('city_id');
        $pincode        = preg_replace('/\D/', '', (string) $request->input('pincode', ''));
        $status         = (string) $request->input('status', '');
        $profileStatus  = (string) $request->input('profile_status', '');
        $profileDone    = (string) $request->input('profile_complete', '');
        $mobileVerified = (string) $request->input('mobile_verified', '');
        $fromDate       = $this->parseDate($request->input('from_date'));
        $toDate         = $this->parseDate($request->input('to_date'));

        return User::query()
            ->where('role', '!=', 'Admin')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    foreach (self::SEARCH_COLUMNS as $col) {
                        $q->orWhere($col, 'like', "%{$search}%");
                    }
                });
            })
            ->when($stateId > 0, fn($q) => $q->where('state', $stateId))
            ->when($cityId > 0, fn($q) => $q->where('city', $cityId))
            ->when($pincode !== '', fn($q) => $q->where('pincode', 'like', "{$pincode}%"))
            ->when(in_array($status, ['0', '1'], true), fn($q) => $q->where('status', (int) $status))
            ->when(in_array($mobileVerified, ['0', '1'], true), function ($q) use ($mobileVerified) {
                $mobileVerified === '1'
                    ? $q->where('mobile_verified', 1)
                    : $q->where(fn($q) => $q->whereNull('mobile_verified')->orWhere('mobile_verified', '!=', 1));
            })
            ->when($profileStatus !== '', function ($q) use ($profileStatus) {
                match ($profileStatus) {
                    'Approved', 'Rejected' => $q->whereHas('profile', fn($p) => $p->where('status', $profileStatus)),
                    'Pending'     => $q->whereHas('profile', fn($p) => $p->where(
                        fn($p) => $p->whereNull('status')->orWhereNotIn('status', ['Approved', 'Rejected'])
                    )),
                    'not_created' => $q->whereDoesntHave('profile'),
                    default       => null,
                };
            })
            ->when(in_array($profileDone, ['0', '1'], true), function ($q) use ($profileDone) {
                $complete = function ($p) {
                    foreach (self::PROFILE_REQUIRED_FIELDS as $field) {
                        $p->whereNotNull($field)->where($field, '!=', '');
                    }
                };
                $profileDone === '1' ? $q->whereHas('profile', $complete) : $q->whereDoesntHave('profile', $complete);
            })
            ->when($fromDate, fn($q, $d) => $q->where('created_at', '>=', $d->copy()->startOfDay()))
            ->when($toDate, fn($q, $d) => $q->where('created_at', '<=', $d->copy()->endOfDay()));
    }

    private function parseDate($value): ?Carbon
    {
        if (blank($value)) {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', (string) $value)->startOfDay();
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function validateMember(Request $request, ?User $member = null): array
    {
        $rules = [
            'role'      => 'required|in:Farmer,Non Farmer',
            'full_name' => 'required|string|max:100',
            'mobile'    => ['required', 'regex:/^[6-9]\d{9}$/', Rule::unique('users', 'mobile')->ignore($member?->id)],
            'email'     => ['nullable', 'email', 'max:150', Rule::unique('users', 'email')->ignore($member?->id)],
            'password'  => ($member ? 'nullable' : 'required') . '|string|min:6|confirmed',
            'state'     => 'required|integer|exists:states,id',
            'city'      => 'required|integer|exists:cities,id',
            'area'      => 'required|string|max:150',
            'pincode'   => 'required|digits:6',
            'address'   => 'required|string|max:500',
            'address_source' => 'required|in:google,manual',
            'status'    => 'required|in:0,1',
        ];

        // Sponsor sirf create par (edit me tree todne se bachane ke liye change nahi hota)
        if (!$member) {
            $rules['sponsor_id'] = ['required', Rule::exists('users', 'unique_id')->whereNull('deleted_at')];
        }

        $data = $request->validate($rules, [
            'mobile.regex'      => 'Please enter a valid 10-digit mobile number.',
            'sponsor_id.exists' => 'Invalid sponsor ID.',
        ]);

        // City usi state ki honi chahiye
        $cityOk = City::where('id', $data['city'])->where('state_id', $data['state'])->exists();
        if (!$cityOk) {
            abort(response()->json([
                'message' => 'Selected city does not belong to the selected state.',
                'errors'  => ['city' => ['Selected city does not belong to the selected state.']],
            ], 422));
        }

        return $data;
    }

    private function generateUniqueId(string $role): string
    {
        $prefix = $role === 'Farmer' ? 'FRM' : 'NFR';

        do {
            $id = $prefix . random_int(100000, 999999);
        } while (User::withTrashed()->where('unique_id', $id)->exists());

        return $id;
    }

    private function isProfileComplete($profile): bool
    {
        if (!$profile) {
            return false;
        }

        foreach (self::PROFILE_REQUIRED_FIELDS as $field) {
            if (blank($profile->{$field})) {
                return false;
            }
        }

        return true;
    }

    private function decodeImages($value): array
    {
        $images = is_array($value) ? $value : json_decode($value ?? '[]', true);
        if (is_string($images)) {
            $images = json_decode($images, true); // double-encoded JSON
        }

        return is_array($images) ? array_values(array_filter($images)) : [];
    }

    /**
     * CKEditor HTML: sirf basic tags, saare attributes hata kar (XSS se bachav)
     */
    private function safeHtml(?string $html): string
    {
        if (blank($html)) {
            return '';
        }

        $clean = strip_tags($html, '<p><br><strong><b><em><i><u><ul><ol><li><h1><h2><h3><h4><h5><h6><blockquote>');

        return preg_replace('/<(\/?)(\w+)[^>]*>/', '<$1$2>', $clean);
    }
}