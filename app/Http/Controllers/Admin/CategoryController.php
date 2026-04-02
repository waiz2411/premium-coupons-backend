<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private function mapCategory($category): array
    {
        $data = is_array($category) ? $category : $category->toArray();
        $data['imageUrl'] = $data['image_url'];
        return $data;
    }

    public function index()
    {
        $categories = Category::all()->map(fn($c) => $this->mapCategory($c));
        return response()->json($categories);
    }

    public function show($id)
    {
        return response()->json($this->mapCategory(Category::findOrFail($id)));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string',
            'description' => 'required|string',
            'icon'        => 'required|string',
            'imageUrl'    => 'nullable|string',
        ]);

        $category = Category::create([
            'name'        => $validated['name'],
            'description' => $validated['description'],
            'icon'        => $validated['icon'],
            'image_url'   => $validated['imageUrl'] ?? null,
        ]);

        return response()->json($this->mapCategory($category->fresh()), 201);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'sometimes|string',
            'description' => 'sometimes|string',
            'icon'        => 'sometimes|string',
            'imageUrl'    => 'nullable|string',
        ]);

        if (isset($validated['imageUrl'])) {
            $validated['image_url'] = $validated['imageUrl'];
            unset($validated['imageUrl']);
        }

        $category->update($validated);

        return response()->json($this->mapCategory($category->fresh()));
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(null, 204);
    }
}
