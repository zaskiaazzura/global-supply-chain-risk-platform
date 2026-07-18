@extends('user.layouts.user')

@section('title', 'Currency Impact Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h1><i class="fas fa-dollar-sign text-success"></i> Currency Impact Dashboard</h1>
            <p class="text-muted">Pantau nilai tukar mata uang dan dampaknya terhadap supply chain</p>
        </div>
    </div>

    <!-- Pilih Negara -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-search"></i> Pilih Negara
                    </h5>
                    <div class="input-group">
                        <select id="countrySelect" class="form-select">
                            <option value="">-- Pilih Negara --</option>
                        </select>
                        <button class="btn btn-primary" id="loadCountryBtn">
                            <i class="fas fa-eye"></i> Tampilkan
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body d-flex align-items-center">
                    <div>
                        <h5 class="card-title">
                            <i class="fas fa-info-circle text-info"></i> Mata Uang Aktif
                        </h5>
                        <h3 id="activeCurrencyDisplay" class="mb-0">-</h3>
                        <p class="text-muted mb-0" id="activeCountryDisplay">Pilih negara</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-chart-line text-primary"></i> 
                        <span id="chartTitle">Pilih negara untuk melihat chart</span>
                    </h5>
                    <div style="height: 400px; max-height: 400px; position: relative;">
                        <canvas id="currencyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Tambahan -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-info-circle text-info"></i> Currency Impact on Supply Chain
                    </h5>
                    <p class="text-muted">
                        Perubahan nilai tukar mata uang dapat mempengaruhi biaya impor/ekspor, 
                        harga bahan baku, dan margin keuntungan. Pantau tren untuk mengantisipasi risiko.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    #currencyChart {
        max-height: 400px;
    }
    .card {
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    const baseUrl = window.baseUrl;
    const apiBaseUrl = baseUrl + '/api';
    let chartInstance = null;
    let currentCurrency = 'IDR';
    let currentCountry = 'IDN';

    // ========================================
    // 1. LOAD COUNTRY LIST
    // ========================================
    function loadCountries() {
        $.ajax({
            url: apiBaseUrl + '/countries',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const select = $('#countrySelect');
                    select.empty();
                    select.append('<option value="">-- Pilih Negara --</option>');
                    
                    response.data.forEach(function(country) {
                        select.append('<option value="' + country.code + '" data-currency="' + (country.currency || 'IDR') + '">' + country.name + ' (' + (country.currency || 'IDR') + ')</option>');
                    });

                    // Cek parameter URL
                    const urlParams = new URLSearchParams(window.location.search);
                    const defaultCountry = urlParams.get('country');
                    if (defaultCountry) {
                        select.val(defaultCountry);
                        loadCurrencyData(defaultCountry);
                    }
                }
            },
            error: function() {
                console.error('Gagal load negara');
            }
        });
    }

    // ========================================
    // 2. LOAD CURRENCY DATA
    // ========================================
    function loadCurrencyData(countryCode) {
        if (!countryCode) {
            $('#activeCurrencyDisplay').text('-');
            $('#activeCountryDisplay').text('Pilih negara');
            $('#chartTitle').text('Pilih negara untuk melihat chart');
            if (chartInstance) {
                chartInstance.destroy();
                chartInstance = null;
            }
            return;
        }

        $.ajax({
            url: apiBaseUrl + '/countries/' + countryCode,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const country = response.data.country;
                    const currency = country.currency || 'IDR';
                    
                    currentCurrency = currency;
                    currentCountry = countryCode;
                    
                    $('#activeCurrencyDisplay').text(currency);
                    $('#activeCountryDisplay').text(country.name + ' (' + countryCode + ')');
                    $('#chartTitle').text(currency + '/USD Trend (30 Hari)');
                    
                    loadCurrencyChart(currency);
                }
            },
            error: function() {
                alert('Gagal mengambil data negara');
            }
        });
    }

    // ========================================
    // 3. LOAD CURRENCY CHART
    // ========================================
    function loadCurrencyChart(currency) {
        const code = currency || 'IDR';
        
        // Tampilkan loading
        const chartContainer = $('#currencyChart').parent();
        chartContainer.html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
        
        // Buat canvas baru di dalam container
        chartContainer.html('<canvas id="currencyChart" style="width:100%;height:100%;"></canvas>');
        
        $.ajax({
            url: apiBaseUrl + '/currency/historical/' + code + '?days=30',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    if (!data || data.length === 0) {
                        chartContainer.html('<p class="text-center text-muted py-5">Tidak ada data untuk ' + code + '</p>');
                        return;
                    }
                    
                    const labels = data.map(function(d) { return d.date; });
                    const rates = data.map(function(d) { return d.rate; });
                    
                    const ctx = document.getElementById('currencyChart').getContext('2d');
                    if (chartInstance) {
                        chartInstance.destroy();
                    }
                    
                    chartInstance = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: code + '/USD Rate',
                                data: rates,
                                borderColor: '#3498db',
                                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                                fill: true,
                                tension: 0.4,
                                pointRadius: 3,
                                pointBackgroundColor: '#3498db',
                                pointHoverRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { 
                                    display: true,
                                    position: 'top',
                                    labels: {
                                        boxWidth: 12,
                                        padding: 15,
                                        font: { size: 13 }
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return 'Rate: ' + context.parsed.y.toFixed(6);
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        maxTicksLimit: 12,
                                        maxRotation: 45,
                                        font: { size: 10 }
                                    },
                                    grid: {
                                        display: false
                                    }
                                },
                                y: { 
                                    beginAtZero: false,
                                    ticks: {
                                        font: { size: 11 }
                                    },
                                    grid: {
                                        color: 'rgba(0,0,0,0.05)'
                                    }
                                }
                            }
                        }
                    });
                }
            },
            error: function() {
                chartContainer.html('<p class="text-center text-danger py-5">Gagal memuat data chart</p>');
            }
        });
    }

    // ========================================
    // 4. EVENT HANDLERS
    // ========================================
    $('#loadCountryBtn').on('click', function() {
        const countryCode = $('#countrySelect').val();
        if (countryCode) {
            loadCurrencyData(countryCode);
            const newUrl = window.location.pathname + '?country=' + countryCode;
            window.history.pushState({}, '', newUrl);
        } else {
            alert('Pilih negara terlebih dahulu!');
        }
    });

    $('#countrySelect').on('change', function() {
        const countryCode = $(this).val();
        if (countryCode) {
            loadCurrencyData(countryCode);
        } else {
            $('#activeCurrencyDisplay').text('-');
            $('#activeCountryDisplay').text('Pilih negara');
            $('#chartTitle').text('Pilih negara untuk melihat chart');
            if (chartInstance) {
                chartInstance.destroy();
                chartInstance = null;
            }
        }
    });

    // ========================================
    // 5. INIT
    // ========================================
    loadCountries();
});
</script>
@endpush