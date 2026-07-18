@extends('user.layouts.user')

@section('title', 'Global Weather Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1><i class="fas fa-cloud-sun text-warning"></i> Global Weather Dashboard</h1>
            <p class="text-muted">Pantau kondisi cuaca seluruh dunia secara real-time</p>
        </div>
        <div class="col text-end">
            <a href="{{ route('weather.refresh') }}" class="btn btn-outline-primary">
                <i class="fas fa-sync"></i> Refresh Data
            </a>
        </div>
    </div>

    <!-- HANYA PENCARIAN -->
    <div class="row mb-4">
        <div class="col-md-6">
            <form method="GET" action="{{ route('weather.index') }}" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Cari negara..." value="{{ $search }}">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                </button>
                @if($search)
                    <a href="{{ route('weather.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Reset
                    </a>
                @endif
            </form>
        </div>
        <div class="col-md-6 text-end">
            <span class="badge bg-info">Total: {{ $totalCountries }} negara</span>
        </div>
    </div>

    <!-- Tabel Cuaca -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 18%;">Negara</th>
                            <th style="width: 15%;">Region</th>
                            <th style="width: 12%;">Suhu</th>
                            <th style="width: 12%;">Angin</th>
                            <th style="width: 18%;">Kondisi</th>
                            <th style="width: 10%;">Risk</th>
                            <th style="width: 10%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($weatherData as $index => $data)
                            @php
                                $country = $data['country'];
                                $temp = $data['temperature'];
                                $wind = $data['windspeed'];
                                $code = $data['weathercode'];
                                $risk = $data['risk'];
                                
                                $weatherCodes = [
                                    0 => '☀️ Cerah', 1 => '🌤️ Sebagian Cerah', 2 => '⛅ Berawan', 3 => '☁️ Mendung',
                                    45 => '🌫️ Kabut', 48 => '🌫️ Kabut Embun',
                                    51 => '🌧️ Gerimis Ringan', 53 => '🌧️ Gerimis Sedang', 55 => '🌧️ Gerimis Deras',
                                    61 => '🌧️ Hujan Ringan', 63 => '🌧️ Hujan Sedang', 65 => '🌧️ Hujan Deras',
                                    71 => '🌨️ Salju Ringan', 73 => '🌨️ Salju Sedang', 75 => '🌨️ Salju Deras',
                                    80 => '🌦️ Hujan Ringan', 81 => '🌧️ Hujan Sedang', 82 => '⛈️ Hujan Deras',
                                    95 => '⛈️ Badai Petir', 96 => '⛈️ Badai + Hujan Es', 99 => '⛈️ Badai + Hujan Es Deras'
                                ];
                                $condition = isset($weatherCodes[$code]) ? $weatherCodes[$code] : '❓ Unknown';
                                
                                $riskBadge = [
                                    'low' => 'bg-success',
                                    'medium' => 'bg-warning',
                                    'high' => 'bg-danger',
                                    'critical' => 'bg-dark',
                                    'unknown' => 'bg-secondary'
                                ];
                                $riskLabel = [
                                    'low' => 'Rendah',
                                    'medium' => 'Sedang',
                                    'high' => 'Tinggi',
                                    'critical' => 'Kritis',
                                    'unknown' => 'N/A'
                                ];
                            @endphp
                            <tr>
                                <td>{{ $countries->firstItem() + $index }}</td>
                                <td>
                                    <strong>{{ $country->name }}</strong>
                                    <br>
                                    <span class="badge bg-secondary">{{ $country->code }}</span>
                                    @if($country->flag_url && !str_starts_with($country->flag_url, 'http'))
                                        <span style="font-size: 1.2rem;">{{ $country->flag_url }}</span>
                                    @endif
                                </td>
                                <td>{{ $country->region ?? '-' }}</td>
                                <td>
                                    @if($temp !== null)
                                        <span class="fw-bold">{{ round($temp, 1) }}°C</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($wind !== null)
                                        <span>{{ round($wind, 1) }} km/h</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($condition !== '❓ Unknown')
                                        <span>{{ $condition }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $riskBadge[$risk] ?? 'bg-secondary' }}">
                                        {{ $riskLabel[$risk] ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('dashboard') }}?country={{ $country->code }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                                    Tidak ada data cuaca
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- PAGINATION (NEXT/PREVIOUS) -->
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div>
            <span class="text-muted small">
                Menampilkan {{ $countries->firstItem() ?? 0 }} - {{ $countries->lastItem() ?? 0 }} 
                dari {{ $totalCountries }} negara
            </span>
        </div>
        <div>
            {{ $countries->appends(request()->query())->links('pagination::bootstrap-5') }}
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
        font-size: 0.85rem;
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
    .pagination {
        margin-bottom: 0;
    }
</style>
@endpush