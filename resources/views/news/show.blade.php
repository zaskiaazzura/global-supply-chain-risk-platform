@extends('user.layouts.user')

@section('title', $article->title)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <a href="{{ route('news.index') }}" class="btn btn-outline-primary mb-3">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>

            <div class="card">
                <div class="card-body">
                    @if($article->image_url)
                        <img src="{{ $article->image_url }}" alt="News" class="img-fluid rounded mb-3">
                    @endif

                    <h2>{{ $article->title }}</h2>
                    <div class="text-muted mb-3">
                        <small>
                            <i class="fas fa-building"></i> {{ $article->source ?? 'Unknown' }}
                            @if($article->country)
                                • <i class="fas fa-flag"></i> {{ $article->country->name }}
                            @endif
                            • <i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($article->published_at)->format('d M Y H:i') }}
                        </small>
                    </div>
                    <hr>
                    <p>{{ $article->content ?? $article->description }}</p>
                    <hr>
                    <a href="{{ $article->url }}" target="_blank" class="btn btn-primary">
                        <i class="fas fa-external-link-alt"></i> Baca Sumber Asli
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection