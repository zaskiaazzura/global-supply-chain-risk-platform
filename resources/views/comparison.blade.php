@extends('user.layouts.user')

@section('title', 'Country Comparison')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1><i class="fas fa-arrows-left-right text-primary"></i> Country Comparison</h1>
            <p class="text-muted">Bandingkan dua negara berdasarkan GDP, Inflasi, Risk, dan Mata Uang</p>
        </div>
    </div>

    <!-- Selector -->
    <div class="row mb-4">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Negara 1</h5>
                    <select id="country1" class="form-select">
                        <option value="">-- Pilih --</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->code }}">{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-2 text-center">
            <div class="card h-100 d-flex align-items-center justify-content-center">
                <div class="card-body">
                    <h2 class="text-muted">VS</h2>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Negara 2</h5>
                    <select id="country2" class="form-select">
                        <option value="">-- Pilih --</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->code }}">{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col text-center">
            <button id="compareBtn" class="btn btn-primary btn-lg">
                <i class="fas fa-chart-bar"></i> Bandingkan
            </button>
        </div>
    </div>

    <!-- Hasil Comparison -->
    <div id="comparisonResult" style="display: none;">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 id="c1Name"></h3>
                        <span class="badge bg-secondary" style="font-size: 1.2rem;" id="c1Code"></span>
                        <hr>
                        <p><strong>GDP:</strong> <span id="c1Gdp"></span></p>
                        <p><strong>Inflasi:</strong> <span id="c1Inflation"></span></p>
                        <p><strong>Risk Score:</strong> <span id="c1Risk" class="badge bg-primary"></span></p>
                        <p><strong>Mata Uang:</strong> <span id="c1Currency"></span></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 id="c2Name"></h3>
                        <span class="badge bg-secondary" style="font-size: 1.2rem;" id="c2Code"></span>
                        <hr>
                        <p><strong>GDP:</strong> <span id="c2Gdp"></span></p>
                        <p><strong>Inflasi:</strong> <span id="c2Inflation"></span></p>
                        <p><strong>Risk Score:</strong> <span id="c2Risk" class="badge bg-primary"></span></p>
                        <p><strong>Mata Uang:</strong> <span id="c2Currency"></span></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- CHART -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-chart-bar text-success"></i> Perbandingan Chart</h5>
                        <div style="height: 300px; max-height: 300px; position: relative;">
                            <canvas id="comparisonChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="noDataMessage" class="text-center py-5" style="display: none;">
        <i class="fas fa-info-circle fa-3x text-muted"></i>
        <h4 class="text-muted">Pilih dua negara untuk dibandingkan</h4>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    let comparisonChart = null;

    $('#compareBtn').on('click', function() {
        const code1 = $('#country1').val();
        const code2 = $('#country2').val();

        if (!code1 || !code2) {
            alert('Pilih dua negara terlebih dahulu!');
            return;
        }

        if (code1 === code2) {
            alert('Pilih negara yang berbeda!');
            return;
        }

        $.ajax({
            url: window.baseUrl + '/api/compare',
            method: 'GET',
            data: {
                country1: code1,
                country2: code2
            },
            success: function(response) {
                if (response.success) {
                    displayComparison(response.data);
                } else {
                    alert('Gagal membandingkan negara');
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                alert('Error fetching data: ' + (xhr.responseJSON?.error || 'Unknown error'));
            }
        });
    });

    function displayComparison(data) {
        const responseData = data.data ? data.data : data;
        
        const c1 = responseData.country1;
        const c2 = responseData.country2;

        console.log('Country 1:', c1); 
        console.log('Country 2:', c2);

        // Nama negara
        $('#c1Name').text(c1.name || '-');
        $('#c2Name').text(c2.name || '-');
        
        // Kode negara
        $('#c1Code').text(c1.code || '-');
        $('#c2Code').text(c2.code || '-');

        // TAMPILKAN FLAG (dari flagcdn.com)
        function getFlagUrl(flag, code) {
            if (!flag) return 'https://via.placeholder.com/60x40?text=Flag';
            if (flag.startsWith('http')) return flag;
            const codeLower = code?.toLowerCase() || '';
            return 'https://flagcdn.com/48x36/' + codeLower + '.png';
        }

        $('#c1Flag').attr('src', getFlagUrl(c1.flag, c1.code));
        $('#c2Flag').attr('src', getFlagUrl(c2.flag, c2.code));

        // GDP
        $('#c1Gdp').text(c1.gdp ? '$' + Number(c1.gdp).toLocaleString() : 'N/A');
        $('#c2Gdp').text(c2.gdp ? '$' + Number(c2.gdp).toLocaleString() : 'N/A');

        // Inflasi
        $('#c1Inflation').text(c1.inflation ? c1.inflation + '%' : 'N/A');
        $('#c2Inflation').text(c2.inflation ? c2.inflation + '%' : 'N/A');

        // Risk Score
        $('#c1Risk').text(c1.risk_score ?? 'N/A')
            .removeClass('badge bg-primary bg-danger')
            .addClass(c1.risk_level === 'High' ? 'badge bg-danger' : 'badge bg-primary');
        $('#c2Risk').text(c2.risk_score ?? 'N/A')
            .removeClass('badge bg-primary bg-danger')
            .addClass(c2.risk_level === 'High' ? 'badge bg-danger' : 'badge bg-primary');

        // MATA UANG
        $('#c1Currency').text(c1.currency || 'N/A');
        $('#c2Currency').text(c2.currency || 'N/A');

        // Chart
        updateComparisonChart(c1, c2);
        $('#comparisonResult').show();
        $('#noDataMessage').hide();
    }

    function updateComparisonChart(c1, c2) {
        const ctx = document.getElementById('comparisonChart').getContext('2d');
        
        if (comparisonChart) {
            comparisonChart.destroy();
        }

        const labels = ['GDP (B)', 'Inflation (%)', 'Risk Score'];
        
        const gdp1 = c1.gdp ? Number(c1.gdp) / 1000000000 : 0;
        const gdp2 = c2.gdp ? Number(c2.gdp) / 1000000000 : 0;
        const inf1 = c1.inflation || 0;
        const inf2 = c2.inflation || 0;
        const risk1 = c1.risk_score || 0;
        const risk2 = c2.risk_score || 0;

        const maxGdp = Math.max(gdp1, gdp2, 1);
        const maxInf = Math.max(inf1, inf2, 1);
        const maxRisk = Math.max(risk1, risk2, 1);

        const data1 = [
            (gdp1 / maxGdp) * 100,
            (inf1 / maxInf) * 100,
            (risk1 / maxRisk) * 100
        ];
        const data2 = [
            (gdp2 / maxGdp) * 100,
            (inf2 / maxInf) * 100,
            (risk2 / maxRisk) * 100
        ];

        comparisonChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: c1.name,
                        data: data1,
                        backgroundColor: 'rgba(52, 152, 219, 0.7)',
                        borderColor: '#3498db',
                        borderWidth: 2
                    },
                    {
                        label: c2.name,
                        data: data2,
                        backgroundColor: 'rgba(231, 76, 60, 0.7)',
                        borderColor: '#e74c3c',
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                let realValue;
                                if (context.dataIndex === 0) {
                                    realValue = context.dataset.data === data1 ? gdp1 : gdp2;
                                    return label + ': $' + realValue.toFixed(2) + 'B';
                                } else if (context.dataIndex === 1) {
                                    realValue = context.dataset.data === data1 ? inf1 : inf2;
                                    return label + ': ' + realValue.toFixed(2) + '%';
                                } else {
                                    realValue = context.dataset.data === data1 ? risk1 : risk2;
                                    return label + ': ' + realValue.toFixed(2);
                                }
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush