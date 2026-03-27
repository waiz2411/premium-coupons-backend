<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Store;
use App\Models\Coupon;
use App\Models\Category;
use App\Models\Product;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['country' => 'required|string']);
        $countryCode = strtolower($request->query('country'));

        $country = Country::where('code', $countryCode)->first();

        // 1. Hero Images from country settings
        $heroImages = $country ? ($country->hero_images ?? []) : [];

        // 2. Featured Stores
        $featuredStores = Store::where('is_featured', true)
            ->whereJsonContains('countries', $countryCode)
            ->get()->map(function($s) {
                // Return camelCase matching docs or general usage
                $data = $s->toArray();
                $data['isFeatured'] = $s->is_featured;
                return $data;
            });

        // 3. Trending Coupons
        $trendingCoupons = Coupon::with('store')
            ->where('is_trending', true)
            ->whereJsonContains('countries', $countryCode)
            ->get();

        // 4. New Coupons
        $newCoupons = Coupon::with('store')
            ->where('is_new', true)
            ->whereJsonContains('countries', $countryCode)
            ->get();

        // 5. Categories
        $categories = Category::withCount(['coupons' => function ($q) use ($countryCode) {
            $q->whereJsonContains('countries', $countryCode);
        }])->get()->map(function($c) {
            $data = $c->toArray();
            $data['couponCount'] = $c->coupons_count;
            $data['imageUrl'] = $c->image_url;
            return $data;
        });

        // 6. Trending Products
        $trendingProducts = Product::where('is_trending', true)
            ->whereJsonContains('countries', $countryCode)
            ->get();

        // 7. Latest Blog Posts
        $blogPosts = BlogPost::orderBy('date', 'desc')->take(3)->get()->map(function($b) {
            $data = $b->toArray();
            $data['readTime'] = $b->read_time;
            return $data;
        });

        return response()->json([
            'heroImages' => $heroImages,
            'featuredStores' => $featuredStores,
            'trendingCoupons' => $trendingCoupons,
            'newCoupons' => $newCoupons,
            'categories' => $categories,
            'trendingProducts' => $trendingProducts,
            'blogPosts' => $blogPosts
        ]);
    }
}
