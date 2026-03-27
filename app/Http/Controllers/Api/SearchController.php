<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['q' => 'required|string', 'country' => 'required|string']);
        
        $query = $request->query('q');
        $country = strtolower($request->query('country'));

        $stores = Store::where('name', 'like', "%{$query}%")
            ->whereJsonContains('countries', $country)
            ->get();

        $coupons = Coupon::with('store')
            ->where('title', 'like', "%{$query}%")
            ->whereJsonContains('countries', $country)
            ->get();

        $products = Product::where('title', 'like', "%{$query}%")
            ->whereJsonContains('countries', $country)
            ->get();

        return response()->json([
            'stores' => $stores,
            'coupons' => $coupons,
            'products' => $products
        ]);
    }
}
