<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Store;
use App\Models\EventLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalCoupons = Coupon::count();
        $activeStores = Store::count();
        $totalClicks = EventLog::whereIn('type', ['coupon_click', 'store_click', 'product_click'])->count();
        $pageViews = EventLog::where('type', 'page_view')->count();

        $recentCoupons = Coupon::with('store')->latest()->take(5)->get()->map(function($c) {
            return [
                'id' => $c->id,
                'title' => $c->title,
                'discount' => $c->discount,
                'successRate' => $c->success_rate,
                'store' => [
                    'name' => $c->store->name ?? '',
                    'logo' => $c->store->logo ?? ''
                ]
            ];
        });

        $topStores = Store::withCount('coupons')->orderBy('rating', 'desc')->take(5)->get()->map(function($s) {
            return [
                'id' => $s->id,
                'name' => $s->name,
                'logo' => $s->logo,
                'couponCount' => $s->coupons_count,
                'rating' => $s->rating
            ];
        });

        return response()->json([
            'totalCoupons' => $totalCoupons,
            'totalCouponsChange' => '+0%',
            'activeStores' => $activeStores,
            'activeStoresChange' => '+0%',
            'totalClicks' => $totalClicks,
            'totalClicksChange' => '+0%',
            'pageViews' => $pageViews,
            'pageViewsChange' => '+0%',
            'recentCoupons' => $recentCoupons,
            'topStores' => $topStores,
        ]);
    }
}
