@extends('user.layouts.user')

@section('title', 'Currency Impact Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1><i class="fas fa-dollar-sign text-success"></i> Currency Impact Dashboard</h1>
            <p class="text-muted">Pantau nilai tukar mata uang dan dampaknya terhadap supply chain</p>
        </div>
    </div>

    <!-- ✅ TAMBAHKAN: PENCARIAN NEGARA -->
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
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-info-circle text-info"></i> Mata Uang Aktif
                    </h5>
                    <h3 id="activeCurrencyDisplay">IDR</h3>
                    <p class="text-muted" id="activeCountryDisplay">Indonesia</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Chart -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-chart-line text-primary"></i> 
                        <span id="chartTitle">IDR/USD</span> Trend (30 Hari)
                    </h5>
                    <div style="height: 350px; max-height: 350px; position: relative;">
                        <canvas id="currencyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Rates -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-coins text-warning"></i> Current Rates
                    </h5>
                    <hr>
                    <div id="currencyRates">
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
        max-height: 350px;
    }
    .bg-primary-light {
        background-color: #e8f4fd;
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
                        select.append('<option value="' + country.code + '" data-currency="' + (country.currency || 'IDR') + '">' + country.name + ' (' + country.currency + ')</option>');
                    });

                    // Auto-load default country (dari session atau parameter)
                    const urlParams = new URLSearchParams(window.location.search);
                    const defaultCountry = urlParams.get('country') || 'IDN';
                    select.val(defaultCountry);
                    loadCurrencyData(defaultCountry);
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
            return;
        }

        // Ambil data negara
        $.ajax({
            url: apiBaseUrl + '/countries/' + countryCode,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const country = response.data.country;
                    const currency = country.currency || 'IDR';
                    
                    currentCurrency = currency;
                    currentCountry = countryCode;
                    
                    // Update tampilan
                    $('#activeCurrencyDisplay').text(currency);
                    $('#activeCountryDisplay').text(country.name + ' (' + countryCode + ')');
                    $('#chartTitle').text(currency + '/USD');
                    
                    // Load chart & rates
                    loadCurrencyRates();
                    loadCurrencyChart(currency);
                }
            },
            error: function() {
                alert('Gagal mengambil data negara');
            }
        });
    }

    // ========================================
    // 3. LOAD CURRENCY RATES
    // ========================================
    function loadCurrencyRates() {
        $.ajax({
            url: apiBaseUrl + '/currency',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    let html = '';
                    const rates = response.data;
                    const currencies = ['USD', 'EUR', 'GBP', 'JPY', 'IDR', 'SGD', 'AUD', 'CNY', 'INR', 'MYR', 'PHP', 'THB', 'VND'];
                    
                    currencies.forEach(function(code) {
                        if (rates[code]) {
                            const value = rates[code];
                            const isSelected = (code === currentCurrency);
                            html += `
                                <div class="d-flex justify-content-between align-items-center border-bottom py-2 ${isSelected ? 'bg-primary text-white' : ''}">
                                    <strong>${code} ${isSelected ? '⭐' : ''}</strong>
                                    <span>${value.toFixed(4)}</span>
                                </div>
                            `;
                        }
                    });
                    
                    $('#currencyRates').html(html || '<p class="text-muted text-center">Tidak ada data</p>');
                }
            },
            error: function() {
                $('#currencyRates').html('<p class="text-danger text-center">Gagal load data</p>');
            }
        });
    }

    // ========================================
    // 4. LOAD CURRENCY CHART
    // ========================================
    function loadCurrencyChart(currency) {
        const code = currency || 'IDR';
        
        $.ajax({
            url: apiBaseUrl + '/currency/historical/' + code + '?days=30',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    if (!data || data.length === 0) {
                        $('#currencyChart').parent().html('<p class="text-center text-muted">Tidak ada data untuk ' + code + '</p>');
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
                                pointRadius: 2,
                                pointHoverRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { 
                                    display: true,
                                    position: 'top'
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        maxTicksLimit: 15,
                                        maxRotation: 45
                                    }
                                },
                                y: { 
                                    beginAtZero: false
                                }
                            }
                        }
                    });
                }
            },
            error: function() {
                $('#currencyChart').parent().html('<p class="text-center text-danger">Gagal memuat data chart</p>');
            }
        });
    }

    // ========================================
    // 5. EVENT HANDLERS
    // ========================================
    $('#loadCountryBtn').on('click', function() {
        const countryCode = $('#countrySelect').val();
        if (countryCode) {
            loadCurrencyData(countryCode);
            // Update URL dengan parameter
            const newUrl = window.location.pathname + '?country=' + countryCode;
            window.history.pushState({}, '', newUrl);
        } else {
            alert('Pilih negara terlebih dahulu!');
        }
    });

    $('#countrySelect').on('change', function() {
        // Auto-load saat dropdown berubah
        const countryCode = $(this).val();
        if (countryCode) {
            loadCurrencyData(countryCode);
        }
    });

    // ========================================
    // 6. INIT
    // ========================================
    loadCountries();
});
</script>
@endpush