<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MemberTreeController extends Controller
{
    private const MEMBER_COLUMNS = [
        'id',
        'full_name',
        'role',
        'unique_id',
        'sponsor_id',
        'sponsor_name',
        'email',
        'mobile',
        'status',
        'created_at',
    ];

    private const PER_PAGE = 10;

    /* ===================== DOWNLINE ===================== */

    public function downline(?string $uniqueId = null)
    {
        $root = $this->resolveRoot($uniqueId);
        abort_if(!$root, 404, 'Member not found');

        return view('admin.network.downline', compact('root'));
    }

    public function downlineTable(Request $request)
    {
        $root = $this->resolveRoot($request->input('unique_id'));
        if (!$root) {
            return response()->json(['message' => 'Member not found'], 404);
        }

        $members  = $this->collectDownline($root);
        $counts   = $this->counts($members);
        $maxLevel = (int) ($members->max('level') ?? 0);

        // Filters
        $search = trim((string) $request->input('search', ''));
        $level  = $request->input('level');
        $role   = $request->input('role');

        $filtered = $members
            ->when($level !== null && $level !== '', fn($c) => $c->where('level', (int) $level))
            ->when(!empty($role), fn($c) => $c->where('role', $role))
            ->when($search !== '', function ($c) use ($search) {
                return $c->filter(function ($m) use ($search) {
                    foreach (['full_name', 'unique_id', 'mobile', 'email', 'sponsor_id', 'sponsor_name'] as $field) {
                        if ($m->{$field} && stripos((string) $m->{$field}, $search) !== false) {
                            return true;
                        }
                    }
                    return false;
                });
            })
            ->values();

        $page    = max(1, (int) $request->input('page', 1));
        $members = new LengthAwarePaginator(
            $filtered->forPage($page, self::PER_PAGE)->values(),
            $filtered->count(),
            self::PER_PAGE,
            $page
        );

        return response()->json([
            'html'      => view('admin.network.downline_table', compact('members'))->render(),
            'counts'    => $counts,
            'max_level' => $maxLevel,
        ]);
    }

    /* ===================== TREE ===================== */

    public function tree(?string $uniqueId = null)
    {
        $root = $this->resolveRoot($uniqueId);
        abort_if(!$root, 404, 'Member not found');

        return view('admin.network.tree', compact('root'));
    }

    public function treeTable(Request $request)
    {
        $root = $this->resolveRoot($request->input('unique_id'));
        if (!$root) {
            return response()->json(['message' => 'Member not found'], 404);
        }

        $root->level = 0;
        $members     = $this->collectDownline($root);
        $childrenMap = $members->groupBy('sponsor_id');

        return response()->json([
            'html'   => view('admin.network.tree_table', compact('root', 'childrenMap'))->render(),
            'counts' => $this->counts($members),
        ]);
    }

    /* ===================== HELPERS ===================== */

    /**
     * Given unique_id ka member, warna Admin (top of tree).
     */
    private function resolveRoot(?string $uniqueId): ?User
    {
        $query = User::select(self::MEMBER_COLUMNS);

        if ($uniqueId) {
            return $query->where('unique_id', $uniqueId)->first();
        }

        return $query->where('role', 'Admin')->orderBy('id')->first();
    }

    /**
     * Level-wise (BFS) poori downline - har level ke liye ek hi query, N+1 nahi.
     * Har member par ->level set hota hai (root ke direct = 1).
     */
    private function collectDownline(User $root): Collection
    {
        $members = collect();
        $visited = [$root->unique_id => true];
        $current = [$root->unique_id];
        $level   = 0;

        while (!empty($current)) {
            $level++;
            $next = [];

            foreach (array_chunk($current, 1000) as $chunk) {
                $children = User::select(self::MEMBER_COLUMNS)
                    ->whereIn('sponsor_id', $chunk)
                    ->orderBy('id')
                    ->get();

                foreach ($children as $child) {
                    if (isset($visited[$child->unique_id])) {
                        continue; // galat data me loop ho to infinite loop se bachata hai
                    }
                    $visited[$child->unique_id] = true;
                    $child->level = $level;
                    $members->push($child);
                    $next[] = $child->unique_id;
                }
            }

            $current = $next;
        }

        return $members;
    }

    private function counts(Collection $members): array
    {
        return [
            'total'      => $members->count(),
            'non_farmer' => $members->where('role', 'Non Farmer')->count(),
            'farmer'     => $members->where('role', 'Farmer')->count(),
        ];
    }
}
