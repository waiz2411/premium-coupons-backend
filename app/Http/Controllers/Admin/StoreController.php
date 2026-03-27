<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $query = Store::query();
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        return response()->json($query->get());
    }

    public function show($id)
    {
        return response()->json(Store::findOrFail($id));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'logo' => 'required|string',
            'description' => 'required|string',
            'about' => 'nullable|string',
            'categoryId' => 'required|string',
            'rating' => 'required|numeric',
            'isFeatured' => 'required|boolean',
            'affiliateLink' => 'required|string',
            'countries' => 'required|array',
        ]);

        $store = Store::create([
            'id' => (string) Str::uuid(),
            'name' => $validated['name'],
            'logo' => $validated['logo'],
            'description' => $validated['description'],
            'about' => $validated['about'] ?? null,
            'category_id' => $validated['categoryId'],
            'rating' => $validated['rating'],
            'is_featured' => $validated['isFeatured'],
            'affiliate_link' => $validated['affiliateLink'],
            'countries' => $validated['countries'],
        ]);

        return response()->json($store, 201);
    }

    public function update(Request $request, $id)
    {
        $store = Store::findOrFail($id);
        $validated = $request->validate([
            'name' => 'sometimes|string',
            'logo' => 'sometimes|string',
            'description' => 'sometimes|string',
            'about' => 'nullable|string',
            'categoryId' => 'sometimes|string',
            'rating' => 'sometimes|numeric',
            'isFeatured' => 'sometimes|boolean',
            'affiliateLink' => 'sometimes|string',
            'countries' => 'sometimes|array',
        ]);

        $updateData = [];
        if (isset($validated['categoryId'])) $updateData['category_id'] = $validated['categoryId'];
        if (isset($validated['isFeatured'])) $updateData['is_featured'] = $validated['isFeatured'];
        if (isset($validated['affiliateLink'])) $updateData['affiliate_link'] = $validated['affiliateLink'];
        
        $fields = ['name', 'logo', 'description', 'about', 'rating', 'countries'];
        foreach($fields as $f) {
            if (isset($validated[$f])) $updateData[$f] = $validated[$f];
        }

        $store->update($updateData);

        return response()->json($store);
    }

    public function destroy($id)
    {
        $store = Store::findOrFail($id);
        $store->delete();
        return response()->json(null, 204);
    }
}
