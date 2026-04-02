<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    private function mapPost($post): array
    {
        $data = is_array($post) ? $post : $post->toArray();
        $data['readTime'] = $data['read_time'];
        return $data;
    }

    public function index(Request $request)
    {
        $query = BlogPost::query();
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        $posts = $query->get()->map(fn($p) => $this->mapPost($p));
        return response()->json($posts);
    }

    public function show($id)
    {
        return response()->json($this->mapPost(BlogPost::findOrFail($id)));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'    => 'required|string',
            'excerpt'  => 'required|string',
            'content'  => 'required|string',
            'image'    => 'required|string',
            'author'   => 'required|string',
            'category' => 'required|string',
            'readTime' => 'required|string',
        ]);

        $post = BlogPost::create([
            'title'     => $validated['title'],
            'excerpt'   => $validated['excerpt'],
            'content'   => $validated['content'],
            'image'     => $validated['image'],
            'author'    => $validated['author'],
            'date'      => now()->toDateString(),
            'category'  => $validated['category'],
            'read_time' => $validated['readTime'],
        ]);

        return response()->json($this->mapPost($post->fresh()), 201);
    }

    public function update(Request $request, $id)
    {
        $post = BlogPost::findOrFail($id);
        $validated = $request->validate([
            'title'    => 'sometimes|string',
            'excerpt'  => 'sometimes|string',
            'content'  => 'sometimes|string',
            'image'    => 'sometimes|string',
            'author'   => 'sometimes|string',
            'category' => 'sometimes|string',
            'readTime' => 'sometimes|string',
        ]);

        if (isset($validated['readTime'])) {
            $validated['read_time'] = $validated['readTime'];
            unset($validated['readTime']);
        }

        $post->update($validated);
        return response()->json($this->mapPost($post->fresh()));
    }

    public function destroy($id)
    {
        $post = BlogPost::findOrFail($id);
        $post->delete();
        return response()->json(null, 204);
    }
}
