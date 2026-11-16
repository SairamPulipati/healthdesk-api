@extends('layouts.app')
@section('title', 'System Administrator')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <h4 class="fw-bold mb-1"><i class="bi bi-shield-shaded text-dark me-2"></i>Hospital Executive &amp; System Administration</h4>
        <p class="text-muted small mb-0">System Census, HIPAA Audit Logs &amp; Staff Directory</p>
    </div>

    <!-- 4 Stats Cards -->
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-primary">
            <div class="text-muted small fw-bold">TOTAL REGISTERED PATIENTS</div>
            <div class="fs-3 fw-bold text-dark mt-1">{{ $stats['total_patients'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-danger">
            <div class="text-muted small fw-bold">PHYSICIANS ON DUTY</div>
            <div class="fs-3 fw-bold text-dark mt-1">{{ $stats['total_doctors'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-success">
            <div class="text-muted small fw-bold">COMPLETED ENCOUNTERS</div>
            <div class="fs-3 fw-bold text-dark mt-1">{{ $stats['total_encounters'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-warning">
            <div class="text-muted small fw-bold">TOTAL COPAY REVENUE</div>
            <div class="fs-3 fw-bold text-dark mt-1">₹{{ number_format($stats['revenue_est'], 2) }}</div>
        </div>
    </div>

    <!-- Staff Accounts Table -->
    <div class="col-12">
        <div class="card card-custom p-4">
            <h6 class="fw-bold mb-3">Hospital Staff Directory &amp; RBAC Access</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Name</th>
                            <th>Email Login</th>
                            <th>Assigned Role</th>
                            <th>Department</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentUsers as $u)
                            <tr>
                                <td class="fw-bold">{{ $u->name }}</td>
                                <td>{{ $u->email }}</td>
                                <td>
                                    <span class="badge bg-dark-subtle text-dark text-uppercase">{{ $u->role }}</span>
                                </td>
                                <td>{{ $u->staffProfile->department ?? 'General' }}</td>
                                <td><span class="badge bg-success">Active</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
