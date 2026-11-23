<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Blog;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index()
    {
        $banners      = Banner::where('status', 'Active')->latest('id')->get();
        $wantProducts = $this->productsByType('want');
        $haveProducts = $this->productsByType('have');
        $profiles     = $this->profiles();
        $blogs        = $this->blogs();

        return view('front.home', compact('banners', 'wantProducts', 'haveProducts', 'profiles', 'blogs'));
    }

    private function productsByType(string $type)
    {
        return Product::with('member')->where('status', 'Approved')->whereNull('products.completed_at')->where('listing_type', $type)
            ->whereHas('member', function ($query) {
                $query->where('status', 1)->whereNull('deleted_at');
            })->inRandomOrder()->get();
    }

    private function profiles()
    {
        return User::with(['profile', 'states', 'citys'])
            ->whereIn('role', ['Farmer', 'Non Farmer'])
            ->where('users.status', 1)
            ->whereNull('users.deleted_at')
            ->whereHas('profile', function ($query) {
                $query->where('status', 'Approved');
            })->inRandomOrder()->get();
    }

    private function blogs(int $limit = 4)
    {
        if (! Schema::hasTable('blogs')) {
            return collect();
        }
        return Blog::published()->inRandomOrder()->limit($limit)->get();
    }
}
