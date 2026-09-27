<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AlertController
{
    public function index(Request $request): JsonResponse
    {
        $alerts = Alert::readAll();

        if ($request->query('status') === 'active') {
            $alerts = array_filter($alerts, fn ($a) => $a['status'] === 'active');
        }

        if ($request->query('facility')) {
            $alerts = array_filter($alerts, fn ($a) => $a['facility_id'] === $request->query('facility'));
        }

        $alerts = array_values($alerts);
        usort($alerts, fn ($a, $b) => strcmp($b['created_at'], $a['created_at']));

        return response()->json([
            'alerts' => $alerts,
            'total' => count($alerts),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $input = $request->json()->all();

        $required = ['type', 'facility_id', 'room'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                return response()->json(['error' => "Missing required field: $field"], 400);
            }
        }

        $alerts = Alert::readAll();

        $newAlert = [
            'id' => uniqid('ALT-'),
            'type' => $input['type'],
            'facility_id' => $input['facility_id'],
            'room' => $input['room'],
            'message' => $input['message'] ?? '',
            'status' => 'active',
            'recipients' => $input['recipients'] ?? 0,
            'responders' => [],
            'created_at' => date('c'),
            'updated_at' => date('c'),
        ];

        $alerts[] = $newAlert;
        Alert::writeAll($alerts);

        return response()->json($newAlert, 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $input = $request->json()->all();
        $alerts = Alert::readAll();

        foreach ($alerts as &$alert) {
            if ($alert['id'] === $id) {
                if (isset($input['status'])) {
                    $alert['status'] = $input['status'];
                }
                if (isset($input['acknowledged_by'])) {
                    $alert['acknowledged_by'] = $input['acknowledged_by'];
                    $alert['acknowledged_at'] = date('c');
                }
                $alert['updated_at'] = date('c');
                Alert::writeAll($alerts);
                return response()->json($alert);
            }
        }

        return response()->json(['error' => 'Alert not found'], 404);
    }

    public function respond(Request $request, string $id): JsonResponse
    {
        $input = $request->json()->all();
        $alerts = Alert::readAll();

        foreach ($alerts as &$alert) {
            if ($alert['id'] === $id) {
                $alert['responders'][] = [
                    'name' => $input['name'] ?? 'Responder',
                    'responded_at' => date('c'),
                ];
                $alert['updated_at'] = date('c');
                Alert::writeAll($alerts);
                return response()->json($alert);
            }
        }

        return response()->json(['error' => 'Alert not found'], 404);
    }
}
