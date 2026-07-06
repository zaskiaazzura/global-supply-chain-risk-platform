<?php

namespace App\Http\Controllers;

use App\Models\Watchlist;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WatchlistController extends Controller
{
    public function index()
    {
        $watchlist = Watchlist::with('country')
            ->where('user_id', Auth::id())
            ->get();
        return view('watchlist', compact('watchlist'));
    }

    /**
     * Toggle watchlist (add/remove)
     */
    public function toggle(Request $request)
    {
        try {
            $request->validate([
                'country_code' => 'required|exists:countries,code'
            ]);

            $country = Country::where('code', $request->country_code)->first();

            if (!$country) {
                return response()->json([
                    'success' => false,
                    'message' => 'Negara tidak ditemukan'
                ], 404);
            }

            // Cek apakah sudah ada di watchlist
            $existing = Watchlist::where('user_id', Auth::id())
                ->where('country_id', $country->id)
                ->first();

            if ($existing) {
                // Jika sudah ada, HAPUS (unfavorite)
                $existing->delete();
                return response()->json([
                    'success' => true,
                    'action' => 'removed',
                    'message' => '✅ Dihapus dari favorit!'
                ]);
            } else {
                // Jika belum ada, TAMBAH (favorite)
                Watchlist::create([
                    'user_id' => Auth::id(),
                    'country_id' => $country->id,
                    'note' => $request->note ?? null
                ]);
                return response()->json([
                    'success' => true,
                    'action' => 'added',
                    'message' => '✅ Ditambahkan ke favorit!'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Watchlist toggle error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if country is in watchlist
     */
    public function check($code)
    {
        try {
            $country = Country::where('code', $code)->first();
            
            if (!$country) {
                return response()->json(['isFavorite' => false]);
            }

            $isFavorite = Watchlist::where('user_id', Auth::id())
                ->where('country_id', $country->id)
                ->exists();

            return response()->json(['isFavorite' => $isFavorite]);

        } catch (\Exception $e) {
            return response()->json(['isFavorite' => false]);
        }
    }

    /**
     * Remove from watchlist
     */
    public function remove($id)
    {
        try {
            $watchlist = Watchlist::where('user_id', Auth::id())->findOrFail($id);
            $watchlist->delete();
            return redirect()->back()->with('success', 'Negara dihapus dari favorit!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus!');
        }
    }
}