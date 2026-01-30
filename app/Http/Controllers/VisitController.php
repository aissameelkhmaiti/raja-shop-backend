<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

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
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays($days);

        // Récupérer les données existantes
        $visits = Visit::selectRaw('DATE(created_at) as date')
            ->selectRaw('COUNT(DISTINCT visitor_id) as total')
            ->selectRaw("SUM(CASE WHEN device = 'desktop' THEN 1 ELSE 0 END) as desktop")
            ->selectRaw("SUM(CASE WHEN device = 'mobile' THEN 1 ELSE 0 END) as mobile")
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Créer un tableau avec tous les jours (même ceux sans données)
        $period = CarbonPeriod::create($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));
        $result = [];

        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            
            if (isset($visits[$dateString])) {
                // Utiliser les vraies données
                $result[] = [
                    'date' => $dateString,
                    'total' => (int) $visits[$dateString]->total,
                    'desktop' => (string) $visits[$dateString]->desktop,
                    'mobile' => (string) $visits[$dateString]->mobile,
                ];
            } else {
                // Remplir avec des zéros pour les jours sans données
                $result[] = [
                    'date' => $dateString,
                    'total' => 0,
                    'desktop' => "0",
                    'mobile' => "0",
                ];
            }
        }

        return response()->json($result);
    }
}