<!DOCTYPE html>
<html>
<head>
    <title>FinGram Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        body {
            background: #f5f6fa;
        }

        .card h3 {
            font-weight: 700;
        }

        .dashboard-title {
            font-weight: 700;
        }

        .chart-container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">

        <a class="navbar-brand" href="/dashboard">
            💰 FinGram
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="/dashboard">Dashboard</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/saving-goals">🎯 Target Tabungan</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/transactions">Transaksi</a>
                </li>
            </ul>
        </div>

    </div>
</nav>

<div class="container py-4">

    <h3 class="dashboard-title mb-4">
        📊 Dashboard Keuangan
    </h3>
    <!-- LAPORAN BULANAN -->
    <div class="mt-5">
        <div class="row g-3">

            <div class="col-md-4">
                <div class="card text-white bg-success shadow-sm">
                    <div class="card-body">
                        <h6>Pemasukan</h6>
                        <h4>
                            Rp {{ number_format($income,0,',','.') }}
                        </h4>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-white bg-danger shadow-sm">
                    <div class="card-body">
                        <h6>Pengeluaran</h6>
                        <h4>
                            Rp {{ number_format($expense,0,',','.') }}
                        </h4>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-white bg-primary shadow-sm">
                    <div class="card-body">
                        <h6>Tabungan</h6>
                        <h4>
                            Rp {{ number_format($saving,0,',','.') }}
                        </h4>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0"
                     style="background-color:#ffc107;color:#212529;">
                    <div class="card-body">
                        <h6>Saldo</h6>
                        <h4>
                            Rp {{ number_format($saldo,0,',','.') }}
                        </h4>
                    </div>
                </div>
             </div>
        </div>
        <!-- BUTTON -->
        <div class="text-end mt-3">
            <a href="/transactions" class="btn btn-primary">
                📋 Lihat Semua Transaksi
            </a>
        </div>

        <div class="mt-5 chart-container">
            <h5 class="mb-3">
                🏆 Top 5 Pengeluaran Terbesar Bulan Ini
            </h5>
        
            <div class="table-responsive">
            
                <table class="table table-striped">
                
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th>Nominal</th>
                        </tr>
                    </thead>
                
                    <tbody>
                    
                        @forelse($topExpenses as $index => $trx)
                    
                        <tr>
                        
                            <td>
                                @if($index == 0)
                                    🥇
                                @elseif($index == 1)
                                    🥈
                                @elseif($index == 2)
                                    🥉
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </td>
                        
                            <td>
                                {{ $trx->transaction_date }}
                            </td>
                        
                            <td>
                                {{ $trx->category }}
                            </td>
                        
                            <td>
                                {{ $trx->description }}
                            </td>
                        
                            <td class="text-danger fw-bold">
                                Rp {{ number_format($trx->amount,0,',','.') }}
                            </td>
                        
                        </tr>
                    
                        @empty
                    
                        <tr>
                            <td colspan="5" class="text-center">
                                Belum ada data pengeluaran bulan ini
                            </td>
                        </tr>
                    
                        @endforelse
                    
                    </tbody>
                
                </table>
            
                @php
                $totalTopExpense = $topExpenses->sum('amount');
                @endphp

                <div class="alert alert-warning mt-3">
                
                    <strong>
                        Total 5 pengeluaran terbesar:
                    </strong>
                
                    Rp {{ number_format($totalTopExpense,0,',','.') }}
                
                </div>

            </div>
        
        </div>

    </div>

    <!-- CHART -->
    <div class="mt-5">
        <h5>🎯 Progress Target Tabungan</h5>
        @foreach($goals as $goal)
        <div class="card mb-3">
            <div class="card-body">
                <h6>{{ $goal->goal_name }}</h6>
                    <div class="progress">
                        <div class="progress-bar bg-success" style="width: {{ min($goal->progress,100) }}%;">
                            {{ round($goal->progress,1) }}%
                        </div>
                    </div>
                    <div class="mt-2">
                        Rp {{ number_format($goal->saved,0,',','.') }}
                        /
                        Rp {{ number_format($goal->target_amount,0,',','.') }}
                    </div>
            </div>
        </div>
        @endforeach
        <div class="text-end mt-3">
            <a href="/saving-goals" class="btn btn-primary">
                📋 Lihat Tabungan
            </a>
        </div>

    </div>

    <div class="mt-5 chart-container">
        <h5 class="mb-3">📈 Grafik Keuangan</h5>
        <canvas id="financeChart"></canvas>
    </div>

    <div class="mt-5 chart-container">
        <h5>💰 Grafik Saldo Bulanan</h5>
        <canvas id="saldoChart"></canvas>
    </div>

    <div class="mt-5 chart-container">
        <h5>🥧 Pengeluaran Berdasarkan Kategori</h5>
        <canvas id="expensePieChart"></canvas>
    </div>
    
</div>

<!-- SCRIPT -->
<canvas id="saldoChart"></canvas>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const saldoCtx =
    document.getElementById('saldoChart');

    new Chart(saldoCtx, {
        type: 'line',
        data: {
            labels: [
                'Jan','Feb','Mar','Apr','May','Jun',
                'Jul','Aug','Sep','Oct','Nov','Dec'
            ],
            datasets: [{
                label: 'Saldo',
                data: @json($saldoChart),
                tension: 0.3,
                fill: false
            }]
        },
        options: {
            responsive: true
        }
    });
</script>
<script>
const ctx = document.getElementById('financeChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [
            'Jan','Feb','Mar','Apr','May','Jun',
            'Jul','Aug','Sep','Oct','Nov','Dec'
        ],
        datasets: [
            {
                label: 'Pemasukan',
                data: @json($incomeChart),
                backgroundColor: '#28a745'
            },
            {
                label: 'Pengeluaran',
                data: @json($expenseChart),
                backgroundColor: '#dc3545'
            },
            {
                label: 'Tabungan',
                data: @json($savingChart),
                backgroundColor: '#0d6efd'
            },
            {
                label: 'Saldo',
                data: @json($saldoChart),
                backgroundColor: '#ffc107'
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top'
            }
        }
    }
});
</script>

<script>
const pieCtx =
document.getElementById('expensePieChart');

new Chart(pieCtx, {

    type: 'pie',

    data: {

        labels: @json(
            $expenseCategories->pluck('category')
        ),

        datasets: [{
            data: @json(
                $expenseCategories->pluck('total')
            )
        }]
    },

    options: {

        responsive: true,

        plugins: {

            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>