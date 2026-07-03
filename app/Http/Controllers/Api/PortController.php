<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Port;
use App\Models\Country;
use App\Services\MarineTrafficService;
use Illuminate\Http\Request;

class PortController extends Controller
{
    protected $marineTraffic;

    public function __construct(MarineTrafficService $marineTraffic)
    {
        $this->marineTraffic = $marineTraffic;
    }

    /**
     * Get all ports
     * GET /api/ports
     */
    public function index(Request $request)
    {
        $query = Port::with('country');

        // Filter by country
        if ($request->has('country')) {
            $query->whereHas('country', function ($q) use ($request) {
                $q->where('code', $request->country)
                  ->orWhere('alpha2', $request->country);
            });
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by size
        if ($request->has('size')) {
            $query->where('size', $request->size);
        }

        $ports = $query->get();

        return response()->json([
            'success' => true,
            'count' => $ports->count(),
            'data' => $ports
        ]);
    }

    /**
     * Get port by ID
     * GET /api/ports/{id}
     */
    public function show($id)
    {
        $port = Port::with('country')->find($id);

        if (!$port) {
            return response()->json([
                'success' => false,
                'message' => 'Port not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $port
        ]);
    }

    /**
     * Search ports
     * GET /api/ports/search?q=keyword
     */
    public function search(Request $request)
    {
        $keyword = $request->get('q');

        if (!$keyword) {
            return response()->json([
                'success' => false,
                'message' => 'Search keyword required'
            ], 400);
        }

        $ports = Port::with('country')
            ->where('name', 'LIKE', "%{$keyword}%")
            ->orWhere('city', 'LIKE', "%{$keyword}%")
            ->orWhere('code', 'LIKE', "%{$keyword}%")
            ->get();

        return response()->json([
            'success' => true,
            'count' => $ports->count(),
            'data' => $ports
        ]);
    }

    /**
     * Get ports by country
     * GET /api/ports/country/{countryCode}
     */
    public function byCountry($countryCode)
    {
        $country = Country::where('code', $countryCode)
            ->orWhere('alpha2', $countryCode)
            ->first();

        if (!$country) {
            return response()->json([
                'success' => false,
                'message' => 'Country not found'
            ], 404);
        }

        $ports = Port::where('country_id', $country->id)->get();

        return response()->json([
            'success' => true,
            'country' => $country->name,
            'count' => $ports->count(),
            'data' => $ports
        ]);
    }

    /**
     * Sync ports from Marine Traffic API
     * POST /api/ports/sync
     */
    public function sync()
    {
        $result = $this->marineTraffic->syncPortsToDatabase();

        return response()->json([
            'success' => $result['success'],
            'message' => $result['success'] ? 'Ports synced successfully' : 'Failed to sync ports',
            'count' => $result['count'] ?? 0
        ]);
    }
}