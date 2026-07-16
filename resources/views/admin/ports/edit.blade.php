@extends('admin.layouts.admin')

@section('title', 'Edit Port')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1><i class="fas fa-edit text-warning"></i> Edit Pelabuhan</h1>
            <p class="text-muted">Edit data pelabuhan</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.ports.update', $port) }}">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Nama Pelabuhan</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $port->name) }}" required>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Kode Pelabuhan (UN/LOCODE)</label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $port->code) }}">
                            <small class="text-muted">Contoh: IDTPP, SGSIN</small>
                            @error('code')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Negara</label>
                            <select name="country_id" class="form-control @error('country_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Negara --</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ old('country_id', $port->country_id) == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }} ({{ $country->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('country_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Kota</label>
                            <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $port->city) }}">
                            @error('city')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Latitude</label>
                            <input type="number" step="any" name="latitude" class="form-control @error('latitude') is-invalid @enderror" value="{{ old('latitude', $port->latitude) }}" required>
                            @error('latitude')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Longitude</label>
                            <input type="number" step="any" name="longitude" class="form-control @error('longitude') is-invalid @enderror" value="{{ old('longitude', $port->longitude) }}" required>
                            @error('longitude')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Ukuran</label>
                            <select name="size" class="form-control @error('size') is-invalid @enderror">
                                <option value="">-- Pilih --</option>
                                <option value="Small" {{ old('size', $port->size) == 'Small' ? 'selected' : '' }}>Small</option>
                                <option value="Medium" {{ old('size', $port->size) == 'Medium' ? 'selected' : '' }}>Medium</option>
                                <option value="Large" {{ old('size', $port->size) == 'Large' ? 'selected' : '' }}>Large</option>
                            </select>
                            @error('size')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label>Tipe Pelabuhan</label>
                    <input type="text" name="type" class="form-control @error('type') is-invalid @enderror" value="{{ old('type', $port->type) }}" required>
                    @error('type')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update
                </button>
                <a href="{{ route('admin.ports.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection