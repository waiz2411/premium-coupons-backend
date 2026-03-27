<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SeoSetting;
use Illuminate\Http\Request;

class SeoController extends Controller
{
    public function show($pageKey)
    {
        $seo = SeoSetting::where('page_key', $pageKey)->first();
        $global = SeoSetting::where('page_key', 'global')->first();

        // Use global fallback if page specific is empty
        $title = $seo->meta_title ?? '';
        if (empty($title)) $title = $global->meta_title ?? '';

        $description = $seo->meta_description ?? '';
        if (empty($description)) $description = $global->meta_description ?? '';

        return response()->json([
            'metaTitle' => $title,
            'metaDescription' => $description,
            'keywords' => $global->keywords ?? ''
        ]);
    }
}
