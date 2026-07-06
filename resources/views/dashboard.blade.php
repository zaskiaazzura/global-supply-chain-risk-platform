@extends('layouts.app')

@section('title', 'Global Supply Chain Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h1><i class="fas fa-tachometer-alt text-primary"></i> Global Supply Chain Dashboard</h1>
            <p class="text-muted">Monitor risiko rantai pasok global secara real-time</p>
        </div>
    </div>

    <!-- Country Selector -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-search"></i> Pilih Negara
                    </h5>
                    <div class="input-group">
                        <select id="countrySelect" class="form-select form-select-lg">
                            <option value="">-- Pilih Negara --</option>
                        </select>
                        <button class="btn btn-primary" id="loadCountryBtn">
                            <i class="fas fa-eye"></i> Lihat
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-sync"></i> Auto Update
                    </h5>
                    <p class="text-muted">Data diperbarui setiap 5 menit</p>
                    <span class="badge bg-success" id="lastUpdate">
                        <i class="fas fa-clock"></i> 
                        <span id="updateTime">{{ now()->format('H:i:s') }}</span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Country Details -->
    <div id="countryDetails" style="display: none;">
        <div class="row mb-4">
            <!-- Flag & Name -->
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <img id="countryFlag" src="" alt="Flag" style="width: 80px; height: auto;" class="mb-2">
                        <h3 id="countryName" class="mb-0"></h3>
                        <p class="text-muted" id="countryCapital"></p>
                        <p><span class="badge bg-primary" id="countryCurrency"></span></p>
                    </div>
                </div>
            </div>

            <!-- Economic Data -->
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-chart-line text-success"></i> Ekonomi</h6>
                        <hr>
                        <div class="mb-2">
                            <small class="text-muted">GDP</small>
                            <p class="h5" id="countryGDP">-</p>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Inflasi</small>
                            <p class="h5" id="countryInflation">-</p>
                        </div>
                        <div>
                            <small class="text-muted">Populasi</small>
                            <p class="h5" id="countryPopulation">-</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Weather Data -->
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-cloud-sun text-warning"></i> Cuaca</h6>
                        <hr>
                        <div class="mb-2">
                            <small class="text-muted">Suhu</small>
                            <p class="h5" id="weatherTemp">-</p>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Angin</small>
                            <p class="h5" id="weatherWind">-</p>
                        </div>
                        <div>
                            <small class="text-muted">Kondisi</small>
                            <p class="h5" id="weatherCondition">-</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Risk Score -->
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h6 class="card-title"><i class="fas fa-shield-alt text-danger"></i> Risk Score</h6>
                        <hr>
                        <div class="display-4" id="riskScore">-</div>
                        <span class="badge" id="riskLevel">-</span>
                        <div class="mt-3">
                            <small class="text-muted">Terakhir diperbarui</small>
                            <p class="small" id="riskUpdated">-</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Risk Breakdown -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-chart-pie text-primary"></i> Risk Breakdown
                        </h5>
                        <div style="height: 250px; max-height: 250px;">
                            <canvas id="riskChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Currency Trend Chart -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-chart-line text-primary"></i> Currency Trend
                    </h5>
                    <div style="height: 200px; max-height: 200px;">
                        <canvas id="currencyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-chart-area text-success"></i> GDP & Inflation Trend
                    </h5>
                    <div style="height: 200px; max-height: 200px;">
                        <canvas id="economicChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- No Data Message -->
    <div id="noDataMessage" class="text-center py-5" style="display: none;">
        <i class="fas fa-info-circle fa-3x text-muted"></i>
        <h4 class="text-muted">Pilih negara untuk melihat data</h4>
    </div>

    <!-- Map Section -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-map-marked-alt text-danger"></i> Global Port & Weather Monitoring
                    </h5>
                    <div id="map" style="height: 500px; border-radius: 8px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    #map { height: 500px; border-radius: 8px; }
    .display-4 { font-size: 3rem; font-weight: 700; }
    .card { box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: all 0.3s; }
    .card:hover { box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
    .risk-low { background-color: #28a745 !important; color: white !important; }
    .risk-medium { background-color: #ffc107 !important; color: black !important; }
    .risk-high { background-color: #fd7e14 !important; color: white !important; }
    .risk-critical { background-color: #dc3545 !important; color: white !important; }
    .badge { font-size: 0.9rem; padding: 0.5rem 1rem; }
    .port-marker { cursor: pointer; }
    .weather-marker { cursor: pointer; }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
$(document).ready(function() {
    console.log('Dashboard loaded!');
    console.log('Base URL:', window.baseUrl);

    const apiBaseUrl = window.baseUrl + '/api';
    
    // ========================================
    // 1. LOAD COUNTRY LIST
    // ========================================
    function loadCountries() {
        $.ajax({
            url: apiBaseUrl + '/countries',
            method: 'GET',
            success: function(response) {
                console.log('Countries loaded:', response);
                if (response.success) {
                    const select = $('#countrySelect');
                    select.empty();
                    select.append('<option value="">-- Pilih Negara --</option>');
                    
                    response.data.forEach(function(country) {
                        select.append('<option value="' + country.code + '">' + country.name + ' (' + country.code + ')</option>');
                    });

                    if (response.data.length > 0) {
                        const firstCode = response.data[0].code;
                        select.val(firstCode);
                        loadCountryDetails(firstCode);
                        markersLayer.clearLayers();
                        loadPorts();
                        loadWeatherMarkers(firstCode);
                    }
                }
            },
            error: function(xhr) {
                console.error('Gagal load negara:', xhr);
            }
        });
    }

    // ========================================
    // 2. LOAD COUNTRY DETAILS
    // ========================================
    function loadCountryDetails(code) {
        if (!code) {
            $('#countryDetails').hide();
            $('#noDataMessage').show();
            return;
        }

        $('#countryDetails').hide();
        $('#noDataMessage').hide();

        $.ajax({
            url: apiBaseUrl + '/countries/' + code,
            method: 'GET',
            success: function(response) {
                console.log('Country details:', response);
                if (response.success) {
                    displayCountryData(response.data);
                    $('#countryDetails').show();
                }
            },
            error: function(xhr) {
                console.error('Gagal load detail:', xhr);
                alert('Gagal mengambil data');
            }
        });
    }

    // ========================================
    // 3. DISPLAY COUNTRY DATA
    // ========================================
    function displayCountryData(data) {
        const country = data.country || {};
        const economic = data.economic || {};
        const weather = data.weather || {};
        const risk = data.risk || {};

        // Basic info
        $('#countryName').text(country.name || '-');
        $('#countryCapital').text('🏛️ ' + (country.capital || '-'));
        $('#countryCurrency').text(country.currency || '-');
        
        if (country.flag_url) {
            $('#countryFlag').attr('src', country.flag_url);
        } else {
            $('#countryFlag').attr('src', 'https://via.placeholder.com/80x50?text=Flag');
        }

        // Economic data
        const gdp = economic.gdp ? '$' + Number(economic.gdp).toLocaleString() : '-';
        $('#countryGDP').text(gdp);
        $('#countryInflation').text(economic.inflation ? economic.inflation + '%' : '-');
        $('#countryPopulation').text(economic.population ? Number(economic.population).toLocaleString() : '-');

        // Weather data
        if (weather && weather.current_weather) {
            $('#weatherTemp').text(weather.current_weather.temperature + '°C');
            $('#weatherWind').text(weather.current_weather.windspeed + ' km/h');
            
            const weatherCodes = {
                0: '☀️ Cerah', 1: '🌤️ Sebagian Cerah', 2: '⛅ Berawan', 3: '☁️ Mendung',
                45: '🌫️ Kabut', 48: '🌫️ Kabut Embun',
                51: '🌧️ Gerimis Ringan', 53: '🌧️ Gerimis Sedang', 55: '🌧️ Gerimis Deras',
                61: '🌧️ Hujan Ringan', 63: '🌧️ Hujan Sedang', 65: '🌧️ Hujan Deras',
                71: '🌨️ Salju Ringan', 73: '🌨️ Salju Sedang', 75: '🌨️ Salju Deras',
                80: '🌦️ Hujan Ringan', 81: '🌧️ Hujan Sedang', 82: '⛈️ Hujan Deras',
                95: '⛈️ Badai Petir', 96: '⛈️ Badai Petir + Hujan Es', 99: '⛈️ Badai Petir + Hujan Es Deras'
            };
            const code = weather.current_weather.weathercode || 0;
            $('#weatherCondition').text(weatherCodes[code] || '❓ Tidak diketahui');
        } else {
            $('#weatherTemp').text('-');
            $('#weatherWind').text('-');
            $('#weatherCondition').text('-');
        }

        // Risk data
        if (risk && risk.total_risk_score !== undefined) {
            const score = risk.total_risk_score;
            const level = risk.risk_level || 'Unknown';
            
            $('#riskScore').text(score);
            
            let badgeClass = 'risk-low';
            if (level === 'Critical') badgeClass = 'risk-critical';
            else if (level === 'High') badgeClass = 'risk-high';
            else if (level === 'Medium') badgeClass = 'risk-medium';
            
            $('#riskLevel').text(level)
                .removeClass('risk-low risk-medium risk-high risk-critical')
                .addClass(badgeClass);
            
            $('#riskUpdated').text(risk.calculated_at ? new Date(risk.calculated_at).toLocaleString() : '-');

            // Risk breakdown chart
            const riskFactors = risk.risk_factors || {};
            const labels = ['Weather', 'Inflation', 'Political', 'Currency', 'Logistics'];
            const values = [
                riskFactors.weather || 0,
                riskFactors.inflation || 0,
                riskFactors.political || 0,
                riskFactors.currency || 0,
                riskFactors.logistics || 0
            ];
            updateRiskChart(labels, values);
        } else {
            $('#riskScore').text('-');
            $('#riskLevel').text('N/A');
            $('#riskUpdated').text('-');
        }

        // Currency chart
        if (country.currency) {
            loadCurrencyChart(country.currency);
        }
        
        // Economic chart
        loadEconomicChart(country.code);
    }

    // ========================================
    // 4. RISK CHART
    // ========================================
    let riskChartInstance = null;

    function updateRiskChart(labels, data) {
        const ctx = document.getElementById('riskChart').getContext('2d');
        
        if (riskChartInstance) {
            riskChartInstance.destroy();
        }

        const colors = ['#28a745', '#ffc107', '#dc3545', '#fd7e14', '#6c757d'];
        
        riskChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Risk Score',
                    data: data,
                    backgroundColor: colors,
                    borderColor: colors.map(c => c),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    // ========================================
    // 5. CURRENCY TREND CHART
    // ========================================
    let currencyChartInstance = null;

    function loadCurrencyChart(code) {
        $.ajax({
            url: apiBaseUrl + '/currency/historical/' + code + '?days=30',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    const labels = data.map(function(d) { return d.date; });
                    const rates = data.map(function(d) { return d.rate; });
                    
                    const ctx = document.getElementById('currencyChart').getContext('2d');
                    
                    if (currencyChartInstance) {
                        currencyChartInstance.destroy();
                    }
                    
                    currencyChartInstance = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: code + '/USD Rate',
                                data: rates,
                                borderColor: '#3498db',
                                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: true }
                            },
                            scales: {
                                y: { beginAtZero: false }
                            }
                        }
                    });
                }
            }
        });
    }

    // ========================================
    // 6. ECONOMIC TREND CHART
    // ========================================
    let economicChartInstance = null;

    function loadEconomicChart(code) {
        const labels = ['2019', '2020', '2021', '2022', '2023'];
        const gdpData = [100, 95, 105, 110, 115];
        const inflationData = [2.5, 3.0, 4.5, 6.0, 3.8];
        
        const ctx = document.getElementById('economicChart').getContext('2d');
        
        if (economicChartInstance) {
            economicChartInstance.destroy();
        }
        
        economicChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'GDP (Index)',
                        data: gdpData,
                        backgroundColor: 'rgba(46, 204, 113, 0.6)',
                        borderColor: '#2ecc71',
                        borderWidth: 2
                    },
                    {
                        label: 'Inflation (%)',
                        data: inflationData,
                        backgroundColor: 'rgba(231, 76, 60, 0.6)',
                        borderColor: '#e74c3c',
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // ========================================
    // 7. INIT MAP
    // ========================================
    let map = null;
    let markersLayer = null;

    function initMap() {
        map = L.map('map', {
            center: [0, 0],
            zoom: 2,
            worldCopyJump: true
        });

        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>, &copy; CartoDB'
        }).addTo(map);

        markersLayer = L.layerGroup().addTo(map);
        loadPorts();
    }

    // ========================================
    // 8. LOAD PORTS
    // ========================================
    function loadPorts() {
        $.ajax({
            url: apiBaseUrl + '/ports',
            method: 'GET',
            success: function(response) {
                if (response.success && response.data) {
                    response.data.forEach(function(port) {
                        if (port.latitude && port.longitude) {
                            const marker = L.marker([port.latitude, port.longitude], {
                                icon: L.divIcon({
                                    className: 'port-marker',
                                    html: '⚓',
                                    iconSize: [30, 30],
                                    iconAnchor: [15, 30]
                                })
                            });

                            const popupContent = '<div style="min-width: 200px;"><h6><strong>' + port.name + '</strong></h6><p class="mb-1">📍 ' + (port.city || '-') + ', ' + (port.country ? port.country.name : '-') + '</p><p class="mb-1">🏷️ ' + (port.type || '-') + ' | ' + (port.size || '-') + '</p><button class="btn btn-sm btn-primary" onclick="showCountry(\'' + (port.country ? port.country.code : '') + '\')"><i class="fas fa-eye"></i> Lihat Negara</button></div>';
                            marker.bindPopup(popupContent);
                            markersLayer.addLayer(marker);
                        }
                    });
                }
            },
            error: function() {
                console.error('Gagal load ports');
            }
        });
    }

    // ========================================
    // 9. LOAD WEATHER MARKERS
    // ========================================
    function loadWeatherMarkers(code) {
        if (code) {
            $.ajax({
                url: apiBaseUrl + '/countries/' + code,
                method: 'GET',
                success: function(response) {
                    if (response.success && response.data.weather) {
                        const country = response.data.country;
                        const weather = response.data.weather;
                        
                        if (country.latitude && country.longitude && weather && weather.current_weather) {
                            const temp = weather.current_weather.temperature;
                            const wind = weather.current_weather.windspeed;
                            const wcode = weather.current_weather.weathercode || 0;
                            
                            let icon = '☀️';
                            let color = '#f39c12';
                            if (wcode >= 95) { icon = '⛈️'; color = '#e74c3c'; }
                            else if (wcode >= 61) { icon = '🌧️'; color = '#3498db'; }
                            else if (wcode >= 71) { icon = '🌨️'; color = '#bdc3c7'; }
                            else if (wcode >= 45) { icon = '🌫️'; color = '#95a5a6'; }
                            else if (wcode >= 3) { icon = '☁️'; color = '#7f8c8d'; }
                            
                            const marker = L.marker([country.latitude, country.longitude], {
                                icon: L.divIcon({
                                    className: 'weather-marker',
                                    html: '<div style="background: ' + color + '; border-radius: 50%; padding: 8px; font-size: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">' + icon + '</div>',
                                    iconSize: [40, 40],
                                    iconAnchor: [20, 20]
                                })
                            });

                            const popupContent = '<div style="min-width: 180px; text-align: center;"><h5><strong>' + country.name + '</strong></h5><p class="display-6 mb-0">' + temp + '°C</p><p class="mb-1">💨 ' + wind + ' km/h</p><p class="text-muted small">' + new Date().toLocaleString() + '</p></div>';
                            marker.bindPopup(popupContent);
                            markersLayer.addLayer(marker);
                            map.setView([country.latitude, country.longitude], 5);
                        }
                    }
                }
            });
        }
    }

    // ========================================
    // 10. SHOW COUNTRY FROM MAP
    // ========================================
    window.showCountry = function(code) {
        if (code) {
            $('#countrySelect').val(code);
            loadCountryDetails(code);
        }
    };

    // ========================================
    // 11. EVENT HANDLERS
    // ========================================
    $('#loadCountryBtn').on('click', function() {
        const code = $('#countrySelect').val();
        loadCountryDetails(code);
        if (code) {
            markersLayer.clearLayers();
            loadPorts();
            loadWeatherMarkers(code);
        }
    });

    $('#countrySelect').on('change', function() {
        const code = $(this).val();
        if (code) {
            loadCountryDetails(code);
            markersLayer.clearLayers();
            loadPorts();
            loadWeatherMarkers(code);
        }
    });

    // ========================================
    // 12. INIT
    // ========================================
    loadCountries();
    initMap();

    function updateTime() {
        $('#updateTime').text(new Date().toLocaleTimeString());
    }
    setInterval(updateTime, 10000);

});
</script>
@endpush