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
        $page    = $request->query('page', 1);
        $limit   = $request->query('limit', 10);
        $sort    = $request->query('sort');

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
            $query->withCount(['coupons' => function ($q) use ($country) {
                $q->whereJsonContains('countries', $country);
            }]);
            if ($sort === 'couponCount') {
                $query->orderBy('coupons_count', 'desc');
            }
        }

        // Always load coupon count for all sort modes
        if ($sort !== 'couponCount' && $sort !== 'rating' && $sort !== 'name') {
            // already loaded above
        } elseif ($sort === 'rating' || $sort === 'name') {
            $query->withCount(['coupons' => function ($q) use ($country) {
                $q->whereJsonContains('countries', $country);
            }]);
        }

        $paginator = $query->paginate($limit, ['*'], 'page', $page);

        $items = $paginator->getCollection()->map(function ($store) {
            $data = $store->toArray();
            $data['slug']         = $store->slug ?? null;
            $data['couponCount']  = $store->coupons_count ?? 0;
            $data['categoryId']   = $data['category_id'];
            $data['isFeatured']   = (bool)$data['is_featured'];
            $data['affiliateLink'] = $data['affiliate_link'];
            unset($data['coupons_count'], $data['category_id'], $data['is_featured'], $data['affiliate_link']);
            return $data;
        });

        return response()->json([
            'stores'     => $items,
            'total'      => $paginator->total(),
            'page'       => $paginator->currentPage(),
            'totalPages' => $paginator->lastPage(),
        ]);
    }

    public function show(Request $request, $id)
    {
        $request->validate(['country' => 'required|string']);
        $country = strtolower($request->query('country'));

        $store = Store::whereJsonContains('countries', $country)
            ->where(function($q) use ($id) {
                $q->where('id', $id)->orWhere('slug', $id);
            })
            ->firstOrFail();

        $coupons = Coupon::where('store_id', $store->id)
            ->whereJsonContains('countries', $country)
            ->get()
            ->map(function ($c) {
                $item = $c->toArray();
                $item['storeId']     = $item['store_id'];
                $item['categoryId']  = $item['category_id'];
                $item['redirectUrl'] = $item['redirect_url'];
                $item['isDeal']      = (bool)$item['is_deal'];
                $item['expiryDate']  = $item['expiry_date'];
                $item['successRate'] = $item['success_rate'];
                $item['isVerified']  = (bool)$item['is_verified'];
                $item['isTrending']  = (bool)$item['is_trending'];
                $item['isNew']       = (bool)$item['is_new'];
                return $item;
            });

        $products = Product::where('store_id', $store->id)
            ->whereJsonContains('countries', $country)
            ->get()
            ->map(function ($p) {
                $item = $p->toArray();
                $item['storeId']      = $item['store_id'];
                $item['categoryId']   = $item['category_id'];
                $item['affiliateUrl'] = $item['affiliate_url'];
                $item['imageUrl']     = $item['image_url'];
                $item['isTrending']   = (bool)$item['is_trending'];
                return $item;
            });

        $relatedStores = Store::where('category_id', $store->category_id)
            ->whereJsonContains('countries', $country)
            ->where('id', '!=', $store->id)
            ->take(4)
            ->get()
            ->map(function ($s) {
                $item = $s->toArray();
                $item['slug']         = $s->slug ?? null;
                $item['categoryId']   = $item['category_id'];
                $item['isFeatured']   = (bool)$item['is_featured'];
                $item['affiliateLink'] = $item['affiliate_link'];
                return $item;
            });

        $data = $store->toArray();
        $data['slug']          = $store->slug ?? null;
        $data['categoryId']    = $data['category_id'];
        $data['isFeatured']    = (bool)$data['is_featured'];
        $data['affiliateLink'] = $data['affiliate_link'];

        return response()->json([
            'store'        => $data,
            'coupons'      => $coupons,
            'products'     => $products,
            'relatedStores' => $relatedStores,
        ]);
    }
}
