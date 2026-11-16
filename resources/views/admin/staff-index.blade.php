@extends('layouts.app')
@section('title', 'Hospital Staff Management')

@section('content')
<div class="row g-4">
    <!-- Header -->
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-shield-lock-fill text-dark me-2"></i>Hospital Staff Directory &amp; RBAC Accounts</h4>
            <p class="text-muted small mb-0">Hire Physicians, Nurses, Receptionists, Manage Employment Status &amp; Credentials</p>
        </div>
        <button class="btn btn-dark btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#newStaffModal">
            <i class="bi bi-person-plus-fill me-1"></i> Add / Hire New Staff
        </button>
    </div>

    <!-- Filters & Search -->
    <div class="col-12">
        <div class="card card-custom p-3">
            <form method="GET" action="{{ route('admin.staff.index') }}" class="row g-2 align-items-center">
                <div class="col-md-7">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <option value="">-- All Roles --</option>
                        <option value="doctor" {{ request('role') == 'doctor' ? 'selected' : '' }}>Doctor (Physician)</option>
                        <option value="nurse" {{ request('role') == 'nurse' ? 'selected' : '' }}>Nurse (Triage)</option>
                        <option value="receptionist" {{ request('role') == 'receptionist' ? 'selected' : '' }}>Receptionist (Front Desk)</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                    @if(request('search') || request('role'))
                        <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Staff Table -->
    <div class="col-12">
        <div class="card card-custom p-4">
            <h6 class="fw-bold mb-3">Total Staff Accounts ({{ $staff->total() }})</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Code &amp; Staff Name</th>
                            <th>Role</th>
                            <th>Department &amp; Spec</th>
                            <th>Contact &amp; Location</th>
                            <th>Account Status</th>
                            <th class="text-end">Administrative Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staff as $s)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $s->name }}</div>
                                    <small class="text-muted">{{ $s->email }}</small>
                                    <div class="font-monospace small text-primary">{{ $s->staffProfile->employee_code ?? 'ADM' }}</div>
                                </td>
                                <td>
                                    @if($s->role === 'doctor')
                                        <span class="role-pill bg-danger-subtle text-danger border border-danger-subtle">Doctor</span>
                                    @elseif($s->role === 'nurse')
                                        <span class="role-pill bg-success-subtle text-success border border-success-subtle">Nurse</span>
                                    @elseif($s->role === 'receptionist')
                                        <span class="role-pill bg-warning-subtle text-warning border border-warning-subtle text-dark">Receptionist</span>
                                    @else
                                        <span class="role-pill bg-dark text-white">Admin</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $s->staffProfile->department ?? 'Hospital Admin' }}</div>
                                    <small class="text-muted">{{ $s->staffProfile->specialization ?? 'General' }}</small>
                                </td>
                                <td>
                                    <div><i class="bi bi-telephone me-1 text-muted"></i>{{ $s->staffProfile->phone ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $s->staffProfile->city ?? 'Hyderabad' }}</small>
                                </td>
                                <td>
                                    @if($s->is_active)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Active / Enabled</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Disabled / Left Firm</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <!-- Edit Modal Button -->
                                        <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editStaffModal{{ $s->id }}" title="Edit Details & Password">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <!-- Toggle Enable / Disable -->
                                        <form action="{{ route('admin.staff.toggle', $s->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-{{ $s->is_active ? 'warning' : 'success' }} btn-sm" title="{{ $s->is_active ? 'Disable / Deactivate Account' : 'Re-enable Account' }}">
                                                <i class="bi bi-{{ $s->is_active ? 'person-slash' : 'person-check' }}"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Edit Staff Modal -->
                            <div class="modal fade" id="editStaffModal{{ $s->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Edit Profile: {{ $s->name }} ({{ $s->email }})</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('admin.staff.update', $s->id) }}" method="POST">
                                            @csrf @method('PUT')
                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold">Full Name *</label>
                                                        <input type="text" name="name" class="form-control" value="{{ $s->name }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold">Reset Password (Leave blank to keep current)</label>
                                                        <input type="password" name="new_password" class="form-control" placeholder="New secure password">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold">Department *</label>
                                                        <input type="text" name="department" class="form-control" value="{{ $s->staffProfile->department ?? 'Cardiology' }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold">Specialization</label>
                                                        <input type="text" name="specialization" class="form-control" value="{{ $s->staffProfile->specialization ?? '' }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label small fw-bold">Phone Number</label>
                                                        <input type="text" name="phone" class="form-control" value="{{ $s->staffProfile->phone ?? '' }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label small fw-bold">Consultation Fee (₹)</label>
                                                        <input type="number" step="0.01" name="consultation_fee" class="form-control" value="{{ $s->staffProfile->consultation_fee ?? 0 }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label small fw-bold">Date of Birth</label>
                                                        <input type="date" name="date_of_birth" class="form-control" value="{{ $s->staffProfile && $s->staffProfile->date_of_birth ? $s->staffProfile->date_of_birth->format('Y-m-d') : '' }}">
                                                    </div>
                                                    <div class="col-md-8">
                                                        <label class="form-label small fw-bold">Street Address</label>
                                                        <input type="text" name="address" class="form-control" value="{{ $s->staffProfile->address ?? '' }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label small fw-bold">City</label>
                                                        <input type="text" name="city" class="form-control" value="{{ $s->staffProfile->city ?? 'Hyderabad' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No staff accounts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $staff->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Hire / Add New Staff Modal -->
<div class="modal fade" id="newStaffModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-plus-fill me-2"></i>Hire / Create New Staff Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.staff.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Dr. Ramesh Kumar" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Role Assignment <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                <option value="doctor">Doctor (Physician)</option>
                                <option value="nurse">Nurse (Triage Station)</option>
                                <option value="receptionist">Receptionist (Front Desk)</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Official Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="name@healthdesk.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Initial Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" value="password123" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Department <span class="text-danger">*</span></label>
                            <input type="text" name="department" class="form-control" placeholder="e.g. Cardiology, Pediatrics, OPD" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Specialization</label>
                            <input type="text" name="specialization" class="form-control" placeholder="e.g. Interventional Cardiology">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Phone Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="+91 98765...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Consultation Fee (₹)</label>
                            <input type="number" step="0.01" name="consultation_fee" class="form-control" value="500.00">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Address</label>
                            <input type="text" name="address" class="form-control" placeholder="Residential Street Address">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">City</label>
                            <input type="text" name="city" class="form-control" value="Hyderabad">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark btn-sm fw-bold">Create Staff Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
