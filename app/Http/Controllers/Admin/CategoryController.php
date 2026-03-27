<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(Category::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'icon' => 'required|string',
            'imageUrl' => 'nullable|string',
        ]);

        $category = Category::create([
            'id' => (string) Str::uuid(),
            'name' => $validated['name'],
            'description' => $validated['description'],
            'icon' => $validated['icon'],
            'image_url' => $validated['imageUrl'] ?? null,
        ]);

        return response()->json($category, 201);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string',
            'description' => 'sometimes|string',
            'icon' => 'sometimes|string',
            'imageUrl' => 'nullable|string',
        ]);

        if (isset($validated['imageUrl'])) {
            $validated['image_url'] = $validated['imageUrl'];
            unset($validated['imageUrl']);
        }

        $category->update($validated);

        return response()->json($category);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(null, 204);
    }
}
