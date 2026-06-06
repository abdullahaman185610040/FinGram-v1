<!DOCTYPE html>
<html>
<head>
    <title>Transactions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .pagination svg{
        width: 16px !important;
        height: 16px !important;
        }

        .pagination{
            margin-bottom:0;
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

    <div class="container mt-4">

    <h3 class="mb-3">📋 Daftar Transaksi</h3>

    <a href="/dashboard" class="btn btn-secondary btn-sm mb-3">
        ⬅ Kembali ke Dashboard
    </a>

    <div class="card">
        <div class="card-body p-0">
            <form method="GET" class="row g-2 mb-4">

                <div class="col-md-2">
                    <select name="month" class="form-control">
                    
                        <option value="">
                            Semua Bulan
                        </option>
                    
                        @for($i=1;$i<=12;$i++)
                            <option
                                value="{{ $i }}"
                                {{ request('month') == $i ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m',$i)->format('F') }}
                            </option>
                        @endfor
                        
                    </select>
                </div>
            
                <div class="col-md-2">
                    <select name="type" class="form-control">
                    
                        <option value="">
                            Semua Tipe
                        </option>
                    
                        <option value="income"
                            {{ request('type') == 'income' ? 'selected' : '' }}>
                            Income
                        </option>
                    
                        <option value="expense"
                            {{ request('type') == 'expense' ? 'selected' : '' }}>
                            Expense
                        </option>
                    
                        <option value="saving"
                            {{ request('type') == 'saving' ? 'selected' : '' }}>
                            Saving
                        </option>
                    
                    </select>
                </div>
            
                <div class="col-md-3">
                    <select name="category" class="form-control">
                    
                        <option value="">
                            Semua Kategori
                        </option>
                    
                        <option value="Pendapatan">
                            Pendapatan
                        </option>
                    
                        <option value="Makanan & Minuman">
                            Makanan & Minuman
                        </option>
                    
                        <option value="Transportasi">
                            Transportasi
                        </option>
                    
                        <option value="Tagihan">
                            Tagihan
                        </option>
                    
                        <option value="Tabungan">
                            Tabungan
                        </option>
                    
                        <option value="Lainnya">
                            Lainnya
                        </option>
                    
                    </select>
                </div>
            
                <div class="col-md-2">
                    <select name="per_page" class="form-control">
                    
                        <option value="10">10 Data</option>
                        <option value="20">20 Data</option>
                        <option value="50">50 Data</option>
                        <option value="100">100 Data</option>
                        <option value="999999">Semua</option>
                    
                    </select>
                </div>
            
                <div class="col-md-3">
                
                    <button class="btn btn-primary">
                        🔍 Filter
                    </button>
                
                    <a href="/transactions"
                       class="btn btn-secondary">
                        Reset
                    </a>
                
                </div>
            
            </form>

            <div class="alert alert-info">

                <b>Total Transaksi:</b>
                {{ $transactions->total() }}

                <br>

                <b>Total Nominal:</b>
                Rp {{ number_format($totalNominal,0,',','.') }}

            </div>

            <table class="table table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Tipe</th>
                        <th>Kategori</th>
                        <th>Nominal</th>
                        <th>Deskripsi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($transactions as $t)
                    <tr>
                        <td>{{ ($transactions->currentPage()-1) * $transactions->perPage() + $loop->iteration }}</td>
                        <td>{{ $t->transaction_date }}</td>
                        <td>
                            @if($t->type == 'income')
                                🟢 Income
                            @elseif($t->type == 'expense')
                                🔴 Expense
                            @else
                                🟡 Saving
                            @endif
                        </td>

                        <td>{{ $t->category }}</td>

                        <td>
                            Rp {{ number_format($t->amount,0,',','.') }}
                        </td>

                        <td>{{ $t->description }}</td>

                        <td>
                            <a href="/transactions/{{ $t->id }}/edit"
                               class="btn btn-warning btn-sm">
                                Edit
                            </a>

                            <form action="/transactions/{{ $t->id }}"
                                  method="POST"
                                  style="display:inline;">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-danger btn-sm"
                                        onclick="return confirm('Hapus transaksi ini?')">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>

            </table>

        </div>
    </div>

    <div class="mt-3">
        {{ $transactions->links('pagination::bootstrap-5') }}
    </div>

</div>

</body>
</html>