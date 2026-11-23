<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Member dashboard ke numbers (website Member\DashboardMemberController jaisa hi hisaab).
 * App API ke /me/dashboard aur /me/team yahin se.
 */
class MemberStats
{
    private const PRODUCT_APPROVED = ['approved', 'approve', 'active', '1'];
    private const PRODUCT_REJECTED = ['rejected', 'reject', 'declined', '2'];
    private const MAX_LEVELS = 50;

    /** total / approved / pending / rejected / completed + have / want */
    public static function products(int $userId): array
    {
        $stats = ['total' => 0, 'approved' => 0, 'pending' => 0, 'rejected' => 0, 'completed' => 0, 'have' => 0, 'want' => 0];

        $query = DB::table('products')->where('member_id', $userId);
        if (self::hasColumn('products', 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        foreach ((clone $query)->select('status', DB::raw('COUNT(*) as total'))->groupBy('status')->get() as $row) {
            $status = strtolower(trim((string) $row->status));
            $count  = (int) $row->total;

            $stats['total'] += $count;
            if (in_array($status, self::PRODUCT_APPROVED, true)) {
                $stats['approved'] += $count;
            } elseif (in_array($status, self::PRODUCT_REJECTED, true)) {
                $stats['rejected'] += $count;
            } else {
                $stats['pending'] += $count;
            }
        }

        if (self::hasColumn('products', 'completed_at')) {
            $stats['completed'] = (clone $query)->whereNotNull('completed_at')->count();
        }

        foreach ((clone $query)->select('listing_type', DB::raw('COUNT(*) as total'))->groupBy('listing_type')->pluck('total', 'listing_type') as $type => $count) {
            $type = strtolower(trim((string) $type));
            if (in_array($type, ['have', 'want'], true)) {
                $stats[$type] += (int) $count;
            }
        }

        $stats['approved_rate'] = $stats['total'] ? (int) round($stats['approved'] * 100 / $stats['total']) : 0;

        return $stats;
    }

    /** received = mere products par aayi, sent = maine bheji. status 0 Pending, 1 Contacted, 2 Closed */
    public static function inquiries(int $userId): array
    {
        $base = DB::table('product_inquiries');
        if (self::hasColumn('product_inquiries', 'deleted_at')) {
            $base->whereNull('deleted_at');
        }

        $received = (clone $base)->where('member_id', $userId);
        $byStatus = (clone $received)->select('status', DB::raw('COUNT(*) as total'))->groupBy('status')->pluck('total', 'status');

        $stats = ['received' => 0, 'pending' => 0, 'contacted' => 0, 'closed' => 0];
        foreach ($byStatus as $status => $count) {
            $bucket = match (is_numeric($status) ? (int) $status : 0) {
                1       => 'contacted',
                2       => 'closed',
                default => 'pending',
            };
            $stats[$bucket]   += (int) $count;
            $stats['received'] += (int) $count;
        }

        $stats['this_month'] = (clone $received)->where('created_at', '>=', now()->startOfMonth()->toDateTimeString())->count();
        $stats['sent']       = (clone $base)->where('user_id', $userId)->count();

        return $stats;
    }

    /**
     * Team / downline: users.sponsor_id = sponsor ka unique_id.
     * Return: stats + har level ka count + (chahiye to) ek level ke members.
     */
    public static function team(?string $uniqueId, ?int $wantLevel = null): array
    {
        $stats   = ['direct' => 0, 'direct_active' => 0, 'below_direct' => 0, 'total' => 0, 'levels' => 0, 'this_month' => 0];
        $levels  = [];
        $members = [];

        if (! $uniqueId) {
            return compact('stats', 'levels', 'members');
        }

        $hasDeleted = self::hasColumn('users', 'deleted_at');
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

                foreach ($query->orderByDesc('id')->get(['unique_id', 'full_name', 'role', 'status', 'created_at', 'sponsor_id']) as $member) {
                    if (isset($seen[$member->unique_id])) {
                        continue; // circular data se loop na bane
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
                    if ($wantLevel === $level) {
                        $members[] = [
                            'unique_id'  => $member->unique_id,
                            'name'       => $member->full_name,
                            'role'       => $member->role,
                            'sponsor_id' => $member->sponsor_id,
                            'active'     => (string) $member->status === '1',
                            'joined_at'  => ApiData::date($member->created_at),
                        ];
                    }
                }
            }

            if ($next) {
                $stats['total'] += count($next);
                $stats['levels'] = $level;
                $levels[] = ['level' => $level, 'count' => count($next)];
            }
            $current = $next;
        }

        $stats['below_direct'] = $stats['total'] - $stats['direct'];

        return compact('stats', 'levels', 'members');
    }

    private static function hasColumn(string $table, string $column): bool
    {
        static $cache = [];

        return $cache["$table.$column"] ??= Schema::hasColumn($table, $column);
    }
}
