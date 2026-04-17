<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    private function mapStore($store): array
    {
        $data = is_array($store) ? $store : $store->toArray();
        $data['slug']          = $data['slug'] ?? null;
        $data['customSlugs']   = $data['custom_slugs'] ?? (object)[];
        $data['categoryId']    = $data['category_id'];
        $data['isFeatured']    = (bool)$data['is_featured'];
        $data['affiliateLink'] = $data['affiliate_link'];
        return $data;
    }

    public function index(Request $request)
    {
        $query = Store::query();
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $stores = $query->get()->map(fn($s) => $this->mapStore($s));
        return response()->json($stores);
    }

    public function show($id)
    {
        return response()->json($this->mapStore(Store::findOrFail($id)));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string',
            'slug'          => 'nullable|string',
            'logo'          => 'required|string',
            'description'   => 'required|string',
            'about'         => 'nullable|array',
            'categoryId'    => 'required|string',
            'rating'        => 'required|numeric',
            'isFeatured'    => 'required|boolean',
            'affiliateLink' => 'required|string',
            'countries'     => 'required|array',
            'customSlugs'   => 'nullable|array',
        ]);

        $store = Store::create([
            'name'           => $validated['name'],
            'slug'           => $validated['slug'] ?? \Illuminate\Support\Str::slug($validated['name'] . '-promo-code'),
            'logo'           => $validated['logo'],
            'description'    => $validated['description'],
            'about'          => $validated['about'] ?? null,
            'category_id'    => $validated['categoryId'],
            'rating'         => $validated['rating'],
            'is_featured'    => $validated['isFeatured'],
            'affiliate_link' => $validated['affiliateLink'],
            'countries'      => $validated['countries'],
            'custom_slugs'   => $validated['customSlugs'] ?? null,
        ]);

        return response()->json($this->mapStore($store->fresh()), 201);
    }

    public function update(Request $request, $id)
    {
        $store = Store::findOrFail($id);
        $validated = $request->validate([
            'name'          => 'sometimes|string',
            'slug'          => 'sometimes|string',
            'logo'          => 'sometimes|string',
            'description'   => 'sometimes|string',
            'about'         => 'nullable|array',
            'categoryId'    => 'sometimes|string',
            'rating'        => 'sometimes|numeric',
            'isFeatured'    => 'sometimes|boolean',
            'affiliateLink' => 'sometimes|string',
            'countries'     => 'sometimes|array',
            'customSlugs'   => 'nullable|array',
        ]);

        $updateData = [];
        if (isset($validated['categoryId']))    $updateData['category_id']    = $validated['categoryId'];
        if (isset($validated['isFeatured']))    $updateData['is_featured']    = $validated['isFeatured'];
        if (isset($validated['affiliateLink'])) $updateData['affiliate_link'] = $validated['affiliateLink'];

        $fields = ['name', 'slug', 'logo', 'description', 'about', 'rating', 'countries'];
        foreach ($fields as $f) {
            if (isset($validated[$f])) $updateData[$f] = $validated[$f];
        }
        if (array_key_exists('customSlugs', $validated)) {
            $updateData['custom_slugs'] = $validated['customSlugs'];
        }

        $store->update($updateData);

        return response()->json($this->mapStore($store->fresh()));
    }

    public function destroy($id)
    {
        $store = Store::findOrFail($id);
        $store->delete();
        return response()->json(null, 204);
    }
}
