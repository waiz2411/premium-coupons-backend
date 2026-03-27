<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 10);

        $query = BlogPost::query();

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->has('category')) {
            $query->where('category', $request->query('category'));
        }

        $query->orderBy('date', 'desc');

        $paginator = $query->paginate($limit, ['id', 'title', 'excerpt', 'image', 'author', 'date', 'category', 'read_time'], 'page', $page);
        
        $items = $paginator->getCollection()->map(function ($p) {
            $item = $p->toArray();
            $item['readTime'] = $item['read_time'];
            unset($item['read_time']);
            return $item;
        });

        return response()->json([
            'posts' => $items,
            'total' => $paginator->total(),
            'page' => $paginator->currentPage(),
            'totalPages' => $paginator->lastPage(),
        ]);
    }

    public function show($id)
    {
        $post = BlogPost::findOrFail($id);
        
        $item = $post->toArray();
        $item['readTime'] = $item['read_time'];
        unset($item['read_time']);
            
        return response()->json($item);
    }
}
