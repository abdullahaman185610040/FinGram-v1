<!DOCTYPE html>
<html>
<head>
    <title>Saving Goals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
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
        <h3>🎯 Target Tabungan</h3>
        <a href="/dashboard" class="btn btn-secondary btn-sm mb-3">
            ⬅ Kembali ke Dashboard
        </a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Target</th>
                    <th>Target</th>
                    <th>Terkumpul</th>
                    <th>Progress</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
            @foreach($goals as $goal)
                <tr>
                    <td>
                        {{ $goal->goal_name }}
                    </td>

                    <td>
                        Rp {{ number_format($goal->target_amount,0,',','.') }}
                    </td>
                
                    <td>
                        Rp {{ number_format($goal->saved,0,',','.') }}
                    </td>

                    <td width="250">
                        <div class="progress">
                            <div
                                class="progress-bar bg-success"
                                style="width: {{ min($goal->progress,100) }}%;">
                                {{ round($goal->progress,1) }}%
                            </div>
                        </div>

                    </td>

                    <td>
                        <a href="/saving-goals/{{ $goal->id }}/edit" class="btn btn-warning btn-sm">Edit</a>
                        <form action="/saving-goals/{{ $goal->id }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus target?')">Hapus</button>
                        </form>
                
                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</body>
</html>