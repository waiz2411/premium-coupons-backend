<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomPage;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = CustomPage::with('subpages')->where('slug', $slug)->firstOrFail();
        
        $couponIds = $page->selected_coupon_ids ?? [];
        $coupons = [];
        if (!empty($couponIds)) {
            $coupons = Coupon::whereIn('id', $couponIds)->with('store')->get();
        }

        $trendingProducts = Product::where('is_trending', true)->take(4)->get();

        $pageData = $page->toArray();
        $pageData['heroTitle'] = $pageData['hero_title'];
        $pageData['heroDescription'] = $pageData['hero_description'];
        $pageData['heroImage'] = $pageData['hero_image'];
        $pageData['selectedCouponIds'] = $pageData['selected_coupon_ids'];
        $pageData['isSubpage'] = $pageData['is_subpage'];
        $pageData['parentPageId'] = $pageData['parent_page_id'];
        
        unset(
            $pageData['hero_title'], $pageData['hero_description'], $pageData['hero_image'],
            $pageData['selected_coupon_ids'], $pageData['is_subpage'], $pageData['parent_page_id'], $pageData['subpages']
        );

        return response()->json([
            'page' => $pageData,
            'coupons' => $coupons,
            'subpages' => $page->subpages,
            'trendingProducts' => $trendingProducts
        ]);
    }
}
