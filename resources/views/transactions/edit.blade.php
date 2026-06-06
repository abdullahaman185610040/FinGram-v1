<!DOCTYPE html>
<html>
<head>
    <title>Edit Transaksi</title>
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

    <h3>Edit Transaksi</h3>

    <form method="POST" action="/transactions/{{ $transaction->id }}">
        @csrf
        @method('PUT')

        <div class="mb-2">
            <label>Type</label>
            <select name="type" class="form-control">
                <option value="income" {{ $transaction->type=='income'?'selected':'' }}>Income</option>
                <option value="expense" {{ $transaction->type=='expense'?'selected':'' }}>Expense</option>
                <option value="saving" {{ $transaction->type=='saving'?'selected':'' }}>Saving</option>
            </select>
        </div>

        <div class="mb-2">
            <label>Nominal</label>
            <input type="number" name="amount" value="{{ $transaction->amount }}" class="form-control">
        </div>

        <div class="mb-2">
            <label>Deskripsi</label>
            <input type="text" name="description" value="{{ $transaction->description }}" class="form-control">
        </div>

        <button class="btn btn-success">Update</button>
    </form>

</div>

</body>
</html>