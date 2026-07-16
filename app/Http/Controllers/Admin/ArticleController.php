<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Country;
use App\Services\SentimentAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    protected $sentimentService;

    public function __construct(SentimentAnalysisService $sentimentService)
    {
        $this->sentimentService = $sentimentService;
    }

    public function index()
    {
        $articles = Article::with('country')->latest()->paginate(15);
        return view('admin.articles.index', compact('articles'));
    }

    public function create()
    {
        $countries = Country::orderBy('name')->get();
        return view('admin.articles.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'excerpt' => 'nullable|string|max:500',
            'author' => 'required|string|max:100',
            'source' => 'nullable|string|max:100',
            'url' => 'nullable|url',
            'category' => 'required|string|max:50',
            'country_id' => 'nullable|exists:countries,id',
            'featured_image' => 'nullable|url',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date'
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->title);
        $data['is_published'] = $request->has('is_published');
        $data['published_at'] = $request->published_at ?? now();

        // 🔥 Analisis Sentimen
        $text = ($request->title ?? '') . ' ' . ($request->excerpt ?? '') . ' ' . ($request->content ?? '');
        $sentiment = $this->sentimentService->analyzeText($text);

        $data['positive_score'] = $sentiment['positive'];
        $data['negative_score'] = $sentiment['negative'];
        $data['neutral_score'] = $sentiment['neutral'];

        $total = $sentiment['positive'] + $sentiment['negative'];
        $data['sentiment'] = $total > 0 ? ($sentiment['positive'] > $sentiment['negative'] ? 'positive' : 'negative') : 'neutral';

        $totalWords = $sentiment['positive'] + $sentiment['negative'] + $sentiment['neutral'];
        $data['sentiment_score'] = $totalWords > 0 ? ($sentiment['positive'] - $sentiment['negative']) / $totalWords : 0;

        Article::create($data);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil ditambahkan!');
    }

    public function edit(Article $article)
    {
        $countries = Country::orderBy('name')->get();
        return view('admin.articles.edit', compact('article', 'countries'));
    }

    public function update(Request $request, Article $article)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'excerpt' => 'nullable|string|max:500',
            'author' => 'required|string|max:100',
            'source' => 'nullable|string|max:100',
            'url' => 'nullable|url',
            'category' => 'required|string|max:50',
            'country_id' => 'nullable|exists:countries,id',
            'featured_image' => 'nullable|url',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date'
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->title);
        $data['is_published'] = $request->has('is_published');

        // 🔥 Analisis Sentimen Ulang
        $text = ($request->title ?? '') . ' ' . ($request->excerpt ?? '') . ' ' . ($request->content ?? '');
        $sentiment = $this->sentimentService->analyzeText($text);

        $data['positive_score'] = $sentiment['positive'];
        $data['negative_score'] = $sentiment['negative'];
        $data['neutral_score'] = $sentiment['neutral'];

        $total = $sentiment['positive'] + $sentiment['negative'];
        $data['sentiment'] = $total > 0 ? ($sentiment['positive'] > $sentiment['negative'] ? 'positive' : 'negative') : 'neutral';

        $totalWords = $sentiment['positive'] + $sentiment['negative'] + $sentiment['neutral'];
        $data['sentiment_score'] = $totalWords > 0 ? ($sentiment['positive'] - $sentiment['negative']) / $totalWords : 0;

        $article->update($data);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil diupdate!');
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel berhasil dihapus!');
    }
}