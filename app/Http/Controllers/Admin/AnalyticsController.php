<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventLog;
use App\Models\Store;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function overview(Request $request)
    {
        // Basic implementation returning dummy shape for charts but real counts for stats.
        $totalClicks = EventLog::whereIn('type', ['coupon_click', 'store_click', 'product_click'])->count();
        $pageViews = EventLog::where('type', 'page_view')->count();
        $uniqueVisitors = EventLog::distinct('data->userAgent')->count('id');
        
        return response()->json([
            'totalClicks' => $totalClicks,
            'totalClicksChange' => '+0%', // Placeholder logic
            'pageViews' => $pageViews,
            'pageViewsChange' => '+0%',
            'uniqueVisitors' => $uniqueVisitors,
            'uniqueVisitorsChange' => '+0%',
            'conversionRate' => 0.0,
            'conversionRateChange' => '0%',
            'clicksOverTime' => [
                ['date' => Carbon::now()->format('M j'), 'clicks' => $totalClicks]
            ],
            'trafficByCategory' => [],
            'topStores' => Store::take(5)->get(['name'])->map(function($s) {
                return ['name' => $s->name, 'conversions' => 0];
            }),
            'recentActivity' => EventLog::latest()->take(5)->get()->map(function($log) {
                return [
                    'action' => 'Event: ' . $log->type,
                    'time' => $log->created_at->diffForHumans(),
                    'type' => 'click'
                ];
            })
        ]);
    }

    public function stores(Request $request)
    {
        return response()->json([
            'storeClicksOverTime' => [],
            'topPerformingStores' => []
        ]);
    }

    public function coupons(Request $request)
    {
        return response()->json([
            'couponUsesOverTime' => [],
            'mostUsedCoupons' => []
        ]);
    }

    public function products(Request $request)
    {
        return response()->json([
            'productViewsOverTime' => [],
            'topViewedProducts' => []
        ]);
    }
}
