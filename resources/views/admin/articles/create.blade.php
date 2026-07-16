@extends('admin.layouts.admin')

@section('title', 'Add Article')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1><i class="fas fa-plus-circle text-success"></i> Add Article</h1>
            <p class="text-muted">Tambah artikel analisis supply chain</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.articles.store') }}">
                @csrf

                <div class="mb-3">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                    @error('title')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label>Excerpt</label>
                    <textarea name="excerpt" class="form-control @error('excerpt') is-invalid @enderror" rows="2">{{ old('excerpt') }}</textarea>
                    @error('excerpt')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label>Content</label>
                    <textarea name="content" class="form-control @error('content') is-invalid @enderror" rows="6" required>{{ old('content') }}</textarea>
                    @error('content')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Author</label>
                            <input type="text" name="author" class="form-control @error('author') is-invalid @enderror" value="{{ old('author') }}" required>
                            @error('author')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Source</label>
                            <input type="text" name="source" class="form-control @error('source') is-invalid @enderror" value="{{ old('source') }}">
                            @error('source')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Category</label>
                            <select name="category" class="form-control @error('category') is-invalid @enderror" required>
                                <option value="">-- Pilih --</option>
                                <option value="logistics" {{ old('category') == 'logistics' ? 'selected' : '' }}>Logistics</option>
                                <option value="economy" {{ old('category') == 'economy' ? 'selected' : '' }}>Economy</option>
                                <option value="trade" {{ old('category') == 'trade' ? 'selected' : '' }}>Trade</option>
                                <option value="shipping" {{ old('category') == 'shipping' ? 'selected' : '' }}>Shipping</option>
                                <option value="technology" {{ old('category') == 'technology' ? 'selected' : '' }}>Technology</option>
                            </select>
                            @error('category')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Country</label>
                            <select name="country_id" class="form-control @error('country_id') is-invalid @enderror">
                                <option value="">Global</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('country_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>URL</label>
                            <input type="url" name="url" class="form-control @error('url') is-invalid @enderror" value="{{ old('url') }}" placeholder="https://...">
                            @error('url')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Featured Image URL</label>
                            <input type="url" name="featured_image" class="form-control @error('featured_image') is-invalid @enderror" value="{{ old('featured_image') }}">
                            @error('featured_image')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Published At</label>
                            <input type="datetime-local" name="published_at" class="form-control @error('published_at') is-invalid @enderror" value="{{ old('published_at') }}">
                            @error('published_at')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3 form-check mt-4">
                            <input type="checkbox" name="is_published" class="form-check-input" id="is_published" {{ old('is_published') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_published">Published</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection