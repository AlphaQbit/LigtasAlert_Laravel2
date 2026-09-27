<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\JsonResponse;

class FacilityController
{
    public function index(): JsonResponse
    {
        return response()->json(Facility::all());
    }
}
