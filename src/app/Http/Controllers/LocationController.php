<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LocationController extends Controller
{
    public function saveLocation(Request $request): JsonResponse
    {
        $location = json_encode([
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
        ]);
        Session::put('location', $location);

        return response()->json([
            'status' => 'OK',
            'message' => 'Location saved successfully',
            'json' => $location,
        ]);
    }
}
