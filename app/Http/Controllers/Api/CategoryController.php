<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Store;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $country = $request->query('country');
        $query = Category::query();

        if ($country) {
            $query->withCount(['coupons' => function ($q) use ($country) {
                // MySQL JSON contains exact country string in array
                $q->whereJsonContains('countries', strtolower($country));
            }]);
        } else {
            $query->withCount('coupons');
        }

        $categories = $query->get()->map(function ($cat) {
            $data = $cat->toArray();
            $data['couponCount'] = $cat->coupons_count;
            unset($data['coupons_count']);
            // Convert to camelCase
            $data['imageUrl'] = $data['image_url'];
            unset($data['image_url']);
            return $data;
        });

        return response()->json($categories);
    }

    public function show(Request $request, $id)
    {
        $request->validate(['country' => 'required|string']);
        $country = strtolower($request->query('country'));
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 10);

        $category = Category::findOrFail($id);
        
        $coupons = Coupon::where('category_id', $category->id)
            ->whereJsonContains('countries', $country)
            ->with('store')
            ->paginate($limit, ['*'], 'page', $page);

        $stores = Store::where('category_id', $category->id)
            ->whereJsonContains('countries', $country)
            ->get();

        $catData = $category->toArray();
        $catData['imageUrl'] = $catData['image_url'];
        unset($catData['image_url']);

        return response()->json([
            'category' => $catData,
            'coupons' => $coupons->items(),
            'stores' => $stores,
            'total' => $coupons->total(),
            'page' => $coupons->currentPage(),
            'totalPages' => $coupons->lastPage(),
        ]);
    }
}
