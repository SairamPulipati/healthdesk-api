@extends('layouts.app')
@section('title', 'Front Desk Arrival Lobby')

@section('content')
<div class="row g-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-person-badge text-warning me-2"></i>Reception &amp; Front Desk Lobby</h4>
            <p class="text-muted small mb-0">Patient Arrival Check-in, Conflict-Free Scheduling &amp; Copay Collection</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#quickBookModal">
                <i class="bi bi-calendar-plus me-1"></i> Book Appointment (Conflict Guard)
            </button>
            <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-people me-1"></i> All Patients Directory
            </a>
        </div>
    </div>

    <!-- Main Lobby Table -->
    <div class="col-12">
        <div class="card card-custom p-4">
            <h6 class="fw-bold mb-3">Today's Scheduled Encounters ({{ $todayAppointments->count() }})</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>MRN &amp; Patient Name</th>
                            <th>Insurance &amp; Payer</th>
                            <th>Assigned Doctor</th>
                            <th>Scheduled Slot</th>
                            <th>Status</th>
                            <th>Copay</th>
                            <th class="text-end">Front Desk Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($todayAppointments as $appt)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $appt->patient->full_name }}</div>
                                    <span class="badge bg-secondary-subtle text-secondary font-monospace">{{ $appt->patient->mrn }}</span>
                                    @if($appt->patient->patient_status === 'deceased')
                                        <span class="badge bg-dark text-white">Deceased</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold text-primary">{{ $appt->patient->insurance_payer }}</div>
                                    <small class="text-muted">Policy: {{ $appt->patient->insurance_policy_no ?? 'Self Pay' }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $appt->doctor->name }}</div>
                                    <small class="text-muted">{{ $appt->doctor->staffProfile->specialization ?? 'Physician' }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $appt->appointment_time->format('h:i A') }}</div>
                                    <small class="text-muted">{{ $appt->appointment_time->format('d M Y') }}</small>
                                </td>
                                <td>
                                    @if($appt->status === 'scheduled')
                                        <span class="badge bg-secondary-subtle text-secondary px-2 py-1">Scheduled (Not Arrived)</span>
                                    @elseif($appt->status === 'checked_in')
                                        <span class="badge bg-warning-subtle text-warning text-dark px-2 py-1">Waiting in Lobby</span>
                                    @elseif($appt->status === 'in_consultation')
                                        <span class="badge bg-primary-subtle text-primary px-2 py-1">With Doctor</span>
                                    @elseif($appt->status === 'completed')
                                        <span class="badge bg-success-subtle text-success px-2 py-1">Completed</span>
                                    @elseif($appt->status === 'cancelled')
                                        <span class="badge bg-danger-subtle text-danger px-2 py-1">Cancelled</span>
                                    @else
                                        <span class="badge bg-info-subtle text-info text-dark px-2 py-1">{{ ucfirst(str_replace('_', ' ', $appt->status)) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold text-success">₹{{ number_format($appt->copay_collected, 2) }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        @if($appt->status === 'scheduled')
                                            <form action="{{ route('reception.checkin', $appt->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button class="btn btn-warning btn-sm text-dark fw-semibold" title="Patient Arrived">
                                                    <i class="bi bi-box-arrow-in-right me-1"></i> Check-In
                                                </button>
                                            </form>
                                        @endif

                                        @if($appt->status !== 'completed' && $appt->status !== 'cancelled')
                                            <form action="{{ route('appointments.cancel', $appt->id) }}" method="POST" class="d-inline ms-1">
                                                @csrf
                                                <button class="btn btn-outline-danger btn-sm" title="Cancel Appointment">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No appointments scheduled for today.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Conflict-Free Appointment Booking Modal -->
<div class="modal fade" id="quickBookModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-calendar-plus me-2"></i>Schedule Appointment (Conflict Guard)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('appointments.book') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Select Registered Patient *</label>
                        <select name="patient_id" class="form-select" required>
                            <option value="">-- Choose Patient --</option>
                            @foreach($patients as $pt)
                                <option value="{{ $pt->id }}">{{ $pt->full_name }} ({{ $pt->mrn }}) - {{ $pt->phone }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Attending Physician *</label>
                        <select name="doctor_id" class="form-select" required>
                            <option value="">-- Choose Doctor --</option>
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}">{{ $doc->name }} ({{ $doc->staffProfile->specialization ?? 'General' }}) - Fee: ₹{{ $doc->staffProfile->consultation_fee ?? 500 }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Appointment Date &amp; Time *</label>
                        <input type="datetime-local" name="appointment_time" class="form-control" required>
                        <small class="text-muted">System automatically checks for existing bookings to prevent overlaps.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Chief Complaint / Visit Reason</label>
                        <textarea name="chief_complaint" rows="2" class="form-control" placeholder="Symptoms, reason for visit..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Copay Amount (₹)</label>
                        <input type="number" step="0.01" name="copay_collected" class="form-control" value="25.00">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold">Check Conflicts &amp; Confirm Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
