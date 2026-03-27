<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EventLog;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function pageView(Request $request)
    {
        $validated = $request->validate([
            'page' => 'required|string',
            'country' => 'required|string',
            'referrer' => 'nullable|string',
            'userAgent' => 'nullable|string',
        ]);

        EventLog::create([
            'type' => 'page_view',
            'country_code' => strtolower($validated['country']),
            'data' => [
                'page' => $validated['page'],
                'referrer' => $validated['referrer'] ?? null,
                'userAgent' => $validated['userAgent'] ?? null
            ]
        ]);

        return response()->json(['message' => 'Tracked']);
    }

    public function storeClick(Request $request)
    {
        $validated = $request->validate([
            'storeId' => 'required|string',
            'country' => 'required|string'
        ]);

        EventLog::create([
            'type' => 'store_click',
            'action' => 'click',
            'country_code' => strtolower($validated['country']),
            'data' => ['storeId' => $validated['storeId']]
        ]);

        return response()->json(['message' => 'Tracked']);
    }

    public function newsletterSubscribe(Request $request)
    {
        $validated = $request->validate(['email' => 'required|email']);
        
        EventLog::create([
            'type' => 'newsletter_subscribe',
            'data' => ['email' => $validated['email']]
        ]);

        return response()->json(['message' => 'Subscribed']);
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'query' => 'required|string',
            'country' => 'required|string',
            'resultCount' => 'required|numeric'
        ]);

        EventLog::create([
            'type' => 'search',
            'country_code' => strtolower($validated['country']),
            'data' => [
                'query' => $validated['query'],
                'resultCount' => $validated['resultCount']
            ]
        ]);

        return response()->json(['message' => 'Tracked']);
    }
}
