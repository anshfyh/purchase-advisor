<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $analyses = $request->user()
            ->analyses()
            ->latest()
            ->get();

        $stats = [
            'total' => $analyses->count(),
            'layak' => $analyses->whereIn('category', [
                'LAYAK',
                'SANGAT_LAYAK'
            ])->count(),
            'kurang_layak' => $analyses
                ->where('category', 'KURANG_LAYAK')
                ->count(),
            'tidak_layak' => $analyses
                ->where('category', 'TIDAK_LAYAK')
                ->count(),
        ];

        $recent = $analyses->take(5);

        $priorities = $analyses
            ->sortByDesc('score')
            ->take(5)
            ->values();

        return view('dashboard', [
            'stats' => $stats,
            'recent' => $recent,
            'priorities' => $priorities,
            'analyses' => $analyses,
        ]);
    }

    public function history(Request $request): View
    {
        return view('history', [
            'analyses' => $request->user()
                ->analyses()
                ->latest()
                ->paginate(10),

            'totalAnalyses' => $request->user()
                ->analyses()
                ->count(),
        ]);
    }
}