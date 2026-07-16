@extends('admin.layouts.admin')

@section('title', 'Manage Articles')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1><i class="fas fa-newspaper text-primary"></i> Manage Articles</h1>
            <p class="text-muted">Kelola artikel analisis supply chain</p>
        </div>
        <div class="col text-end">
            <a href="{{ route('admin.articles.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Article
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Author</th>
                            <th>Source</th>
                            <th>Country</th>
                            <th>Sentiment</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($articles as $article)
                            <tr>
                                <td>
                                    <strong>{{ Str::limit($article->title, 50) }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $article->category }}</span>
                                </td>
                                <td>{{ $article->author }}</td>
                                <td>
                                    @if($article->source)
                                        <span class="badge bg-secondary">{{ Str::limit($article->source, 20) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $article->country->name ?? 'Global' }}</td>
                                <td>
                                    @if($article->sentiment)
                                        <span class="badge bg-{{ $article->sentiment_badge }}">
                                            <i class="fas {{ $article->sentiment === 'positive' ? 'fa-smile' : ($article->sentiment === 'negative' ? 'fa-frown' : 'fa-meh') }}"></i>
                                            {{ $article->sentiment_label }}
                                            @if($article->sentiment_score !== null)
                                                <small>({{ number_format($article->sentiment_score * 100, 0) }}%)</small>
                                            @endif
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $article->is_published ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $article->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus artikel ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">Tidak ada artikel</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <span class="text-muted small">
                        Menampilkan {{ $articles->firstItem() ?? 0 }} - {{ $articles->lastItem() ?? 0 }} 
                        dari {{ $articles->total() }} artikel
                    </span>
                </div>
                <div>
                    {{ $articles->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection