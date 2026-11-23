<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardMemberController extends Controller
{
    /** products.status ki values (lowercase me match hoti hain). DB me alag value ho to yahan add karo. */
    private const PRODUCT_APPROVED = ['approved', 'approve', 'active', '1'];
    private const PRODUCT_REJECTED = ['rejected', 'reject', 'declined', '2'];
    // Baaki sab (pending, 0, null, empty) Pending me count hota hai.

    /** product_inquiries.status: 0 = Pending, 1 = Contacted, 2 = Closed */
    private const INQUIRY_PENDING   = 0;
    private const INQUIRY_CONTACTED = 1;
    private const INQUIRY_CLOSED    = 2;

    /** Downline kitne level tak gini jaye (loop safety) */
    private const MAX_LEVELS = 50;

    public function dashboard()
    {
        $user    = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();

        $productStats = $this->productStats($user->id);
        $inquiryStats = $this->inquiryStats($user->id);
        $teamStats    = $this->teamStats($user->unique_id);

        return view('member.dashboard', compact('user', 'profile', 'productStats', 'inquiryStats', 'teamStats'));
    }

    /** products.listing_type 'have' / 'want' ko key me badalta hai, baaki null */
    private function typeKey($type): ?string
    {
        $type = strtolower(trim((string) $type));

        if (str_contains($type, 'want')) {
            return 'want';
        }
        if (str_contains($type, 'have')) {
            return 'have';
        }

        return null;
    }

    /**
     * Member ke products, I Have aur I Want alag:
     * ['total' => .., 'have' => [total, approved, pending, rejected], 'want' => [...]]
     */
    private function productStats(int $userId): array
    {
        $blank = ['total' => 0, 'approved' => 0, 'pending' => 0, 'rejected' => 0];
        $stats = ['total' => 0, 'approved' => 0, 'have' => $blank, 'want' => $blank];

        $query = DB::table('products')->where('member_id', $userId);
        if ($this->hasColumn('products', 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        $rows = $query->select('listing_type', 'status', DB::raw('COUNT(*) as total'))
            ->groupBy('listing_type', 'status')
            ->get();

        foreach ($rows as $row) {
            $count  = (int) $row->total;
            $status = strtolower(trim((string) $row->status));

            if (in_array($status, self::PRODUCT_APPROVED, true)) {
                $bucket = 'approved';
            } elseif (in_array($status, self::PRODUCT_REJECTED, true)) {
                $bucket = 'rejected';
            } else {
                $bucket = 'pending';
            }

            $stats['total'] += $count;
            if ($bucket === 'approved') {
                $stats['approved'] += $count;
            }

            if ($type = $this->typeKey($row->listing_type)) {
                $stats[$type]['total']  += $count;
                $stats[$type][$bucket]  += $count;
            }
        }

        $stats['approved_rate'] = $stats['total'] ? (int) round($stats['approved'] * 100 / $stats['total']) : 0;

        return $stats;
    }

    /**
     * Inquiries, product ke listing_type ke hisaab se alag:
     * received = mere products par aayi (product_inquiries.member_id), sent = maine bheji (user_id)
     */
    private function inquiryStats(int $userId): array
    {
        $blank = ['received' => 0, 'pending' => 0, 'contacted' => 0, 'closed' => 0, 'this_month' => 0, 'sent' => 0];
        $stats = ['received' => 0, 'pending' => 0, 'sent' => 0, 'have' => $blank, 'want' => $blank];

        $base = DB::table('product_inquiries as pi')
            ->leftJoin('products as p', 'p.id', '=', 'pi.product_id');
        if ($this->hasColumn('product_inquiries', 'deleted_at')) {
            $base->whereNull('pi.deleted_at');
        }

        // Received: type + status ke hisaab se
        $received = (clone $base)->where('pi.member_id', $userId)
            ->select('p.listing_type', 'pi.status', DB::raw('COUNT(*) as total'))
            ->groupBy('p.listing_type', 'pi.status')
            ->get();

        foreach ($received as $row) {
            $count  = (int) $row->total;
            $status = is_numeric($row->status) ? (int) $row->status : self::INQUIRY_PENDING;
            $bucket = match ($status) {
                self::INQUIRY_CONTACTED => 'contacted',
                self::INQUIRY_CLOSED    => 'closed',
                default                 => 'pending',
            };

            $stats['received'] += $count;
            if ($bucket === 'pending') {
                $stats['pending'] += $count;
            }

            if ($type = $this->typeKey($row->listing_type)) {
                $stats[$type]['received'] += $count;
                $stats[$type][$bucket]    += $count;
            }
        }

        // Is mahine aayi
        $thisMonth = (clone $base)->where('pi.member_id', $userId)
            ->where('pi.created_at', '>=', now()->startOfMonth()->toDateTimeString())
            ->select('p.listing_type', DB::raw('COUNT(*) as total'))
            ->groupBy('p.listing_type')
            ->get();

        foreach ($thisMonth as $row) {
            if ($type = $this->typeKey($row->listing_type)) {
                $stats[$type]['this_month'] += (int) $row->total;
            }
        }

        // Maine dusron ke products par bheji
        $sent = (clone $base)->where('pi.user_id', $userId)
            ->select('p.listing_type', DB::raw('COUNT(*) as total'))
            ->groupBy('p.listing_type')
            ->get();

        foreach ($sent as $row) {
            $stats['sent'] += (int) $row->total;
            if ($type = $this->typeKey($row->listing_type)) {
                $stats[$type]['sent'] += (int) $row->total;
            }
        }

        return $stats;
    }

    /**
     * Team / downline. users.sponsor_id me sponsor ka unique_id save hota hai (register flow ke hisaab se).
     * Level 1 = direct members, uske niche har level alag gina jata hai.
     */
    private function teamStats(?string $uniqueId): array
    {
        $stats = ['direct' => 0, 'direct_active' => 0, 'total' => 0, 'levels' => 0, 'this_month' => 0, 'below_direct' => 0];

        if (!$uniqueId) {
            return $stats;
        }

        $hasDeleted = $this->hasColumn('users', 'deleted_at');
        $monthStart = now()->startOfMonth()->toDateTimeString();
        $seen       = [$uniqueId => true];
        $current    = [$uniqueId];

        for ($level = 1; $level <= self::MAX_LEVELS && $current; $level++) {
            $next = [];

            foreach (array_chunk($current, 1000) as $chunk) {
                $query = DB::table('users')->whereIn('sponsor_id', $chunk)->whereNotNull('unique_id');
                if ($hasDeleted) {
                    $query->whereNull('deleted_at');
                }

                foreach ($query->get(['unique_id', 'status', 'created_at']) as $member) {
                    if (isset($seen[$member->unique_id])) {
                        continue; // circular sponsor data se infinite loop na bane
                    }
                    $seen[$member->unique_id] = true;
                    $next[] = $member->unique_id;

                    if ($level === 1) {
                        $stats['direct']++;
                        if ((string) $member->status === '1') {
                            $stats['direct_active']++;
                        }
                    }
                    if ($member->created_at && (string) $member->created_at >= $monthStart) {
                        $stats['this_month']++;
                    }
                }
            }

            if ($next) {
                $stats['total'] += count($next);
                $stats['levels'] = $level;
            }
            $current = $next;
        }

        $stats['below_direct'] = $stats['total'] - $stats['direct'];

        return $stats;
    }

    private function hasColumn(string $table, string $column): bool
    {
        static $cache = [];
        return $cache["$table.$column"] ??= Schema::hasColumn($table, $column);
    }
}
