@extends('layouts.app')
@section('title', 'Patient Health Portal')

@section('content')
<div class="row g-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-person-heart text-info me-2"></i>My Patient Health Portal</h4>
            <p class="text-muted small mb-0">MRN: <strong class="font-monospace text-dark">{{ $patient->mrn ?? 'Pending' }}</strong> &bull; Primary Care Physician: {{ $patient->pcp_name ?? 'Unassigned' }}</p>
        </div>
        <button class="btn btn-primary btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#patientBookModal">
            <i class="bi bi-calendar-plus me-1"></i> Book New Consultation
        </button>
    </div>

    @if($patient && $patient->allergies)
        <div class="col-12">
            <div class="alert alert-danger py-2 small mb-0">
                <i class="bi bi-shield-fill-exclamation me-1"></i> <strong>Recorded Allergies on File:</strong> {{ $patient->allergies }}
            </div>
        </div>
    @endif

    <!-- Patient Visits History -->
    <div class="col-12">
        <div class="card card-custom p-4">
            <h6 class="fw-bold mb-3">My Visit History &amp; Signed Clinical Summaries</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Visit Date</th>
                            <th>Attending Doctor</th>
                            <th>Status</th>
                            <th>Prescriptions &amp; Plan</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $appt)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $appt->appointment_time->format('d M Y') }}</div>
                                    <small class="text-muted">{{ $appt->appointment_time->format('h:i A') }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold text-primary">{{ $appt->doctor->name }}</div>
                                    <small class="text-muted">{{ $appt->doctor->staffProfile->specialization ?? 'Specialist' }}</small>
                                </td>
                                <td>
                                    @if($appt->status === 'completed')
                                        <span class="badge bg-success-subtle text-success px-2 py-1"><i class="bi bi-lock-fill"></i> Encounter Signed</span>
                                    @elseif($appt->status === 'cancelled')
                                        <span class="badge bg-danger-subtle text-danger px-2 py-1">Cancelled</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning text-dark px-2 py-1">{{ ucfirst(str_replace('_', ' ', $appt->status)) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($appt->encounter && $appt->encounter->prescriptions->count())
                                        @foreach($appt->encounter->prescriptions as $rx)
                                            <span class="badge bg-light text-dark border">{{ $rx->medication_name }} ({{ $rx->dosage }})</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted small">No medications prescribed</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        @if($appt->encounter && $appt->encounter->is_signed)
                                            <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                                                <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
                                            </button>
                                        @elseif($appt->status !== 'completed' && $appt->status !== 'cancelled')
                                            <form action="{{ route('appointments.cancel', $appt->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Cancel this upcoming appointment?');">
                                                    Cancel
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No clinical visits recorded. Click "Book New Consultation" to schedule one!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Patient Booking Modal -->
@if($patient)
<div class="modal fade" id="patientBookModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-calendar-plus me-2"></i>Book Consultation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('appointments.book') }}" method="POST">
                @csrf
                <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Select Doctor *</label>
                        <select name="doctor_id" class="form-select" required>
                            <option value="">-- Choose a Doctor --</option>
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}">{{ $doc->name }} ({{ $doc->staffProfile->specialization ?? 'General' }}) - Fee: ₹{{ $doc->staffProfile->consultation_fee ?? 500 }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Preferred Date &amp; Time *</label>
                        <input type="datetime-local" name="appointment_time" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Describe Your Symptoms / Reason</label>
                        <textarea name="chief_complaint" rows="2" class="form-control" placeholder="Fever, cough, back pain..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold">Confirm Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
