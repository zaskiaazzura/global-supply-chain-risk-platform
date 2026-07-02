<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Tambahkan kedua baris di bawah ini untuk mengimport Model kamu:
use App\Models\Country;
use App\Models\Port;

class SubmisiCoreController extends Controller
{
    // GET /api/countries
    public function getCountries()
    {
        // Mengambil data negara dasar
        $countries = Country::select('id', 'code', 'name', 'region', 'currency_code', 'flag_url')->get();
        return response()->json([
            'status' => 'success',
            'data' => $countries
        ], 200);
    }

    // GET /api/ports
    public function getPorts(Request $request)
    {
        $query = Port::with('country');

        if ($request->has('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        $ports = $query->get();
        return response()->json([
            'status' => 'success',
            'data' => $ports
        ], 200);
    }
}