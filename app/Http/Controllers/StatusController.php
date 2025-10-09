<?php

namespace App\Http\Controllers;

use App\Models\Facility;

class StatusController extends Controller
{
    public function index()
    {
        // Fetch all facilities and group by type for easy rendering
        $facilities = Facility::query()
            ->orderBy('type')
            ->orderBy('code')
            ->get()
            ->groupBy('type');

        // Helper closures
        $isActive = fn(string $status) => in_array($status, ['open','active','busy','boarding','final_call','on_time']);

        // Pre-compute summary counts per type
        $summary = [];
        foreach ($facilities as $type => $list) {
            $total = $list->count();
            $active = $list->filter(fn($f) => $isActive($f->status))->count();
            $countToday = $list->sum('today_count');
            $summary[$type] = compact('total','active','countToday');
        }

        return view('status', [
            'facilities' => $facilities,
            'summary' => $summary,
        ]);
    }
}
