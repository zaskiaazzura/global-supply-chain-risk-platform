@extends('user.layouts.user')

@section('title', 'Favorite Countries')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1><i class="fas fa-star text-warning"></i> Favorite Countries</h1>
            <p class="text-muted">Daftar negara favorit yang kamu pantau</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        @forelse($watchlist as $item)
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        @if($item->country->flag_url && str_starts_with($item->country->flag_url, 'http'))
                            <img src="{{ $item->country->flag_url }}" alt="Flag" style="width: 60px; height: auto;" class="mb-2">
                        @else
                            <img src="https://flagcdn.com/48x36/{{ strtolower($item->country->alpha2 ?? '') }}.png" alt="Flag" style="width: 60px; height: auto;" class="mb-2">
                        @endif
                        <h5 class="card-title">{{ $item->country->name }}</h5>
                        <p class="text-muted">
                            <span class="badge bg-primary">{{ $item->country->code }}</span>
                            <span class="badge bg-info">{{ $item->country->currency }}</span>
                        </p>
                        <p class="small text-muted">
                            {{ $item->note ?? 'Tidak ada catatan' }}
                        </p>
                        <form action="{{ route('watchlist.remove', $item->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus dari favorit?')">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                        <a href="{{ route('dashboard') }}?country={{ $item->country->code }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye"></i> Lihat
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-2x"></i>
                    <h4>Belum ada negara favorit</h4>
                    <p>Kunjungi dashboard dan klik <strong>"Tambah Favorit"</strong> untuk menambahkan negara.</p>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Ke Dashboard
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection