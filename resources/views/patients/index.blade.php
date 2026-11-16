@extends('layouts.app')
@section('title', 'Patient Master Directory')

@section('content')
<div class="row g-4">
    <!-- Header with Action Button -->
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-people-fill text-primary me-2"></i>Patient Master Directory</h4>
            <p class="text-muted small mb-0">Demographics, MRN Tracking, Allergy Alerts &amp; Insurance Profiles</p>
        </div>
        <button class="btn btn-primary btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#newPatientModal">
            <i class="bi bi-person-plus-fill me-1"></i> Register New Patient
        </button>
    </div>

    <!-- Search Filter Bar -->
    <div class="col-12">
        <div class="card card-custom p-3">
            <form method="GET" action="{{ route('patients.index') }}" class="row g-2 align-items-center">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search by Patient Name, MRN (e.g. MRN-2026-0001), or Phone number..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                    @if(request('search'))
                        <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">Clear</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Patients Table -->
    <div class="col-12">
        <div class="card card-custom p-4">
            <h6 class="fw-bold mb-3">Total Registered Patients ({{ $patients->total() }})</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>MRN</th>
                            <th>Patient Name &amp; Age</th>
                            <th>Contact &amp; Email</th>
                            <th>Insurance Payer</th>
                            <th>Allergies &amp; PCP (PR #1930)</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patients as $pt)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary font-monospace fs-7 px-2 py-1">{{ $pt->mrn }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $pt->full_name }}</div>
                                    <small class="text-muted">
                                        {{ $pt->date_of_birth ? $pt->date_of_birth->age . ' yrs (' . $pt->date_of_birth->format('d M Y') . ')' : 'Age N/A' }} 
                                        &bull; <span class="text-capitalize">{{ $pt->gender }}</span>
                                        @if($pt->blood_group) &bull; <strong class="text-danger">{{ $pt->blood_group }}</strong> @endif
                                    </small>
                                </td>
                                <td>
                                    <div><i class="bi bi-telephone me-1 text-muted"></i>{{ $pt->phone }}</div>
                                    <small class="text-muted"><i class="bi bi-envelope me-1"></i>{{ $pt->email ?? 'No email' }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold text-primary">{{ $pt->insurance_payer }}</div>
                                    <small class="text-muted">Policy: {{ $pt->insurance_policy_no ?? 'Self Pay' }}</small>
                                </td>
                                <td>
                                    @if($pt->allergies)
                                        <div class="small text-danger fw-semibold"><i class="bi bi-shield-fill-exclamation"></i> {{ Str::limit($pt->allergies, 30) }}</div>
                                    @else
                                        <div class="small text-muted">No allergies recorded</div>
                                    @endif
                                    <small class="text-secondary">PCP: {{ $pt->pcp_name ?? 'Unassigned' }}</small>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <!-- Edit Button -->
                                        <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $pt->id }}" title="Edit Demographics">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <!-- Delete Button -->
                                        <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $pt->id }}" title="Delete Record">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Edit Patient Modal -->
                            <div class="modal fade" id="editModal{{ $pt->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Edit Demographics: {{ $pt->full_name }} ({{ $pt->mrn }})</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('patients.update', $pt->id) }}" method="POST">
                                            @csrf @method('PUT')
                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold">First Name *</label>
                                                        <input type="text" name="first_name" class="form-control" value="{{ $pt->first_name }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold">Last Name *</label>
                                                        <input type="text" name="last_name" class="form-control" value="{{ $pt->last_name }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label small fw-bold">Date of Birth</label>
                                                        <input type="date" name="date_of_birth" class="form-control" value="{{ $pt->date_of_birth ? $pt->date_of_birth->format('Y-m-d') : '' }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label small fw-bold">Gender *</label>
                                                        <select name="gender" class="form-select" required>
                                                            <option value="male" {{ $pt->gender == 'male' ? 'selected' : '' }}>Male</option>
                                                            <option value="female" {{ $pt->gender == 'female' ? 'selected' : '' }}>Female</option>
                                                            <option value="other" {{ $pt->gender == 'other' ? 'selected' : '' }}>Other</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label small fw-bold">Blood Group</label>
                                                        <input type="text" name="blood_group" class="form-control" value="{{ $pt->blood_group }}" placeholder="e.g. B+, O-">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold">Phone Number *</label>
                                                        <input type="text" name="phone" class="form-control" value="{{ $pt->phone }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold">Email Address</label>
                                                        <input type="email" name="email" class="form-control" value="{{ $pt->email }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold">Insurance Payer *</label>
                                                        <input type="text" name="insurance_payer" class="form-control" value="{{ $pt->insurance_payer }}" placeholder="Aetna, Humana, Medicare, Self Pay" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold">Insurance Policy #</label>
                                                        <input type="text" name="insurance_policy_no" class="form-control" value="{{ $pt->insurance_policy_no }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold">Primary Care Physician (PCP)</label>
                                                        <input type="text" name="pcp_name" class="form-control" value="{{ $pt->pcp_name }}" placeholder="Dr. Name">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-bold text-danger">Recorded Allergies (Triggers Alerts)</label>
                                                        <input type="text" name="allergies" class="form-control" value="{{ $pt->allergies }}" placeholder="e.g. Penicillin, Sulfa drugs">
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

                            <!-- Delete Confirmation Modal -->
                            <div class="modal fade" id="deleteModal{{ $pt->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold text-danger"><i class="bi bi-trash"></i> Confirm Deletion</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you want to permanently delete patient <strong>{{ $pt->full_name }}</strong> (MRN: {{ $pt->mrn }})? This action cannot be undone.
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                            <form action="{{ route('patients.destroy', $pt->id) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Delete Permanently</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No patients found. Click "Register New Patient" to add one!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $patients->links() }}
            </div>
        </div>
    </div>
</div>

<!-- New Patient Registration Modal -->
<div class="modal fade" id="newPatientModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-plus-fill me-2"></i>New Patient Intake Registration</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('patients.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" placeholder="e.g. Priya" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" placeholder="e.g. Sharma" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select" required>
                                <option value="female">Female</option>
                                <option value="male">Male</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Blood Group</label>
                            <input type="text" name="blood_group" class="form-control" placeholder="e.g. A+, O+, B-">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" placeholder="+91 9876543210" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="patient@example.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Insurance Payer <span class="text-danger">*</span></label>
                            <input type="text" name="insurance_payer" class="form-control" placeholder="e.g. Aetna, Humana, Medicare, Self Pay" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Insurance Policy #</label>
                            <input type="text" name="insurance_policy_no" class="form-control" placeholder="e.g. AET-883920">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Primary Care Physician (PCP)</label>
                            <input type="text" name="pcp_name" class="form-control" placeholder="e.g. Dr. Rajesh Rao">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-danger">Known Allergies (Penicillin, etc.)</label>
                            <input type="text" name="allergies" class="form-control" placeholder="Leave blank if none">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold">Generate MRN &amp; Register Patient</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
