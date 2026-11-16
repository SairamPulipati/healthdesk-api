<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthDesk - @yield('title', 'Clinical Portal')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; color: #0f172a; }
        .navbar-brand { font-weight: 800; letter-spacing: 0.5px; color: #0284c7 !important; }
        .role-pill { font-size: 0.75rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; padding: 3px 8px; border-radius: 6px; }
        .card-custom { border: none; border-radius: 10px; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.08); background: #ffffff; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top py-2">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
            <i class="bi bi-heart-pulse-fill text-danger fs-4"></i>
            <span>HealthDesk <span class="text-secondary fw-normal fs-6">| Clinical HIMS</span></span>
        </a>
        @auth
            <ul class="navbar-nav me-auto ms-4 gap-2">
                @if(auth()->user()->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-dark {{ request()->is('admin/dashboard*') ? 'active fw-bold' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-dark {{ request()->is('admin/staff*') ? 'active fw-bold' : '' }}" href="{{ route('admin.staff.index') }}">
                            <i class="bi bi-person-badge-fill me-1"></i> Staff Directory
                        </a>
                    </li>
                @endif
                @if(in_array(auth()->user()->role, ['admin', 'receptionist', 'doctor', 'nurse']))
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-primary {{ request()->is('patients*') ? 'active fw-bold' : '' }}" href="{{ route('patients.index') }}">
                            <i class="bi bi-people-fill me-1"></i> Patient Master
                        </a>
                    </li>
                @endif
            </ul>
        @endauth
        <div class="d-flex align-items-center gap-3 ms-auto">
            @auth
                <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary btn-sm py-1" title="Account & Security Settings">
                    <i class="bi bi-gear me-1"></i> My Profile
                </a>
                <div class="d-flex align-items-center gap-2">
                    <span class="small fw-bold text-dark">{{ auth()->user()->name }}</span>
                    @if(auth()->user()->role === 'doctor')
                        <span class="role-pill bg-danger-subtle text-danger border border-danger-subtle">Physician</span>
                    @elseif(auth()->user()->role === 'nurse')
                        <span class="role-pill bg-success-subtle text-success border border-success-subtle">Triage Nurse</span>
                    @elseif(auth()->user()->role === 'receptionist')
                        <span class="role-pill bg-warning-subtle text-warning border border-warning-subtle text-dark">Front Desk</span>
                    @elseif(auth()->user()->role === 'admin')
                        <span class="role-pill bg-dark text-white">Administrator</span>
                    @else
                        <span class="role-pill bg-info-subtle text-info border border-info-subtle text-dark">Patient</span>
                    @endif
                </div>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-outline-secondary btn-sm py-1 px-2" title="Sign Out">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm px-3">Sign In</a>
            @endauth
        </div>
    </div>
</nav>

<main class="py-4">
    <div class="container-fluid px-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 py-2" role="alert">
                <i class="bi bi-check-circle-fill"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 py-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
