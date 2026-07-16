@extends('admin.layouts.admin')

@section('title', 'Manage Ports')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1><i class="fas fa-anchor text-primary"></i> Manage Ports</h1>
            <p class="text-muted">Kelola data pelabuhan seluruh dunia</p>
        </div>
        <div class="col text-end">
            <a href="{{ route('admin.ports.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Port
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Kode</th>
                            <th>Negara</th>
                            <th>Kota</th>
                            <th>Tipe</th>
                            <th>Ukuran</th>
                            <th>Koordinat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ports as $port)
                            <tr>
                                <td>{{ $port->id }}</td>
                                <td><strong>{{ $port->name }}</strong></td>
                                <td><span class="badge bg-secondary">{{ $port->code ?? '-' }}</span></td>
                                <td>{{ $port->country->name ?? '-' }}</td>
                                <td>{{ $port->city ?? '-' }}</td>
                                <td><span class="badge bg-info">{{ $port->type ?? '-' }}</span></td>
                                <td>
                                    <span class="badge {{ $port->size === 'Large' ? 'bg-success' : ($port->size === 'Medium' ? 'bg-warning' : 'bg-secondary') }}">
                                        {{ $port->size ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <small>
                                        {{ number_format($port->latitude, 4) }}, 
                                        {{ number_format($port->longitude, 4) }}
                                    </small>
                                </td>
                                <td>
                                    <a href="{{ route('admin.ports.edit', $port) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.ports.destroy', $port) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus pelabuhan ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">Tidak ada data pelabuhan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <span class="text-muted small">
                        Menampilkan {{ $ports->firstItem() ?? 0 }} - {{ $ports->lastItem() ?? 0 }} 
                        dari {{ $ports->total() }} pelabuhan
                    </span>
                </div>
                <div>
                    {{ $ports->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection