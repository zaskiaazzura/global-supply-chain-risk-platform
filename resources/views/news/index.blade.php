@extends('user.layouts.user')

@section('title', 'Logistics News')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h1><i class="fas fa-newspaper text-primary"></i> Logistics News</h1>
            <p class="text-muted">Berita terkini seputar logistik, supply chain, dan perdagangan global</p>
        </div>
    </div>

    <!-- Filter & Info -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('news.index') }}" class="d-flex gap-2">
                        <select name="country" class="form-select">
                            <option value="">Semua Negara</option>
                            @foreach($countries as $c)
                                <option value="{{ $c->code }}" {{ request('country') == $c->code ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('news.index') }}" class="btn btn-secondary">
                            <i class="fas fa-undo"></i>
                        </a>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <span>
                        <i class="fas fa-newspaper text-primary"></i> 
                        <strong>{{ $news->total() }}</strong> berita ditemukan
                    </span>
                    <span class="text-muted">
                        Halaman {{ $news->currentPage() }} dari {{ $news->lastPage() }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Berita -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 12%;">Negara</th>
                            <th style="width: 25%;">Judul</th>
                            <th style="width: 25%;">Deskripsi</th>
                            <th style="width: 12%;">Sumber</th>
                            <th style="width: 11%;">Sentimen</th>
                            <th style="width: 10%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($news as $index => $item)
                            <tr>
                                <td>{{ $news->firstItem() + $index }}</td>
                                <td>
                                    @if($item->country)
                                        <span class="badge bg-primary">{{ $item->country->name }}</span>
                                    @else
                                        <span class="badge bg-secondary">Global</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ Str::limit($item->title, 55) }}</strong>
                                </td>
                                <td>{{ Str::limit($item->description ?? $item->content ?? '-', 80) }}</td>
                                <td>
                                    <small class="text-muted">{{ $item->source ?? 'Unknown' }}</small>
                                    <br>
                                    <small class="text-muted" style="font-size: 0.65rem;">
                                        {{ $item->published_at ? \Carbon\Carbon::parse($item->published_at)->format('d M Y') : '-' }}
                                    </small>
                                </td>
                                <td>
                                    @if($item->sentiment)
                                        <span class="badge {{ $item->sentiment === 'positive' ? 'bg-success' : ($item->sentiment === 'negative' ? 'bg-danger' : 'bg-warning') }}">
                                            <i class="fas {{ $item->sentiment === 'positive' ? 'fa-smile' : ($item->sentiment === 'negative' ? 'fa-frown' : 'fa-meh') }}"></i>
                                            {{ ucfirst($item->sentiment) }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ $item->url }}" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                                    Belum ada berita logistik yang tersimpan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <span class="text-muted small">
                        Menampilkan {{ $news->firstItem() ?? 0 }} - {{ $news->lastItem() ?? 0 }} 
                        dari {{ $news->total() }} berita
                    </span>
                </div>
                <div>
                    {{ $news->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table th {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }
    .table td {
        vertical-align: middle;
        font-size: 0.82rem;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(52, 152, 219, 0.05);
    }
    .badge {
        font-size: 0.7rem;
        padding: 0.3rem 0.6rem;
    }
    .btn-sm {
        font-size: 0.7rem;
        padding: 0.2rem 0.5rem;
    }
    .card {
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    .card-body {
        padding: 1rem 1.25rem;
    }
    .pagination {
        margin-bottom: 0;
    }
    .page-link {
        font-size: 0.8rem;
        padding: 0.35rem 0.7rem;
    }
</style>
@endpush