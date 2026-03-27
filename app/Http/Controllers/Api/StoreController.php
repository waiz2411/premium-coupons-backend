<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['country' => 'required|string']);
        $country = strtolower($request->query('country'));
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 10);
        $sort = $request->query('sort');

        $query = Store::whereJsonContains('countries', $country);

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->has('category')) {
            $query->where('category_id', $request->query('category'));
        }
        if ($request->has('featured') && $request->featured === 'true') {
            $query->where('is_featured', true);
        }

        if ($sort === 'rating') {
            $query->orderBy('rating', 'desc');
        } elseif ($sort === 'name') {
            $query->orderBy('name', 'asc');
        } else {
            // default or couponCount sort
            $query->withCount(['coupons' => function ($q) use ($country) {
                $q->whereJsonContains('countries', $country);
            }]);
            if ($sort === 'couponCount') {
                $query->orderBy('coupons_count', 'desc');
            }
        }

        // Just mapping properties to camelCase on items. 
        // Need to load coupon count for instances if not loaded
        if ($sort !== 'couponCount') {
            $query->withCount(['coupons' => function ($q) use ($country) {
                $q->whereJsonContains('countries', $country);
            }]);
        }

        $paginator = $query->paginate($limit, ['*'], 'page', $page);
        
        $items = $paginator->getCollection()->map(function ($store) {
            $data = $store->toArray();
            $data['couponCount'] = $store->coupons_count;
            unset($data['coupons_count']);
            $data['categoryId'] = $data['category_id'];
            $data['isFeatured'] = $data['is_featured'];
            $data['affiliateLink'] = $data['affiliate_link'];
            return $data;
        });

        return response()->json([
            'stores' => $items,
            'total' => $paginator->total(),
            'page' => $paginator->currentPage(),
            'totalPages' => $paginator->lastPage(),
        ]);
    }

    public function show(Request $request, $id)
    {
        $request->validate(['country' => 'required|string']);
        $country = strtolower($request->query('country'));
        
        $store = Store::whereJsonContains('countries', $country)->findOrFail($id);
        
        $coupons = Coupon::where('store_id', $store->id)
            ->whereJsonContains('countries', $country)
            ->get();
            
        $products = Product::where('store_id', $store->id)
            ->whereJsonContains('countries', $country)
            ->get();
            
        $relatedStores = Store::where('category_id', $store->category_id)
            ->whereJsonContains('countries', $country)
            ->where('id', '!=', $store->id)
            ->take(4)
            ->get();

        $data = $store->toArray();
        $data['categoryId'] = $data['category_id'];
        $data['isFeatured'] = $data['is_featured'];
        $data['affiliateLink'] = $data['affiliate_link'];

        return response()->json([
            'store' => $data,
            'coupons' => $coupons,
            'products' => $products,
            'relatedStores' => $relatedStores
        ]);
    }
}
