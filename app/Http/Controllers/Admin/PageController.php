<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomPage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        return response()->json(CustomPage::all());
    }

    public function show($id)
    {
        return response()->json(CustomPage::findOrFail($id));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'slug' => 'required|string|unique:custom_pages,slug',
            'heroTitle' => 'required|string',
            'heroDescription' => 'nullable|string',
            'heroImage' => 'nullable|string',
            'selectedCouponIds' => 'nullable|array',
            'isSubpage' => 'boolean',
            'parentPageId' => 'nullable|string',
        ]);

        $page = CustomPage::create([
            'id' => (string) Str::uuid(),
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'hero_title' => $validated['heroTitle'],
            'hero_description' => $validated['heroDescription'] ?? null,
            'hero_image' => $validated['heroImage'] ?? null,
            'selected_coupon_ids' => $validated['selectedCouponIds'] ?? [],
            'is_subpage' => $validated['isSubpage'] ?? false,
            'parent_page_id' => $validated['parentPageId'] ?? null,
        ]);

        return response()->json($page, 201);
    }

    public function update(Request $request, $id)
    {
        $page = CustomPage::findOrFail($id);
        $validated = $request->validate([
            'name' => 'sometimes|string',
            'slug' => 'sometimes|string|unique:custom_pages,slug,' . $id,
            'heroTitle' => 'sometimes|string',
            'heroDescription' => 'nullable|string',
            'heroImage' => 'nullable|string',
            'selectedCouponIds' => 'nullable|array',
            'isSubpage' => 'boolean',
            'parentPageId' => 'nullable|string',
        ]);

        $map = [
            'heroTitle' => 'hero_title', 'heroDescription' => 'hero_description',
            'heroImage' => 'hero_image', 'selectedCouponIds' => 'selected_coupon_ids',
            'isSubpage' => 'is_subpage', 'parentPageId' => 'parent_page_id'
        ];
        
        $updateData = [];
        foreach($validated as $key => $val) {
            $dbKey = $map[$key] ?? $key;
            if ($key === 'selectedCouponIds' && $val === null) $val = [];
            $updateData[$dbKey] = $val;
        }

        $page->update($updateData);
        return response()->json($page);
    }

    public function destroy($id)
    {
        $page = CustomPage::findOrFail($id);
        $page->delete();
        return response()->json(null, 204);
    }
}
