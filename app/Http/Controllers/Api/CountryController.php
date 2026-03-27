<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Store;
use App\Models\Coupon;
use App\Models\Category;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::select('code', 'name')->get();
        return response()->json($countries);
    }

    public function show($code)
    {
        $code = strtolower($code);
        $country = Country::where('code', $code)->first();

        // Admin might not have saved settings for this country yet, fallback to empties
        $data = $country ? $country->toArray() : [
            'code' => $code,
            'name' => strtoupper($code),
            'hero_images' => [],
            'featured_store_ids' => [],
            'trending_coupon_ids' => []
        ];

        $data['heroImages'] = $data['hero_images'] ?? [];
        unset($data['hero_images'], $data['featured_store_ids'], $data['trending_coupon_ids']);

        $featuredStoreIds = $country ? ($country->featured_store_ids ?? []) : [];
        $trendingCouponIds = $country ? ($country->trending_coupon_ids ?? []) : [];

        $featuredStores = !empty($featuredStoreIds) 
            ? Store::whereIn('id', $featuredStoreIds)->get() 
            : [];
            
        $trendingCoupons = !empty($trendingCouponIds)
            ? Coupon::whereIn('id', $trendingCouponIds)->with('store')->get()
            : [];

        $allStores = Store::whereJsonContains('countries', $code)->get();

        $categories = Category::withCount(['coupons' => function ($q) use ($code) {
            $q->whereJsonContains('countries', $code);
        }])->get()->map(function ($cat) {
            $catData = $cat->toArray();
            $catData['couponCount'] = $cat->coupons_count;
            unset($catData['coupons_count']);
            $catData['imageUrl'] = $catData['image_url'];
            unset($catData['image_url']);
            return $catData;
        });

        return response()->json([
            'country' => $data,
            'heroImages' => $data['heroImages'],
            'featuredStores' => $featuredStores,
            'trendingCoupons' => $trendingCoupons,
            'stores' => $allStores,
            'categories' => $categories
        ]);
    }
}
