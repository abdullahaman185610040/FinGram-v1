<!DOCTYPE html>
<html>
<head>
    <title>Login FinGram</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-4">

            <div class="card">
                <div class="card-body">

                    <h4 class="text-center mb-3">🔐 Login Telegram</h4>

                    <form method="POST" action="/login">
                        @csrf

                        <div class="mb-3">
                            <label>Telegram ID</label>
                            <input type="text" name="telegram_id" class="form-control" required>
                        </div>

                        <button class="btn btn-primary w-100">
                            Masuk
                        </button>

                    </form>

                </div>
            </div>

        </div>

    </div>

</div>

</body>
</html>