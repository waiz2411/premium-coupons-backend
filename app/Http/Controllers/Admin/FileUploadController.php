<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileUploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,jpg,png,gif,webp|max:5120', // 5MB limit
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('uploads', 'public');
            // asset('storage/' . $path) generates the correct public URL after storage:link
            $url = asset('storage/app/public/' . $path);
            return response()->json([
                'url' => $url,
                'path' => $path,
            ], 201);
        }

        return response()->json(['message' => 'File not found'], 400);
    }
}
