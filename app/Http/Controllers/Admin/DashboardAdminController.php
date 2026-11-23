<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardAdminController extends Controller
{
    /** products.status ki values (lowercase me match hoti hain). DB me alag value ho to yahan add karo. */
    private const PRODUCT_APPROVED = ['approved', 'approve', 'active', '1'];
    private const PRODUCT_REJECTED = ['rejected', 'reject', 'declined', '2'];
    // Baaki sab (pending, 0, null, empty) Pending me count hota hai.

    /** product_inquiries.status aur contact_inquiries.status: 0 = Pending, 1 = Contacted, 2 = Closed */
    private const INQUIRY_PENDING   = 0;
    private const INQUIRY_CONTACTED = 1;
    private const INQUIRY_CLOSED    = 2;

    public function admin_dashboard()
    {
        $today      = now()->startOfDay()->toDateTimeString();
        $weekStart  = now()->startOfWeek()->toDateTimeString();
        $monthStart = now()->startOfMonth()->toDateTimeString();

        $productStats = $this->productStats($monthStart);
        $inquiryStats = $this->inquiryStats($today, $monthStart);
        $memberStats  = $this->memberStats($monthStart);
        $profileStats = $this->profileStats($memberStats['total']);
        $contactStats = $this->contactStats($today, $weekStart, $monthStart);

        return view('admin.dashboard', compact('productStats', 'inquiryStats', 'memberStats', 'profileStats', 'contactStats'));
    }

    /** Sab members ke products: total / approved / pending / rejected */
    private function productStats(string $monthStart): array
    {
        $stats = ['total' => 0, 'approved' => 0, 'pending' => 0, 'rejected' => 0, 'have' => 0, 'want' => 0, 'this_month' => 0];

        $query = $this->table('products');
        if (!$query) {
            return $stats;
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

        if ($this->hasColumn('products', 'listing_type')) {
            $types = (clone $query)->select('listing_type', DB::raw('COUNT(*) as total'))->groupBy('listing_type')->pluck('total', 'listing_type');
            foreach ($types as $type => $count) {
                $type = strtolower(trim((string) $type));
                if (str_contains($type, 'want')) {
                    $stats['want'] += (int) $count;
                } elseif (str_contains($type, 'have')) {
                    $stats['have'] += (int) $count;
                }
            }
        }

        $stats['this_month'] = (clone $query)->where('created_at', '>=', $monthStart)->count();

        return $stats;
    }

    /** Inquiries status ke hisaab se: Pending (0) / Contacted (1) / Closed (2) */
    private function inquiryStats(string $today, string $monthStart): array
    {
        $stats = ['total' => 0, 'pending' => 0, 'contacted' => 0, 'closed' => 0, 'this_month' => 0, 'today' => 0];

        $query = $this->table('product_inquiries');
        if (!$query) {
            return $stats;
        }

        $byStatus = (clone $query)->select('status', DB::raw('COUNT(*) as total'))->groupBy('status')->pluck('total', 'status');

        $stats['total']      = (int) $byStatus->sum();
        $stats['pending']    = (int) ($byStatus[self::INQUIRY_PENDING] ?? 0);
        $stats['contacted']  = (int) ($byStatus[self::INQUIRY_CONTACTED] ?? 0);
        $stats['closed']     = (int) ($byStatus[self::INQUIRY_CLOSED] ?? 0);
        $stats['this_month'] = (clone $query)->where('created_at', '>=', $monthStart)->count();
        $stats['today']      = (clone $query)->where('created_at', '>=', $today)->count();

        return $stats;
    }

    /** Members = Admin ko chhod kar sab users (users.role: Farmer / Non Farmer) */
    private function memberStats(string $monthStart): array
    {
        $stats = ['total' => 0, 'farmers' => 0, 'non_farmers' => 0, 'active' => 0, 'this_month' => 0];

        $query = $this->table('users');
        if (!$query) {
            return $stats;
        }
        $query->where(fn ($q) => $q->whereNull('role')->orWhere('role', '!=', 'Admin'));

        $stats['total']       = (clone $query)->count();
        $stats['farmers']     = (clone $query)->where('role', 'Farmer')->count();
        $stats['non_farmers'] = (clone $query)->where('role', 'Non Farmer')->count();
        $stats['active']      = (clone $query)->where('status', 1)->count();
        $stats['this_month']  = (clone $query)->where('created_at', '>=', $monthStart)->count();

        return $stats;
    }

    /** Business profiles: profiles.status 'Approved' / 'Pending' */
    private function profileStats(int $members): array
    {
        $stats = ['total' => 0, 'approved' => 0, 'pending' => 0, 'without' => 0];

        $query = $this->table('profiles');
        if (!$query) {
            return $stats;
        }

        $stats['total']    = (clone $query)->count();
        $stats['approved'] = (clone $query)->where('status', 'Approved')->count();
        $stats['pending']  = $stats['total'] - $stats['approved'];
        $stats['without']  = max(0, $members - $stats['total']);

        return $stats;
    }

    /** Contact Us form ke messages (contact_inquiries table, status 0/1/2 product inquiries jaisa) */
    private function contactStats(string $today, string $weekStart, string $monthStart): array
    {
        $stats = ['total' => 0, 'pending' => 0, 'contacted' => 0, 'closed' => 0, 'today' => 0, 'this_week' => 0, 'this_month' => 0];

        $query = $this->table('contact_inquiries');
        if (!$query) {
            return $stats;
        }

        $byStatus = (clone $query)->select('status', DB::raw('COUNT(*) as total'))->groupBy('status')->pluck('total', 'status');

        $stats['total']      = (int) $byStatus->sum();
        $stats['pending']    = (int) ($byStatus[self::INQUIRY_PENDING] ?? 0);
        $stats['contacted']  = (int) ($byStatus[self::INQUIRY_CONTACTED] ?? 0);
        $stats['closed']     = (int) ($byStatus[self::INQUIRY_CLOSED] ?? 0);
        $stats['today']      = (clone $query)->where('created_at', '>=', $today)->count();
        $stats['this_week']  = (clone $query)->where('created_at', '>=', $weekStart)->count();
        $stats['this_month'] = (clone $query)->where('created_at', '>=', $monthStart)->count();

        return $stats;
    }

    /** Table na ho to null, deleted_at ho to deleted rows skip */
    private function table(string $table)
    {
        if (!Schema::hasTable($table)) {
            return null;
        }

        $query = DB::table($table);
        if ($this->hasColumn($table, 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        return $query;
    }

    private function hasColumn(string $table, string $column): bool
    {
        static $cache = [];
        return $cache["$table.$column"] ??= Schema::hasColumn($table, $column);
    }
}