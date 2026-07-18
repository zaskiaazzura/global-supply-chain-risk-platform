@extends('user.layouts.user')

@section('title', 'Route Simulation')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h1><i class="fas fa-route text-primary"></i> Route Simulation</h1>
            <p class="text-muted">Simulasikan rute pengiriman antar negara atau pelabuhan</p>
        </div>
    </div>

    <!-- Form -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form id="routeForm" class="row g-3">
                        <!-- TIPE RUTE -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Tipe Rute</label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="routeType" id="typeCountry" value="country" checked>
                                    <label class="form-check-label fw-semibold" for="typeCountry">
                                        <i class="fas fa-flag text-primary"></i> Negara
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="routeType" id="typePort" value="port">
                                    <label class="form-check-label fw-semibold" for="typePort">
                                        <i class="fas fa-anchor text-success"></i> Pelabuhan
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- DARI -->
                        <div class="col-md-5">
                            <label class="form-label" id="fromLabel">Negara Asal</label>
                            <select id="fromSelect" class="form-select" required>
                                <option value="">-- Pilih --</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->code }}" data-type="country">{{ $country->name }} ({{ $country->code }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- KE -->
                        <div class="col-md-5">
                            <label class="form-label" id="toLabel">Negara Tujuan</label>
                            <select id="toSelect" class="form-select" required>
                                <option value="">-- Pilih --</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->code }}" data-type="country">{{ $country->name }} ({{ $country->code }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- TOMBOL -->
                        <div class="col-md-2 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-play"></i> Simulasikan
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="clearRoute()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Result -->
    <div id="resultContainer" style="display: none;">
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-map-marked-alt text-danger"></i> Rute Peta</h5>
                        <!-- MAP DENGAN LOADING INDICATOR -->
                        <div id="routeMap" style="height: 450px; border-radius: 8px; position: relative; background: #f0f0f0;">
                            <div id="mapLoading" class="text-center py-5" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Memuat peta...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-info-circle text-info"></i> Detail Rute</h5>
                        <hr>
                        <div id="routeDetails">
                            <div class="text-center py-3">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- No Data -->
    <div id="noDataMessage" class="text-center py-5">
        <i class="fas fa-route fa-3x text-muted"></i>
        <h4 class="text-muted">Pilih negara atau pelabuhan asal dan tujuan</h4>
        <p class="text-muted">Klik "Simulasikan" untuk melihat rute</p>
    </div>
</div>
@endsection

