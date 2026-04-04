<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\EventLog;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['country' => 'required|string']);
        $country = strtolower($request->query('country'));
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 10);

        $query = Product::whereJsonContains('countries', $country);

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

        $paginator = $query->paginate($limit, ['*'], 'page', $page);
        
        $items = $paginator->getCollection()->map(function ($p) {
            $item = $p->toArray();
            $item['storeId'] = $item['store_id'];
            $item['categoryId'] = $item['category_id'];
            $item['affiliateUrl'] = $item['affiliate_url'];
            $item['imageUrl'] = $item['image_url'];
            $item['isTrending'] = $item['is_trending'];
            $item['originalPrice'] = $item['original_price'];
            $item['discountedPrice'] = $item['discounted_price'];
            return $item;
        });

        return response()->json([
            'products' => $items,
            'total' => $paginator->total(),
            'page' => $paginator->currentPage(),
            'totalPages' => $paginator->lastPage(),
        ]);
    }

    public function show(Request $request, $id)
    {
        $request->validate(['country' => 'required|string']);
        $country = strtolower($request->query('country'));

        $product = Product::with(['store', 'category'])->findOrFail($id);
        
        $relatedProducts = Product::where('store_id', $product->store_id)
            ->whereJsonContains('countries', $country)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        $storeCoupons = Coupon::where('store_id', $product->store_id)
            ->whereJsonContains('countries', $country)
            ->get();
            
        $item = $product->toArray();
        $item['storeId'] = $item['store_id'];
        $item['categoryId'] = $item['category_id'];
        $item['affiliateUrl'] = $item['affiliate_url'];
        $item['imageUrl'] = $item['image_url'];
        $item['isTrending'] = $item['is_trending'];
        $item['originalPrice'] = $item['original_price'];
        $item['discountedPrice'] = $item['discounted_price'];
            
        return response()->json([
            'product' => $item,
            'relatedProducts' => $relatedProducts,
            'storeCoupons' => $storeCoupons
        ]);
    }

    public function click(Request $request, $id)
    {
        $request->validate(['country' => 'required|string']);
        
        EventLog::create([
            'type' => 'product_click',
            'action' => 'click',
            'country_code' => strtolower($request->country),
            'data' => collect(['product_id' => $id])->toArray()
        ]);

        return response()->json(['message' => 'Event tracked'], 200);
    }
}
