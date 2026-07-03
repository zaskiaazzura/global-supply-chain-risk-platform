<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Services\ExchangeRateService;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    protected $exchangeRate;

    public function __construct(ExchangeRateService $exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;
    }

    /**
     * Get all currency rates
     * GET /api/currency
     */
    public function index(Request $request)
    {
        $base = $request->get('base', 'USD');
        
        $rates = $this->exchangeRate->getLatestRates($base);

        if (!$rates) {
            // Fallback to database
            $currencies = Currency::all();
            return response()->json([
                'success' => true,
                'source' => 'database',
                'data' => $currencies
            ]);
        }

        return response()->json([
            'success' => true,
            'source' => 'api',
            'base' => $rates['base'],
            'date' => $rates['date'],
            'data' => $rates['rates']
        ]);
    }

    /**
     * Get specific currency rate
     * GET /api/currency/{code}
     */
    public function show($code, Request $request)
    {
        $base = $request->get('base', 'USD');
        $code = strtoupper($code);
        
        $rate = $this->exchangeRate->getRate($base, $code);

        if (!$rate) {
            return response()->json([
                'success' => false,
                'message' => 'Currency rate not found'
            ], 404);
        }

        // Also get from database for historical
        $currency = Currency::where('code', $code)->first();

        return response()->json([
            'success' => true,
            'data' => [
                'from' => $rate['from'],
                'to' => $rate['to'],
                'rate' => $rate['rate'],
                'date' => $rate['date'],
                'historical' => $currency ? [
                    'daily_change' => $currency->daily_change,
                    'weekly_change' => $currency->weekly_change,
                    'monthly_change' => $currency->monthly_change
                ] : null
            ]
        ]);
    }

    /**
     * Get historical rates
     * GET /api/currency/historical/{code}
     */
    public function historical($code, Request $request)
    {
        $days = $request->get('days', 30);
        $code = strtoupper($code);

        $historical = $this->exchangeRate->getHistoricalRates($code, $days);

        if (!$historical) {
            return response()->json([
                'success' => false,
                'message' => 'Historical data not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'currency' => $code,
            'days' => $days,
            'data' => $historical
        ]);
    }

    /**
     * Convert currency
     * GET /api/currency/convert?from=USD&to=IDR&amount=100
     */
    public function convert(Request $request)
    {
        $from = strtoupper($request->get('from', 'USD'));
        $to = strtoupper($request->get('to', 'IDR'));
        $amount = $request->get('amount', 1);

        $result = $this->exchangeRate->convert($amount, $from, $to);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Conversion failed'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * Sync currency rates
     * POST /api/currency/sync
     */
    public function sync()
    {
        $result = $this->exchangeRate->updateCurrencyRates();

        return response()->json([
            'success' => $result['success'],
            'message' => $result['success'] ? 'Currency rates synced successfully' : 'Failed to sync rates',
            'count' => $result['count'] ?? 0
        ]);
    }
}