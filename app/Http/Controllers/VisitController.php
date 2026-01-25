<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'visitor_id' => 'required|string',
            'page' => 'nullable|string',
            'device' => 'nullable|string',
        ]);

        $exists = Visit::where('visitor_id', $request->visitor_id)
            ->whereDate('created_at', today())
            ->exists();

        if (!$exists) {
            Visit::create([
                'visitor_id' => $request->visitor_id,
                'page' => $request->page,
                'device' => $request->device,
                'ip' => $request->ip(),
            ]);
        }

        return response()->json(['status' => 'ok']);
    }

      public function stats(Request $request)
    {
        $days = $request->get('days', 30);

        $data = Visit::selectRaw('DATE(created_at) as date')
            ->selectRaw('COUNT(DISTINCT visitor_id) as total')
            ->selectRaw("SUM(device = 'desktop') as desktop")
            ->selectRaw("SUM(device = 'mobile') as mobile")
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($data);
    }
}

