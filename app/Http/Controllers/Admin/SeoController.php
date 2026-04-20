<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoSetting;
use Illuminate\Http\Request;

class SeoController extends Controller
{
    public function index()
    {
        $seoSettings = SeoSetting::all();
        
        return response()->json(
            $seoSettings->map(function ($p) {
                return [
                    'pageKey' => $p->page_key,
                    'metaTitle' => $p->meta_title ?? '',
                    'metaDescription' => $p->meta_description ?? '',
                    'keywords' => $p->keywords ?? '',
                    'urlSlug' => $p->url_slug ?? ''
                ];
            })
        );
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'seoSettings' => 'required|array',
        ]);

        foreach ($validated['seoSettings'] as $page) {
            if (isset($page['pageKey'])) {
                SeoSetting::updateOrCreate(
                    ['page_key' => $page['pageKey']],
                    [
                        'meta_title' => $page['metaTitle'] ?? '',
                        'meta_description' => $page['metaDescription'] ?? '',
                        'keywords' => $page['keywords'] ?? '',
                        'url_slug' => $page['urlSlug'] ?? '',
                    ]
                );
            }
        }

        return response()->json(['message' => 'SEO settings updated successfully']);
    }
}
