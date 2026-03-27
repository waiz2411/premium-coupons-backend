<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\EventLog;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['country' => 'required|string']);
        $country = strtolower($request->query('country'));
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 10);
        $sort = $request->query('sort');

        $query = Coupon::whereJsonContains('countries', $country)->with('store');

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->has('category')) {
            $query->where('category_id', $request->query('category'));
        }
        if ($request->has('storeId')) {
            $query->where('store_id', $request->query('storeId'));
        }
        if ($request->has('trending') && $request->trending === 'true') {
            $query->where('is_trending', true);
        }
        if ($request->has('verified') && $request->verified === 'true') {
            $query->where('is_verified', true);
        }
        if ($request->has('isNew') && $request->isNew === 'true') {
            $query->where('is_new', true);
        }
        if ($request->has('isDeal')) {
            $isDeal = $request->isDeal === 'true';
            $query->where('is_deal', $isDeal);
        }

        if ($sort === 'newest') {
            $query->orderBy('created_at', 'desc');
        } elseif ($sort === 'expiring') {
            $query->orderBy('expiry_date', 'asc');
        } elseif ($sort === 'successRate') {
            $query->orderBy('success_rate', 'desc');
        }

        $paginator = $query->paginate($limit, ['*'], 'page', $page);
        
        $items = $paginator->getCollection()->map(function ($c) {
            $item = $c->toArray();
            $item['storeId'] = $item['store_id'];
            $item['categoryId'] = $item['category_id'];
            $item['redirectUrl'] = $item['redirect_url'];
            $item['isDeal'] = $item['is_deal'];
            $item['expiryDate'] = $item['expiry_date'];
            $item['successRate'] = $item['success_rate'];
            $item['isVerified'] = $item['is_verified'];
            $item['isTrending'] = $item['is_trending'];
            $item['isNew'] = $item['is_new'];
            return $item;
        });

        return response()->json([
            'coupons' => $items,
            'total' => $paginator->total(),
            'page' => $paginator->currentPage(),
            'totalPages' => $paginator->lastPage(),
        ]);
    }

    public function show($id)
    {
        $coupon = Coupon::with('store')->findOrFail($id);
        
        $item = $coupon->toArray();
        $item['storeId'] = $item['store_id'];
        $item['categoryId'] = $item['category_id'];
        $item['redirectUrl'] = $item['redirect_url'];
        $item['isDeal'] = $item['is_deal'];
        $item['expiryDate'] = $item['expiry_date'];
        $item['successRate'] = $item['success_rate'];
        $item['isVerified'] = $item['is_verified'];
        $item['isTrending'] = $item['is_trending'];
        $item['isNew'] = $item['is_new'];
            
        return response()->json($item);
    }

    public function click(Request $request, $id)
    {
        $request->validate(['country' => 'required|string']);
        
        EventLog::create([
            'type' => 'coupon_click',
            'action' => 'click',
            'country_code' => strtolower($request->country),
            'data' => collect(['coupon_id' => $id])->toArray()
        ]);

        return response()->json(['message' => 'Event tracked'], 200);
    }
}
