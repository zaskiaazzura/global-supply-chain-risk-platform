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
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Author</th>
                            <th>Country</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($articles as $article)
                            <tr>
                                <td>{{ $article->title }}</td>
                                <td><span class="badge bg-info">{{ $article->category }}</span></td>
                                <td>{{ $article->author }}</td>
                                <td>{{ $article->country->name ?? 'Global' }}</td>
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
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus artikel ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">No articles found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $articles->links() }}
        </div>
    </div>
</div>
@endsection