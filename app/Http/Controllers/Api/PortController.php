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
     * GET /api/ports
     * Get all ports with filters
     */
    public function index(Request $request)
    {
        $query = Port::with('country');

        if ($request->has('country')) {
            $query->whereHas('country', function ($q) use ($request) {
                $q->where('code', $request->country)
                  ->orWhere('alpha2', $request->country);
            });
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('size')) {
            $query->where('size', $request->size);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('city', 'LIKE', "%{$search}%");
        }

        $ports = $query->get();

        return response()->json([
            'success' => true,
            'count' => $ports->count(),
            'data' => $ports
        ]);
    }

    /**
     * GET /api/ports/{id}
     * Get port by ID
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
     * GET /api/ports/search
     * Search ports
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
     * GET /api/ports/country/{countryCode}
     * Get ports by country
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
     * POST /api/ports/sync
     * Sync ports from Marine Traffic API
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