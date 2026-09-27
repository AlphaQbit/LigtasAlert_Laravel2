<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Facility;
use Illuminate\Http\JsonResponse;

class StatsController
{
    public function index(): JsonResponse
    {
        $alerts = Alert::readAll();
        $active = array_filter($alerts, fn ($a) => $a['status'] === 'active');
        $resolved = array_filter($alerts, fn ($a) => $a['status'] === 'resolved');

        $today = new \DateTime('today');

        $resolvedToday = array_filter($resolved, function ($a) use ($today) {
            $updated = new \DateTime($a['updated_at'] ?? $a['created_at']);
            return $updated >= $today;
        });

        return response()->json([
            'active_count' => count($active),
            'resolved_today' => count($resolvedToday),
            'total_alerts' => count($alerts),
            'responders_on_duty' => 12,
        ]);
    }
}
