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
        $global = $seoSettings->firstWhere('page_key', 'global');
        $pages = $seoSettings->where('page_key', '!=', 'global')->values();

        return response()->json([
            'global' => [
                'siteTitle' => $global->meta_title ?? '',
                'siteDescription' => $global->meta_description ?? '',
                'keywords' => $global->keywords ?? ''
            ],
            'pages' => $pages->map(function ($p) {
                return [
                    'pageKey' => $p->page_key,
                    'metaTitle' => $p->meta_title,
                    'metaDescription' => $p->meta_description,
                    'urlSlug' => $p->url_slug
                ];
            })
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'global' => 'nullable|array',
            'pages' => 'nullable|array',
        ]);

        if (isset($validated['global'])) {
            SeoSetting::updateOrCreate(
                ['page_key' => 'global'],
                [
                    'meta_title' => $validated['global']['siteTitle'] ?? '',
                    'meta_description' => $validated['global']['siteDescription'] ?? '',
                    'keywords' => $validated['global']['keywords'] ?? '',
                ]
            );
        }

        if (isset($validated['pages'])) {
            foreach ($validated['pages'] as $page) {
                if (isset($page['pageKey'])) {
                    SeoSetting::updateOrCreate(
                        ['page_key' => $page['pageKey']],
                        [
                            'meta_title' => $page['metaTitle'] ?? '',
                            'meta_description' => $page['metaDescription'] ?? '',
                            'url_slug' => $page['urlSlug'] ?? '',
                        ]
                    );
                }
            }
        }

        return response()->json(['message' => 'SEO settings updated successfully']);
    }
}
