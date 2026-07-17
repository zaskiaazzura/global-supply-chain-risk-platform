<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Port;
use Illuminate\Http\Request;

class RouteSimulationController extends Controller
{
    public function index()
    {
        $countries = Country::orderBy('name')->get();
        return view('route-simulation', compact('countries'));
    }

    public function calculate(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $type = $request->input('type', 'country');

        if ($type === 'country') {
            return $this->calculateCountryRoute($from, $to);
        } else {
            return $this->calculatePortRoute($from, $to);
        }
    }

    private function calculateCountryRoute($fromCode, $toCode)
    {
        $from = Country::where('code', $fromCode)->first();
        $to = Country::where('code', $toCode)->first();

        if (!$from || !$to) {
            return response()->json(['error' => 'Country not found'], 404);
        }

        $distance = $this->haversine($from->latitude, $from->longitude, $to->latitude, $to->longitude);

        $flightTime = round($distance / 800, 2);
        $shipTime = round($distance / 30, 2);

        $fromPort = Port::where('country_id', $from->id)->first();
        $toPort = Port::where('country_id', $to->id)->first();

        return response()->json([
            'success' => true,
            'from' => [
                'name' => $from->name,
                'code' => $from->code,
                'lat' => (float) $from->latitude,
                'lng' => (float) $from->longitude,
                'capital' => $from->capital,
            ],
            'to' => [
                'name' => $to->name,
                'code' => $to->code,
                'lat' => (float) $to->latitude,
                'lng' => (float) $to->longitude,
                'capital' => $to->capital,
            ],
            'distance' => [
                'km' => round($distance, 0),
                'miles' => round($distance * 0.621371, 0),
                'nautical' => round($distance * 0.539957, 0),
            ],
            'time' => [
                'flight' => round($flightTime, 1) . ' jam',
                'ship' => round($shipTime, 1) . ' jam (' . round($shipTime / 24, 1) . ' hari)',
            ],
            'ports' => [
                'from' => $fromPort ? $fromPort->name : 'Tidak ditemukan',
                'to' => $toPort ? $toPort->name : 'Tidak ditemukan',
            ]
        ]);
    }

    private function calculatePortRoute($fromId, $toId)
    {
        $from = Port::where('id', $fromId)->with('country')->first();
        $to = Port::where('id', $toId)->with('country')->first();

        if (!$from || !$to) {
            return response()->json(['error' => 'Port not found'], 404);
        }

        $distance = $this->haversine($from->latitude, $from->longitude, $to->latitude, $to->longitude);

        $flightTime = round($distance / 800, 2);
        $shipTime = round($distance / 30, 2);

        return response()->json([
            'success' => true,
            'from' => [
                'name' => $from->name,
                'code' => $from->code,
                'lat' => (float) $from->latitude,
                'lng' => (float) $from->longitude,
                'country' => $from->country->name ?? 'Unknown',
            ],
            'to' => [
                'name' => $to->name,
                'code' => $to->code,
                'lat' => (float) $to->latitude,
                'lng' => (float) $to->longitude,
                'country' => $to->country->name ?? 'Unknown',
            ],
            'distance' => [
                'km' => round($distance, 0),
                'miles' => round($distance * 0.621371, 0),
                'nautical' => round($distance * 0.539957, 0),
            ],
            'time' => [
                'flight' => round($flightTime, 1) . ' jam',
                'ship' => round($shipTime, 1) . ' jam (' . round($shipTime / 24, 1) . ' hari)',
            ],
        ]);
    }

    private function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $deltaLat = $lat2 - $lat1;
        $deltaLon = $lon2 - $lon1;

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
             cos($lat1) * cos($lat2) *
             sin($deltaLon / 2) * sin($deltaLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}