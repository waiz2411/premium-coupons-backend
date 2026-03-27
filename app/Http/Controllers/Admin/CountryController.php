<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index(Request $request)
    {
        $query = Country::query();
        if ($request->has('search')) {
            $search = $request->query('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
        }
        return response()->json($query->get());
    }

    public function update(Request $request, $code)
    {
        // Try to find the country, create if it doesn't exist yet (for admin settings initial saves)
        $country = Country::firstOrNew(['code' => strtolower($code)]);
        
        $validated = $request->validate([
            'name' => 'sometimes|string',
            'heroImages' => 'nullable|array',
            'featuredStoreIds' => 'nullable|array',
            'trendingCouponIds' => 'nullable|array',
        ]);

        if (isset($validated['name'])) $country->name = $validated['name'];
        if (array_key_exists('heroImages', $validated)) $country->hero_images = $validated['heroImages'] ?? [];
        if (array_key_exists('featuredStoreIds', $validated)) $country->featured_store_ids = $validated['featuredStoreIds'] ?? [];
        if (array_key_exists('trendingCouponIds', $validated)) $country->trending_coupon_ids = $validated['trendingCouponIds'] ?? [];

        // If name wasn't set and this is new, set it to uppercase code as fallback
        if (!$country->exists && empty($country->name)) {
            $country->name = strtoupper($code);
        }

        $country->save();
        return response()->json($country);
    }

    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'heroImages' => 'required|array'
        ]);

        $updatedCount = Country::query()->update(['hero_images' => json_encode($validated['heroImages'])]);
        return response()->json(['updatedCount' => $updatedCount]);
    }
}