@push('styles')
<style>
    #routeMap { height: 450px; border-radius: 8px; }
    .detail-item { 
        display: flex; 
        justify-content: space-between; 
        padding: 8px 0; 
        border-bottom: 1px solid #f0f0f0;
    }
    .detail-item:last-child { border-bottom: none; }
    .detail-label { color: #6c757d; font-weight: 500; }
    .detail-value { font-weight: 600; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
$(document).ready(function() {
    let map = null;
    let routeLayer = null;
    let markerFrom = null;
    let markerTo = null;
    let allPorts = [];
    let mapInitialized = false;

    // ========================================
    // 1. INIT MAP (HANYA 1 KALI)
    // ========================================
    function initMap() {
        if (mapInitialized) return;
        
        const mapContainer = document.getElementById('routeMap');
        if (!mapContainer) {
            console.error('Map container #routeMap not found!');
            return;
        }

        const loading = document.getElementById('mapLoading');
        if (loading) loading.style.display = 'none';

        map = L.map('routeMap', {
            center: [0, 0],
            zoom: 2
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        mapInitialized = true;
        console.log('Map initialized');
    }

    // ========================================
    // 2. LOAD PORTS (HANYA SAAT DIPILIH)
    // ========================================
    let portsLoaded = false;

    function loadPorts() {
        if (portsLoaded) return;
        
        $.ajax({
            url: window.baseUrl + '/api/ports?limit=500',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    allPorts = response.data;
                    portsLoaded = true;
                    populatePortDropdowns();
                }
            },
            error: function(xhr) {
                console.error('Error loading ports:', xhr);
            }
        });
    }

    // ========================================
    // 3. POPULATE PORT DROPDOWNS
    // ========================================
    function populatePortDropdowns() {
        const fromSelect = $('#fromSelect');
        const toSelect = $('#toSelect');
        
        const fromVal = fromSelect.val();
        const toVal = toSelect.val();

        fromSelect.empty();
        toSelect.empty();

        fromSelect.append('<option value="">-- Pilih --</option>');
        toSelect.append('<option value="">-- Pilih --</option>');

        allPorts.forEach(function(port) {
            const countryName = port.country ? port.country.name : 'Unknown';
            const label = port.name + ' (' + countryName + ')';
            const value = port.id;
            fromSelect.append('<option value="' + value + '" data-type="port">' + label + '</option>');
            toSelect.append('<option value="' + value + '" data-type="port">' + label + '</option>');
        });

        if (fromVal) fromSelect.val(fromVal);
        if (toVal) toSelect.val(toVal);
    }

    // ========================================
    // 4. SWITCH ROUTE TYPE
    // ========================================
    function switchRouteType(type) {
        const fromSelect = $('#fromSelect');
        const toSelect = $('#toSelect');
        const fromLabel = $('#fromLabel');
        const toLabel = $('#toLabel');

        if (type === 'country') {
            fromLabel.text('Negara Asal');
            toLabel.text('Negara Tujuan');
            
            const fromVal = fromSelect.val();
            const toVal = toSelect.val();

            fromSelect.empty();
            toSelect.empty();
            fromSelect.append('<option value="">-- Pilih --</option>');
            toSelect.append('<option value="">-- Pilih --</option>');

            @foreach($countries as $country)
                fromSelect.append('<option value="{{ $country->code }}" data-type="country">{{ $country->name }} ({{ $country->code }})</option>');
                toSelect.append('<option value="{{ $country->code }}" data-type="country">{{ $country->name }} ({{ $country->code }})</option>');
            @endforeach

            if (fromVal) fromSelect.val(fromVal);
            if (toVal) toSelect.val(toVal);
        } else {
            fromLabel.text('Pelabuhan Asal');
            toLabel.text('Pelabuhan Tujuan');
            loadPorts(); 
        }
    }

    // ========================================
    // 5. RADIO BUTTON
    // ========================================
    $('input[name="routeType"]').on('change', function() {
        const type = $(this).val();
        if (type === 'port') {
            loadPorts();
        }
        switchRouteType(type);
    });

    // ========================================
    // 6. SUBMIT
    // ========================================
    $('#routeForm').on('submit', function(e) {
        e.preventDefault();

        const type = $('input[name="routeType"]:checked').val();
        const from = $('#fromSelect').val();
        const to = $('#toSelect').val();

        if (!from || !to) {
            alert('Pilih asal dan tujuan!');
            return;
        }

        if (from === to) {
            alert('Pilih lokasi yang berbeda!');
            return;
        }

        initMap();

        $.ajax({
            url: window.baseUrl + '/api/route-simulation',
            method: 'POST',
            data: {
                from: from,
                to: to,
                type: type,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    displayRoute(response);
                } else {
                    alert('Gagal: ' + (response.error || 'Unknown error'));
                }
            },
            error: function(xhr) {
                console.error(xhr);
                alert('Error: ' + (xhr.responseJSON?.error || 'Server error'));
            }
        });
    });

    // ========================================
    // 7. DISPLAY ROUTE
    // ========================================
    function displayRoute(data) {
        if (!map) {
            initMap();
        }

        const from = data.from;
        const to = data.to;
        const distance = data.distance;
        const time = data.time;
        const ports = data.ports || {};

        if (routeLayer) { map.removeLayer(routeLayer); }
        if (markerFrom) { map.removeLayer(markerFrom); }
        if (markerTo) { map.removeLayer(markerTo); }

        const latlngs = [
            [from.lat, from.lng],
            [to.lat, to.lng]
        ];

        routeLayer = L.polyline(latlngs, {
            color: '#3498db',
            weight: 4,
            opacity: 0.8,
            dashArray: '8, 8'
        }).addTo(map);

        const iconFrom = L.divIcon({
            html: '<div style="background:#2ecc71;border-radius:50%;width:16px;height:16px;border:2px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.3);"></div>',
            iconSize: [16, 16],
            iconAnchor: [8, 8]
        });

        markerFrom = L.marker([from.lat, from.lng], { icon: iconFrom })
            .bindPopup('<strong>📍 ' + from.name + '</strong>')
            .addTo(map);

        const iconTo = L.divIcon({
            html: '<div style="background:#e74c3c;border-radius:50%;width:16px;height:16px;border:2px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.3);"></div>',
            iconSize: [16, 16],
            iconAnchor: [8, 8]
        });

        markerTo = L.marker([to.lat, to.lng], { icon: iconTo })
            .bindPopup('<strong>🏁 ' + to.name + '</strong>')
            .addTo(map);

        const bounds = L.latLngBounds(latlngs);
        map.fitBounds(bounds, { padding: [50, 50] });

        $('#resultContainer').show();
        $('#noDataMessage').hide();

        $('#routeDetails').html(`
            <div class="detail-item">
                <span class="detail-label">Dari</span>
                <span class="detail-value">${from.name}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Ke</span>
                <span class="detail-value">${to.name}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Jarak</span>
                <span class="detail-value">${distance.km.toLocaleString()} km</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Pesawat</span>
                <span class="detail-value">${time.flight}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Kapal</span>
                <span class="detail-value">${time.ship}</span>
            </div>
            ${ports.from ? `<div class="detail-item"><span class="detail-label">Pelabuhan Asal</span><span class="detail-value">${ports.from}</span></div>` : ''}
            ${ports.to ? `<div class="detail-item"><span class="detail-label">Pelabuhan Tujuan</span><span class="detail-value">${ports.to}</span></div>` : ''}
            <div class="mt-3 text-center"><span class="badge bg-primary">${distance.nautical} nautical miles</span></div>
        `);
    }

    // ========================================
    // 8. CLEAR ROUTE
    // ========================================
    window.clearRoute = function() {
        if (routeLayer) { map.removeLayer(routeLayer); routeLayer = null; }
        if (markerFrom) { map.removeLayer(markerFrom); markerFrom = null; }
        if (markerTo) { map.removeLayer(markerTo); markerTo = null; }
        $('#resultContainer').hide();
        $('#noDataMessage').show();
        $('#fromSelect').val('');
        $('#toSelect').val('');
    };

    // ========================================
    // 9. INIT (MAP TIDAK LANGSUNG DI-LOAD)
    // ========================================
    console.log('Route Simulation ready. Map will load on demand.');
});
</script>
@endpush