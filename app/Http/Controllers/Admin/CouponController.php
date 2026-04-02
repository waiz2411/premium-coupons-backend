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
            'storeId'     => 'required|string',
            'categoryId'  => 'required|string',
            'title'       => 'required|string',
            'code'        => 'nullable|string',
            'redirectUrl' => 'required|string',
            'isDeal'      => 'required|boolean',
            'discount'    => 'required|string',
            'description' => 'required|string',
            'expiryDate'  => 'required|date',
            'successRate' => 'required|integer',
            'isVerified'  => 'required|boolean',
            'isTrending'  => 'required|boolean',
            'isNew'       => 'required|boolean',
            'countries'   => 'required|array',
        ]);

        $coupon = Coupon::create([
            'store_id'    => $validated['storeId'],
            'category_id' => $validated['categoryId'],
            'title'       => $validated['title'],
            'code'        => $validated['code'] ?? null,
            'redirect_url'=> $validated['redirectUrl'],
            'is_deal'     => $validated['isDeal'],
            'discount'    => $validated['discount'],
            'description' => $validated['description'],
            'expiry_date' => $validated['expiryDate'],
            'success_rate'=> $validated['successRate'],
            'is_verified' => $validated['isVerified'],
            'is_trending' => $validated['isTrending'],
            'is_new'      => $validated['isNew'],
            'countries'   => $validated['countries'],
        ]);

        return response()->json($this->mapCoupon($coupon->fresh()), 201);
    }

    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);
        $validated = $request->validate([
            'storeId'     => 'sometimes|string',
            'categoryId'  => 'sometimes|string',
            'title'       => 'sometimes|string',
            'code'        => 'nullable|string',
            'redirectUrl' => 'sometimes|string',
            'isDeal'      => 'sometimes|boolean',
            'discount'    => 'sometimes|string',
            'description' => 'sometimes|string',
            'expiryDate'  => 'sometimes|date',
            'successRate' => 'sometimes|integer',
            'isVerified'  => 'sometimes|boolean',
            'isTrending'  => 'sometimes|boolean',
            'isNew'       => 'sometimes|boolean',
            'countries'   => 'sometimes|array',
        ]);

        $map = [
            'storeId'     => 'store_id',
            'categoryId'  => 'category_id',
            'redirectUrl' => 'redirect_url',
            'isDeal'      => 'is_deal',
            'expiryDate'  => 'expiry_date',
            'successRate' => 'success_rate',
            'isVerified'  => 'is_verified',
            'isTrending'  => 'is_trending',
            'isNew'       => 'is_new',
        ];

        $updateData = [];
        foreach ($validated as $key => $val) {
            $dbKey = $map[$key] ?? $key;
            $updateData[$dbKey] = $val;
        }

        $coupon->update($updateData);
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
            'couponIds'  => 'required|array',
            'expiryDate' => 'required|date',
        ]);

        $updatedCount = Coupon::whereIn('id', $request->couponIds)
            ->update(['expiry_date' => $request->expiryDate]);

        return response()->json(['updatedCount' => $updatedCount]);
    }
}
