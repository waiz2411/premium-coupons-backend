<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $query = Coupon::query();
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        return response()->json($query->get());
    }

    public function show($id)
    {
        return response()->json(Coupon::findOrFail($id));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'storeId' => 'required|string',
            'categoryId' => 'required|string',
            'title' => 'required|string',
            'code' => 'nullable|string',
            'redirectUrl' => 'required|string',
            'isDeal' => 'required|boolean',
            'discount' => 'required|string',
            'description' => 'required|string',
            'expiryDate' => 'required|date',
            'successRate' => 'required|integer',
            'isVerified' => 'required|boolean',
            'isTrending' => 'required|boolean',
            'isNew' => 'required|boolean',
            'countries' => 'required|array',
        ]);

        $coupon = Coupon::create([
            'id' => (string) Str::uuid(),
            'store_id' => $validated['storeId'],
            'category_id' => $validated['categoryId'],
            'title' => $validated['title'],
            'code' => $validated['code'] ?? null,
            'redirect_url' => $validated['redirectUrl'],
            'is_deal' => $validated['isDeal'],
            'discount' => $validated['discount'],
            'description' => $validated['description'],
            'expiry_date' => $validated['expiryDate'],
            'success_rate' => $validated['successRate'],
            'is_verified' => $validated['isVerified'],
            'is_trending' => $validated['isTrending'],
            'is_new' => $validated['isNew'],
            'countries' => $validated['countries'],
        ]);

        return response()->json($coupon, 201);
    }

    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);
        $validated = $request->validate([
            'storeId' => 'sometimes|string',
            'categoryId' => 'sometimes|string',
            'title' => 'sometimes|string',
            'code' => 'nullable|string',
            'redirectUrl' => 'sometimes|string',
            'isDeal' => 'sometimes|boolean',
            'discount' => 'sometimes|string',
            'description' => 'sometimes|string',
            'expiryDate' => 'sometimes|date',
            'successRate' => 'sometimes|integer',
            'isVerified' => 'sometimes|boolean',
            'isTrending' => 'sometimes|boolean',
            'isNew' => 'sometimes|boolean',
            'countries' => 'sometimes|array',
        ]);

        $map = [
            'storeId' => 'store_id', 'categoryId' => 'category_id',
            'redirectUrl' => 'redirect_url', 'isDeal' => 'is_deal',
            'expiryDate' => 'expiry_date', 'successRate' => 'success_rate',
            'isVerified' => 'is_verified', 'isTrending' => 'is_trending',
            'isNew' => 'is_new'
        ];
        
        $updateData = [];
        foreach($validated as $key => $val) {
            $dbKey = $map[$key] ?? $key;
            $updateData[$dbKey] = $val;
        }

        $coupon->update($updateData);
        return response()->json($coupon);
    }

    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();
        return response()->json(null, 204);
    }

    public function bulkUpdateExpiry(Request $request)
    {
        $request->validate([
            'couponIds' => 'required|array',
            'expiryDate' => 'required|date'
        ]);

        $updatedCount = Coupon::whereIn('id', $request->couponIds)
            ->update(['expiry_date' => $request->expiryDate]);

        return response()->json(['updatedCount' => $updatedCount]);
    }
}
