<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'ClientPulse') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #1d4ed8;
            --font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        body {
            font-family: var(--font-family);
            background: linear-gradient(135deg, #f0f2f5 0%, #e8ecf1 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .auth-wrapper { width: 100%; max-width: 420px; }
        .auth-card {
            background: #fff;
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 36px 32px;
        }
        .auth-logo {
            font-size: 22px;
            font-weight: 700;
            color: #1a2035;
        }
        .auth-logo i { color: var(--primary); }
        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background: #1e40af;
            border-color: #1e40af;
        }
        .form-control {
            border-radius: 8px;
            padding: 10px 14px;
            border-color: #d1d5db;
            font-size: 14px;
        }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(29,78,216,0.1);
        }
        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 4px;
        }
        .auth-footer a { color: var(--primary); }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="text-center mb-4">
            <div class="auth-logo mb-1">
                <i class="bi bi-people-fill me-2"></i>ClientPulse
            </div>
        </div>
        <div class="auth-card">
            @yield('guest-content')
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
