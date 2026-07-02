@extends('layouts.app')

@content
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Global Country Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <select id="countrySelect" class="form-select" style="width: 250px;">
            <option value="">-- Pilih Negara --</option>
        </select>
    </div>
</div>

<div class="row" id="dashboardContent" style="display: none;">
    <div class="col-md-3 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">GDP (Produk Domestik Bruto)</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800" id="gdpValue">-</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Inflasi</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800" id="inflationValue">-</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Populasi</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800" id="populationValue">-</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Mata Uang</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800" id="currencyValue">-</div>
            </div>
        </div>
    </div>
</div>
@endcontent

@stack('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const countrySelect = document.getElementById('countrySelect');
        const dashboardContent = document.getElementById('dashboardContent');

        // 1. Ambil data negara lewat AJAX untuk mengisi Dropdown select
        axios.get('/api/countries')
            .then(function (response) {
                const countries = response.data.data;
                countries.forEach(country => {
                    const option = document.createElement('option');
                    option.value = country.id;
                    option.textContent = country.name;
                    countrySelect.appendChild(option);
                });
            })
            .catch(function (error) {
                console.error("Gagal memuat data negara:", error);
            });

        // 2. Event Listener saat negara dipilih
        countrySelect.addEventListener('change', function() {
            const countryId = this.value;
            if(countryId) {
                // Tampilkan container dashboard
                dashboardContent.style.display = 'flex';
                
                // Catatan: Pada Sesi 3 ini dashboard baru *siap* menampilkan data. 
                // Proses pengisian data dinamis (World Bank / Cuaca) akan ditembak lewat API di Sesi 4 & 5.
                document.getElementById('gdpValue').innerText = "Loading...";
                document.getElementById('inflationValue').innerText = "Loading...";
            } else {
                dashboardContent.style.display = 'none';
            }
        });
    });
</script>
@endpush