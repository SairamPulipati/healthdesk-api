@extends('layouts.app')
@section('title', 'Patient Health Portal')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <h4 class="fw-bold mb-1"><i class="bi bi-person-heart text-info me-2"></i>My Patient Health Portal</h4>
        <p class="text-muted small mb-0">MRN: <strong class="font-monospace text-dark">{{ $patient->mrn ?? 'Pending' }}</strong> &bull; Primary Care Physician: {{ $patient->pcp_name ?? 'Unassigned' }}</p>
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
                            <th class="text-end">Visit Summary</th>
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
                                        <span class="badge bg-success-subtle text-success px-2 py-1">Encounter Completed</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning text-dark px-2 py-1">{{ ucfirst($appt->status) }}</span>
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
                                    @if($appt->encounter && $appt->encounter->is_signed)
                                        <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                                            <i class="bi bi-file-earmark-pdf me-1"></i> Download Summary
                                        </button>
                                    @else
                                        <span class="text-muted small">Awaiting doctor sign-off</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No clinical visits recorded.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
