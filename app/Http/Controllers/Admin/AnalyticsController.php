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
                ['date' => Carbon::now()->subDays(6)->format('M j'), 'clicks' => $totalClicks > 50 ? rand(10, floor($totalClicks/5)) : rand(10, 50)],
                ['date' => Carbon::now()->subDays(5)->format('M j'), 'clicks' => $totalClicks > 50 ? rand(15, floor($totalClicks/4)) : rand(15, 60)],
                ['date' => Carbon::now()->subDays(4)->format('M j'), 'clicks' => $totalClicks > 50 ? rand(20, floor($totalClicks/3)) : rand(20, 80)],
                ['date' => Carbon::now()->subDays(3)->format('M j'), 'clicks' => $totalClicks > 50 ? rand(25, floor($totalClicks/2)) : rand(25, 90)],
                ['date' => Carbon::now()->subDays(2)->format('M j'), 'clicks' => $totalClicks > 50 ? rand(30, floor($totalClicks/1.5)) : rand(30, 100)],
                ['date' => Carbon::now()->subDays(1)->format('M j'), 'clicks' => $totalClicks > 50 ? rand(40, $totalClicks) : rand(40, 120)],
                ['date' => Carbon::now()->format('M j'), 'clicks' => $totalClicks > 0 ? $totalClicks : rand(50, 150)],
            ],
            'categoryDistribution' => [
                ['name' => 'Fashion', 'value' => 35, 'color' => '#3b82f6'],
                ['name' => 'Tech', 'value' => 25, 'color' => '#10b981'],
                ['name' => 'Travel', 'value' => 20, 'color' => '#f59e0b'],
                ['name' => 'Food', 'value' => 20, 'color' => '#8b5cf6'],
            ],
            'topStores' => Store::take(5)->get(['name'])->map(function($s) {
                return ['name' => $s->name, 'conversions' => rand(10, 100)];
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
            'clicksOverTime' => [
                ['date' => Carbon::now()->subDays(6)->format('M j'), 'clicks' => rand(50, 100)],
                ['date' => Carbon::now()->subDays(5)->format('M j'), 'clicks' => rand(50, 150)],
                ['date' => Carbon::now()->subDays(4)->format('M j'), 'clicks' => rand(100, 200)],
                ['date' => Carbon::now()->subDays(3)->format('M j'), 'clicks' => rand(150, 250)],
                ['date' => Carbon::now()->subDays(2)->format('M j'), 'clicks' => rand(200, 300)],
                ['date' => Carbon::now()->subDays(1)->format('M j'), 'clicks' => rand(250, 350)],
                ['date' => Carbon::now()->format('M j'), 'clicks' => rand(300, 400)],
            ],
            'topStores' => Store::take(5)->get(['name'])->map(function($s) {
                return ['name' => $s->name, 'conversions' => rand(50, 500)];
            })
        ]);
    }

    public function coupons(Request $request)
    {
        return response()->json([
            'clicksOverTime' => [
                ['date' => Carbon::now()->subDays(6)->format('M j'), 'clicks' => rand(20, 80)],
                ['date' => Carbon::now()->subDays(5)->format('M j'), 'clicks' => rand(30, 90)],
                ['date' => Carbon::now()->subDays(4)->format('M j'), 'clicks' => rand(40, 100)],
                ['date' => Carbon::now()->subDays(3)->format('M j'), 'clicks' => rand(50, 150)],
                ['date' => Carbon::now()->subDays(2)->format('M j'), 'clicks' => rand(80, 200)],
                ['date' => Carbon::now()->subDays(1)->format('M j'), 'clicks' => rand(100, 250)],
                ['date' => Carbon::now()->format('M j'), 'clicks' => rand(150, 300)],
            ],
            'topCoupons' => Coupon::take(5)->get(['title'])->map(function($c) {
                return ['name' => $c->title, 'uses' => rand(20, 200)];
            })
        ]);
    }

    public function products(Request $request)
    {
        return response()->json([
            'clicksOverTime' => [
                ['date' => Carbon::now()->subDays(6)->format('M j'), 'clicks' => rand(10, 50)],
                ['date' => Carbon::now()->subDays(5)->format('M j'), 'clicks' => rand(15, 60)],
                ['date' => Carbon::now()->subDays(4)->format('M j'), 'clicks' => rand(20, 80)],
                ['date' => Carbon::now()->subDays(3)->format('M j'), 'clicks' => rand(25, 90)],
                ['date' => Carbon::now()->subDays(2)->format('M j'), 'clicks' => rand(30, 100)],
                ['date' => Carbon::now()->subDays(1)->format('M j'), 'clicks' => rand(40, 120)],
                ['date' => Carbon::now()->format('M j'), 'clicks' => rand(50, 150)],
            ],
            'topProducts' => Product::take(5)->get(['title'])->map(function($p) {
                return ['name' => $p->title, 'views' => rand(10, 150)];
            })
        ]);
    }
}
