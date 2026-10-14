<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — {{ __('erp.app_name') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .login-card {
            width: 100%;
            max-width: 480px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .login-header {
            background: linear-gradient(135deg, #2563eb, #0284c7);
            color: #ffffff;
            padding: 2.2rem 2rem;
            text-align: center;
        }

        .login-body {
            padding: 2.2rem;
        }

        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37,99,235,0.15);
        }

        .demo-btn {
            font-size: 0.78rem;
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
            text-align: left;
            transition: all 0.2s;
        }

        .demo-btn:hover {
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="d-inline-flex p-3 rounded-circle bg-white text-primary mb-3 shadow-sm">
                <i class="bi bi-building-gear fs-2"></i>
            </div>
            <h4 class="fw-bold mb-1">{{ __('erp.app_name') }}</h4>
            <p class="mb-0 opacity-75 small">H Tech Provision IT — Construction ERP Portal</p>
        </div>

        <div class="login-body">
            @if($errors->any())
            <div class="alert alert-danger py-2 small mb-4">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $errors->first() }}
            </div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" id="emailInput" class="form-control border-start-0 ps-0" placeholder="user@htech.com.bd" value="{{ old('email', 'pm@htech.com.bd') }}" required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" id="passwordInput" class="form-control border-start-0 ps-0" placeholder="••••••••" value="password" required>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                        <label class="form-check-label small text-muted" for="remember">
                            Remember Me
                        </label>
                    </div>
                    <span class="small text-muted fw-semibold">
                        <i class="bi bi-globe me-1"></i> English
                    </span>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm mb-4">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                </button>
            </form>

            <!-- Quick Demo Credentials Picker -->
            <div class="border-top pt-3">
                <p class="text-muted small fw-bold mb-2">
                    <i class="bi bi-key-fill text-warning me-1"></i> Quick Demo Login Selection:
                </p>
                <div class="row g-2">
                    <div class="col-6">
                        <button type="button" class="btn btn-light border w-100 demo-btn" onclick="setDemo('superadmin@htech.com.bd', 'Super Admin')">
                            <strong>Super Admin</strong>
                            <small class="d-block text-muted">Platform Super Admin</small>
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-light border w-100 demo-btn" onclick="setDemo('owner@htech.com.bd', 'Company Owner')">
                            <strong>Owner / MD</strong>
                            <small class="d-block text-muted">Managing Director</small>
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-light border w-100 demo-btn" onclick="setDemo('pm@htech.com.bd', 'Project Manager')">
                            <strong>Project Manager</strong>
                            <small class="d-block text-muted">Site Operations Lead</small>
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-light border w-100 demo-btn" onclick="setDemo('site.engineer@htech.com.bd', 'Site Engineer')">
                            <strong>Site Engineer</strong>
                            <small class="d-block text-muted">Field Supervision</small>
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-light border w-100 demo-btn" onclick="setDemo('qs@htech.com.bd', 'Quantity Surveyor')">
                            <strong>QS (Estimator)</strong>
                            <small class="d-block text-muted">BOQ & Cost Estimator</small>
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-light border w-100 demo-btn" onclick="setDemo('accounts@htech.com.bd', 'Accounts Manager')">
                            <strong>Accounts</strong>
                            <small class="d-block text-muted">Commercial & Finance</small>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function setDemo(email, role) {
            document.getElementById('emailInput').value = email;
            document.getElementById('passwordInput').value = 'password';
        }
    </script>
</body>
</html>
