<?php

namespace App\Http\Controllers;

use App\Models\NewsCache;
use App\Models\Country;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $country = $request->input('country');
        $category = $request->input('category', 'logistics');
        
        $query = NewsCache::with('country');

        if ($country) {
            $query->whereHas('country', function($q) use ($country) {
                $q->where('code', $country)->orWhere('alpha2', $country);
            });
        }

        if ($category) {
            $query->where('category', $category);
        }

        $news = $query->latest('published_at')->paginate(15);
        $countries = Country::all();

        return view('news.index', compact('news', 'countries'));
    }
}