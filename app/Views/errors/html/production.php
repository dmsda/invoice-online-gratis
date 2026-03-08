<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terjadi Kesalahan - Invoice Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="/css/custom.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #fff0f0;
        }
        .error-card {
            text-align: center;
            padding: 40px;
            max-width: 500px;
        }
        .icon-box {
            font-size: 60px;
            color: #dc3545;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="error-card">
                    <div class="icon-box">⚠️</div>
                    <h2 class="fw-bold text-danger">Whoops! Terjadi Kesalahan.</h2>
                    <p class="text-muted mt-3">
                        Sepertinya ada sedikit masalah teknis di sisi kami. <br>
                        Tim kami akan segera memperbaikinya.
                    </p>
                    <div class="mt-4">
                        <a href="javascript:history.back()" class="btn btn-outline-danger px-4">Coba Lagi</a>
                        <a href="/dashboard" class="btn btn-danger px-4 ms-2">Ke Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
