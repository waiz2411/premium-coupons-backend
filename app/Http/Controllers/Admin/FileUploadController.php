<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileUploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:5120', // 5MB limit
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('uploads', 'public');
            return response()->json([
                'url' => asset('storage/' . $path),
                'path' => $path
            ], 201);
        }

        return response()->json(['message' => 'File not found'], 400);
    }
}
