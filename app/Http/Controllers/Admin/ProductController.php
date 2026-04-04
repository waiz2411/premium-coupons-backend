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
        $data['originalPrice'] = $data['original_price'];
        $data['discountedPrice'] = $data['discounted_price'];
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
            'store_id'     => 'required|string',
            'category_id'  => 'required|string',
            'description'  => 'required|string',
            'affiliate_url'=> 'required|string',
            'image_url'    => 'nullable|string',
            'is_trending'  => 'required|boolean',
            'countries'    => 'required|array',
            'original_price' => 'nullable|string',
            'discounted_price' => 'nullable|string',
        ]);

        $product = Product::create($validated);

        return response()->json($this->mapProduct($product->fresh()), 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $validated = $request->validate([
            'title'        => 'sometimes|string',
            'store_id'     => 'sometimes|string',
            'category_id'  => 'sometimes|string',
            'description'  => 'sometimes|string',
            'affiliate_url'=> 'sometimes|string',
            'image_url'    => 'nullable|string',
            'is_trending'  => 'sometimes|boolean',
            'countries'    => 'sometimes|array',
            'original_price' => 'nullable|string',
            'discounted_price' => 'nullable|string',
        ]);

        $product->update($validated);
        return response()->json($this->mapProduct($product->fresh()));
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(null, 204);
    }
}
