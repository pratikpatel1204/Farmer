<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class MLMController extends Controller
{
    /**
     * Sirf zaroori columns (password / temp_password / otp load nahi hote).
     */
    private const COLUMNS = [
        'id',
        'full_name',
        'role',
        'unique_id',
        'sponsor_id',
        'email',
        'mobile',
        'status',
        'created_at',
    ];

    public function treeview()
    {
        $root = User::select(self::COLUMNS)->findOrFail(Auth::id());

        $members = $this->downline($root);
        $this->attachChildren($root, $members);

        $tree = $root->children;

        return view('member.network.tree', compact('root', 'tree', 'members'));
    }

    public function my_downline()
    {
        $root = User::select(self::COLUMNS)->findOrFail(Auth::id());

        $members = $this->downline($root);

        return view('member.network.my_downline', compact('members'));
    }

    /**
     * Logged-in member ke neeche ka poora network, level ke saath (1 = direct).
     * Ek level ek query me aata hai, isliye N+1 nahi hota.
     */
    private function downline(User $root, int $maxDepth = 50): Collection
    {
        $all = collect();
        $parentIds = [$root->unique_id];
        $seen = [$root->unique_id => true]; // loop / cycle se bachne ke liye
        $depth = 1;

        while (!empty($parentIds) && $depth <= $maxDepth) {
            $members = User::select(self::COLUMNS)
                ->whereIn('sponsor_id', $parentIds)
                ->orderBy('created_at')
                ->get()
                ->reject(fn($m) => isset($seen[$m->unique_id]))
                ->values();

            if ($members->isEmpty()) {
                break;
            }

            foreach ($members as $member) {
                $member->setAttribute('level', $depth);
                $seen[$member->unique_id] = true;
            }

            $all = $all->concat($members);
            $parentIds = $members->pluck('unique_id')->all();
            $depth++;
        }

        return $all;
    }

    /**
     * Flat list ko tree me badalta hai: har user par `children` relation set hota hai.
     */
    private function attachChildren(User $root, Collection $members): void
    {
        $byParent = $members->groupBy('sponsor_id');

        foreach ($members as $member) {
            $member->setRelation('children', $byParent->get($member->unique_id, collect()));
        }

        $root->setRelation('children', $byParent->get($root->unique_id, collect()));
    }
}
