<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        return response()->json($query->get());
    }

    public function show($id)
    {
        return response()->json(Product::findOrFail($id));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'storeId' => 'required|string',
            'categoryId' => 'required|string',
            'description' => 'required|string',
            'affiliateUrl' => 'required|string',
            'imageUrl' => 'nullable|string',
            'isTrending' => 'required|boolean',
            'countries' => 'required|array',
        ]);

        $product = Product::create([
            'id' => (string) Str::uuid(),
            'title' => $validated['title'],
            'store_id' => $validated['storeId'],
            'category_id' => $validated['categoryId'],
            'description' => $validated['description'],
            'affiliate_url' => $validated['affiliateUrl'],
            'image_url' => $validated['imageUrl'] ?? null,
            'is_trending' => $validated['isTrending'],
            'countries' => $validated['countries'],
        ]);

        return response()->json($product, 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $validated = $request->validate([
            'title' => 'sometimes|string',
            'storeId' => 'sometimes|string',
            'categoryId' => 'sometimes|string',
            'description' => 'sometimes|string',
            'affiliateUrl' => 'sometimes|string',
            'imageUrl' => 'nullable|string',
            'isTrending' => 'sometimes|boolean',
            'countries' => 'sometimes|array',
        ]);

        $map = [
            'storeId' => 'store_id', 'categoryId' => 'category_id',
            'affiliateUrl' => 'affiliate_url', 'imageUrl' => 'image_url',
            'isTrending' => 'is_trending'
        ];
        
        $updateData = [];
        foreach($validated as $key => $val) {
            $dbKey = $map[$key] ?? $key;
            $updateData[$dbKey] = $val;
        }

        $product->update($updateData);
        return response()->json($product);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(null, 204);
    }
}
