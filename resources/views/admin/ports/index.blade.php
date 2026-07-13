@extends('admin.layouts.admin')

@section('title', 'Manage Ports')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1><i class="fas fa-anchor text-primary"></i> Manage Ports</h1>
            <p class="text-muted">Kelola data pelabuhan</p>
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

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Country</th>
                            <th>City</th>
                            <th>Type</th>
                            <th>Size</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ports as $port)
                            <tr>
                                <td>{{ $port->name }}</td>
                                <td>{{ $port->code }}</td>
                                <td>{{ $port->country->name ?? 'N/A' }}</td>
                                <td>{{ $port->city ?? '-' }}</td>
                                <td><span class="badge bg-info">{{ $port->type ?? '-' }}</span></td>
                                <td>
                                    <span class="badge {{ $port->size === 'Large' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $port->size ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.ports.edit', $port) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.ports.destroy', $port) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus port ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted">No ports found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $ports->links() }}
        </div>
    </div>
</div>
@endsection