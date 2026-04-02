<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private function mapProduct($product): array
    {
        $data = is_array($product) ? $product : $product->toArray();
        $data['storeId']      = $data['store_id'];
        $data['categoryId']   = $data['category_id'];
        $data['affiliateUrl'] = $data['affiliate_url'];
        $data['imageUrl']     = $data['image_url'];
        $data['isTrending']   = (bool)$data['is_trending'];
        $data['price']        = $data['price'] ?? null;
        return $data;
    }

    public function index(Request $request)
    {
        $query = Product::query();
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        $products = $query->get()->map(fn($p) => $this->mapProduct($p));
        return response()->json($products);
    }

    public function show($id)
    {
        return response()->json($this->mapProduct(Product::findOrFail($id)));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string',
            'storeId'      => 'required|string',
            'categoryId'   => 'required|string',
            'description'  => 'required|string',
            'affiliateUrl' => 'required|string',
            'imageUrl'     => 'nullable|string',
            'isTrending'   => 'required|boolean',
            'countries'    => 'required|array',
            'price'        => 'nullable|string',
        ]);

        $product = Product::create([
            'title'         => $validated['title'],
            'store_id'      => $validated['storeId'],
            'category_id'   => $validated['categoryId'],
            'description'   => $validated['description'],
            'affiliate_url' => $validated['affiliateUrl'],
            'image_url'     => $validated['imageUrl'] ?? null,
            'is_trending'   => $validated['isTrending'] ?? $validated['is_trending'] ?? false,
            'countries'     => $validated['countries'],
            'price'         => $validated['price'] ?? null,
        ]);

        return response()->json($this->mapProduct($product->fresh()), 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $validated = $request->validate([
            'title'        => 'sometimes|string',
            'storeId'      => 'sometimes|string',
            'categoryId'   => 'sometimes|string',
            'description'  => 'sometimes|string',
            'affiliateUrl' => 'sometimes|string',
            'imageUrl'     => 'nullable|string',
            'isTrending'   => 'sometimes|boolean',
            'countries'    => 'sometimes|array',
            'price'        => 'nullable|string',
        ]);

        $map = [
            'storeId'      => 'store_id',
            'categoryId'   => 'category_id',
            'affiliateUrl' => 'affiliate_url',
            'imageUrl'     => 'image_url',
            'isTrending'   => 'is_trending',
        ];

        $updateData = [];
        foreach ($validated as $key => $val) {
            $dbKey = $map[$key] ?? $key;
            $updateData[$dbKey] = $val;
        }

        $product->update($updateData);
        return response()->json($this->mapProduct($product->fresh()));
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(null, 204);
    }
}
