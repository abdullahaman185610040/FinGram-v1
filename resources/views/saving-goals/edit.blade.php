<!DOCTYPE html>
<html>
<head>
    <title>Edit Target</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
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

    <h3>Edit Target Tabungan</h3>

    <form method="POST"
          action="{{ url('/saving-goals/'.$goal->id) }}">

        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Nama Target</label>

            <input type="text"
                   name="goal_name"
                   class="form-control"
                   value="{{ $goal->goal_name }}">
        </div>

        <div class="mb-3">
            <label>Nominal Target</label>

            <input type="number"
                   name="target_amount"
                   class="form-control"
                   value="{{ $goal->target_amount }}">
        </div>

        <button class="btn btn-primary">
            Simpan
        </button>

        <a href="/dashboard"
           class="btn btn-secondary">
            Batal
        </a>

    </form>

</div>

</body>
</html>