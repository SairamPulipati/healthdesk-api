<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthDesk - Clinical System Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .login-card {
            background: #ffffff;
            border-radius: 14px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 440px;
            padding: 30px;
        }
        .demo-chip {
            cursor: pointer;
            transition: all 0.15s ease;
            font-size: 0.8rem;
        }
        .demo-chip:hover {
            transform: scale(1.03);
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="text-center mb-4">
        <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle p-3 mb-2" style="width: 54px; height: 54px;">
            <i class="bi bi-heart-pulse-fill fs-3"></i>
        </div>
        <h4 class="fw-bold mb-0 text-dark">HealthDesk Core</h4>
        <small class="text-muted">Clinical Portal &amp; Hospital Management</small>
    </div>

    @if(session('success'))
        <div class="alert alert-success py-2 small">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger py-2 small">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('login.post') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label small fw-bold text-secondary">Clinical User Email</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                <input type="email" id="emailInput" name="email" class="form-control" placeholder="user@healthdesk.com" required value="{{ old('email') }}">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label small fw-bold text-secondary">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                <input type="password" id="passwordInput" name="password" class="form-control" placeholder="••••••••" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
            <i class="bi bi-box-arrow-in-right me-1"></i> Sign In to Portal
        </button>
    </form>

    <hr class="my-4">

    <!-- 1-Click Quick Demo Switcher -->
    <div class="text-center">
        <p class="small text-muted fw-bold mb-2 text-uppercase" style="letter-spacing: 0.5px;">1-Click Role Logins (Password: password123)</p>
        <div class="d-flex flex-wrap gap-1 justify-content-center">
            <button type="button" class="btn btn-outline-danger btn-sm demo-chip" onclick="fillCreds('doctor@healthdesk.com')">👨‍⚕️ Doctor</button>
            <button type="button" class="btn btn-outline-success btn-sm demo-chip" onclick="fillCreds('nurse@healthdesk.com')">👩‍⚕️ Nurse</button>
            <button type="button" class="btn btn-outline-warning btn-sm demo-chip text-dark" onclick="fillCreds('reception@healthdesk.com')">📋 Reception</button>
            <button type="button" class="btn btn-outline-dark btn-sm demo-chip" onclick="fillCreds('admin@healthdesk.com')">🛡️ Admin</button>
            <button type="button" class="btn btn-outline-info btn-sm demo-chip text-dark" onclick="fillCreds('patient@healthdesk.com')">🧑‍💼 Patient</button>
        </div>
    </div>
</div>

<script>
function fillCreds(email) {
    document.getElementById('emailInput').value = email;
    document.getElementById('passwordInput').value = 'password123';
}
</script>

</body>
</html>
