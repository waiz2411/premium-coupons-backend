<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    private function mapCoupon($coupon): array
    {
        $data = is_array($coupon) ? $coupon : $coupon->toArray();
        $data['storeId']     = $data['store_id'];
        $data['categoryId']  = $data['category_id'];
        $data['redirectUrl'] = $data['redirect_url'];
        $data['isDeal']      = (bool)$data['is_deal'];
        $data['expiryDate']  = $data['expiry_date'];
        $data['successRate'] = $data['success_rate'];
        $data['isVerified']  = (bool)$data['is_verified'];
        $data['isTrending']  = (bool)$data['is_trending'];
        $data['isNew']       = (bool)$data['is_new'];
        return $data;
    }

    public function index(Request $request)
    {
        $query = Coupon::query();
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        $coupons = $query->get()->map(fn($c) => $this->mapCoupon($c));
        return response()->json($coupons);
    }

    public function show($id)
    {
        return response()->json($this->mapCoupon(Coupon::findOrFail($id)));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_id'    => 'required|string',
            'category_id' => 'required|string',
            'title'       => 'required|string',
            'code'        => 'nullable|string',
            'redirect_url'=> 'required|string',
            'is_deal'     => 'required|boolean',
            'discount'    => 'required|string',
            'description' => 'required|string',
            'expiry_date' => 'required|date',
            'success_rate'=> 'required|integer',
            'is_verified' => 'required|boolean',
            'is_trending' => 'required|boolean',
            'is_new'      => 'required|boolean',
            'countries'   => 'required|array',
        ]);

        $coupon = Coupon::create($validated);

        return response()->json($this->mapCoupon($coupon->fresh()), 201);
    }

    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);
        $validated = $request->validate([
            'store_id'    => 'sometimes|string',
            'category_id' => 'sometimes|string',
            'title'       => 'sometimes|string',
            'code'        => 'nullable|string',
            'redirect_url'=> 'sometimes|string',
            'is_deal'     => 'sometimes|boolean',
            'discount'    => 'sometimes|string',
            'description' => 'sometimes|string',
            'expiry_date' => 'sometimes|date',
            'success_rate'=> 'sometimes|integer',
            'is_verified' => 'sometimes|boolean',
            'is_trending' => 'sometimes|boolean',
            'is_new'      => 'sometimes|boolean',
            'countries'   => 'sometimes|array',
        ]);

        $coupon->update($validated);
        return response()->json($this->mapCoupon($coupon->fresh()));
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
            'coupon_ids'  => 'required|array',
            'expiry_date' => 'required|date',
        ]);

        $updatedCount = Coupon::whereIn('id', $request->coupon_ids)
            ->update(['expiry_date' => $request->expiry_date]);

        return response()->json(['updatedCount' => $updatedCount]);
    }
}
