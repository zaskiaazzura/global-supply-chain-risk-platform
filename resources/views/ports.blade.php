@extends('layouts.app')

@section('title', 'Global Port Location Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h1><i class="fas fa-anchor text-primary"></i> Global Port Location Dashboard</h1>
            <p class="text-muted">Cari dan pantau pelabuhan di seluruh dunia</p>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-search"></i> Cari Pelabuhan
                    </h5>
                    <div class="input-group">
                        <input type="text" id="portSearch" class="form-control" placeholder="Nama pelabuhan...">
                        <button class="btn btn-primary" id="searchPortBtn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-filter"></i> Filter
                    </h5>
                    <select id="countryFilter" class="form-select">
                        <option value="">Semua Negara</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-chart-simple"></i> Statistik
                    </h5>
                    <p class="mb-0">Total Pelabuhan: <span id="totalPorts" class="badge bg-primary">0</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Map -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-map-marked-alt text-danger"></i> Peta Pelabuhan Dunia
                    </h5>
                    <div id="portMap" style="height: 600px; border-radius: 8px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Port List -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-list"></i> Daftar Pelabuhan
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover" id="portTable">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Negara</th>
                                    <th>Kota</th>
                                    <th>Tipe</th>
                                    <th>Ukuran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="portTableBody">
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    #portMap {
        height: 600px;
        border-radius: 8px;
    }
    .port-marker {
        cursor: pointer;
    }
    .port-popup {
        min-width: 200px;
    }
    .port-popup h6 {
        margin-bottom: 5px;
    }
    .port-popup p {
        margin-bottom: 3px;
        font-size: 14px;
    }
    .table td {
        vertical-align: middle;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    console.log('Ports page loaded!');
    console.log('Base URL:', window.baseUrl);

    // 🔥 BASE URL UNTUK API
    const apiBaseUrl = window.baseUrl + '/api';

    let map = null;
    let markersLayer = null;
    let allPorts = [];

    // ========================================
    // 1. LOAD COUNTRY FILTER
    // ========================================
    function loadCountryFilter() {
        $.ajax({
            url: apiBaseUrl + '/countries',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const select = $('#countryFilter');
                    select.append('<option value="">Semua Negara</option>');
                    response.data.forEach(function(country) {
                        select.append('<option value="' + country.code + '">' + country.name + '</option>');
                    });
                }
            }
        });
    }

    // ========================================
    // 2. INIT MAP
    // ========================================
    function initMap() {
        map = L.map('portMap', {
            center: [0, 0],
            zoom: 2
        });

        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        markersLayer = L.layerGroup().addTo(map);
        
        loadPorts();
    }

    // ========================================
    // 3. LOAD PORTS
    // ========================================
    function loadPorts() {
        $.ajax({
            url: apiBaseUrl + '/ports',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    allPorts = response.data;
                    $('#totalPorts').text(allPorts.length);
                    renderPorts(allPorts);
                    renderPortTable(allPorts);
                }
            },
            error: function() {
                alert('Gagal load data pelabuhan');
            }
        });
    }

    // ========================================
    // 4. RENDER PORTS ON MAP
    // ========================================
    function renderPorts(ports) {
        markersLayer.clearLayers();

        ports.forEach(function(port) {
            if (port.latitude && port.longitude) {
                const marker = L.marker([port.latitude, port.longitude], {
                    icon: L.divIcon({
                        className: 'port-marker',
                        html: '⚓',
                        iconSize: [30, 30],
                        iconAnchor: [15, 30]
                    })
                });

                const popupContent = `
                    <div class="port-popup">
                        <h6><strong>${port.name}</strong></h6>
                        <p>📍 ${port.city || '-'}, ${port.country ? port.country.name : '-'}</p>
                        <p>🏷️ ${port.type || '-'} | ${port.size || '-'}</p>
                        <p>🌐 ${port.latitude}, ${port.longitude}</p>
                        <button class="btn btn-sm btn-primary" onclick="focusPort(${port.latitude}, ${port.longitude})">
                            <i class="fas fa-crosshairs"></i> Zoom
                        </button>
                    </div>
                `;

                marker.bindPopup(popupContent);
                markersLayer.addLayer(marker);
            }
        });

        // Fit map to show all markers
        if (ports.length > 0) {
            const group = L.featureGroup(markersLayer.getLayers());
            map.fitBounds(group.getBounds().pad(0.1));
        }
    }

    // ========================================
    // 5. RENDER PORT TABLE
    // ========================================
    function renderPortTable(ports) {
        const tbody = $('#portTableBody');
        tbody.empty();

        if (ports.length === 0) {
            tbody.html('<tr><td colspan="6" class="text-center text-muted">Tidak ada data</td></tr>');
            return;
        }

        ports.forEach(function(port) {
            const row = `
                <tr>
                    <td><strong>${port.name}</strong></td>
                    <td>${port.country ? port.country.name : '-'}</td>
                    <td>${port.city || '-'}</td>
                    <td><span class="badge bg-info">${port.type || '-'}</span></td>
                    <td><span class="badge ${port.size === 'Large' ? 'bg-success' : 'bg-warning'}">${port.size || '-'}</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="focusPort(${port.latitude}, ${port.longitude})">
                            <i class="fas fa-map-pin"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    // ========================================
    // 6. FOCUS PORT
    // ========================================
    window.focusPort = function(lat, lng) {
        if (map) {
            map.setView([lat, lng], 10);
        }
    };

    // ========================================
    // 7. SEARCH PORTS
    // ========================================
    $('#searchPortBtn').on('click', function() {
        const keyword = $('#portSearch').val().toLowerCase();
        const countryCode = $('#countryFilter').val();

        let filtered = allPorts;

        if (keyword) {
            filtered = filtered.filter(function(port) {
                return port.name.toLowerCase().includes(keyword) ||
                       (port.city && port.city.toLowerCase().includes(keyword));
            });
        }

        if (countryCode) {
            filtered = filtered.filter(function(port) {
                return port.country && port.country.code === countryCode;
            });
        }

        renderPorts(filtered);
        renderPortTable(filtered);
        $('#totalPorts').text(filtered.length);
    });

    // Enter key for search
    $('#portSearch').on('keypress', function(e) {
        if (e.which === 13) {
            $('#searchPortBtn').click();
        }
    });

    // Filter change
    $('#countryFilter').on('change', function() {
        $('#searchPortBtn').click();
    });

    // ========================================
    // 8. INIT
    // ========================================
    loadCountryFilter();
    initMap();
});
</script>
@endpush