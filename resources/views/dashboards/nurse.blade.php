@extends('layouts.app')
@section('title', 'Nurse Triage Station')

@section('content')
<div class="row g-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-clipboard2-pulse text-success me-2"></i>Outpatient Triage Station</h4>
            <p class="text-muted small mb-0">Record Clinical Vitals, Pre-Encounter Intake &amp; Allergy Screening</p>
        </div>
    </div>

    <!-- Triage Waiting Queue -->
    <div class="col-12">
        <div class="card card-custom p-4">
            <h6 class="fw-bold mb-3">Arrived Patients Awaiting Triage ({{ $triageQueue->count() }})</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>MRN &amp; Patient</th>
                            <th>Age &amp; Gender</th>
                            <th>Allergies &amp; PCP</th>
                            <th>Assigned Doctor</th>
                            <th>Chief Complaint</th>
                            <th class="text-end">Triage Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($triageQueue as $appt)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $appt->patient->full_name }}</div>
                                    <span class="badge bg-secondary-subtle text-secondary font-monospace">{{ $appt->patient->mrn }}</span>
                                </td>
                                <td>
                                    <div>{{ $appt->patient->date_of_birth ? $appt->patient->date_of_birth->age . ' yrs' : 'N/A' }}</div>
                                    <small class="text-muted text-capitalize">{{ $appt->patient->gender }}</small>
                                </td>
                                <td>
                                    @if($appt->patient->allergies)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi bi-exclamation-triangle-fill"></i> {{ Str::limit($appt->patient->allergies, 25) }}</span>
                                    @else
                                        <span class="badge bg-light text-muted">No Known Allergies</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $appt->doctor->name }}</div>
                                </td>
                                <td class="small text-muted" style="max-width: 250px;">
                                    {{ $appt->chief_complaint ?? 'Routine consultation' }}
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-success btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#vitalsModal{{ $appt->id }}">
                                        <i class="bi bi-heart-pulse me-1"></i> Record Vitals
                                    </button>
                                </td>
                            </tr>

                            <!-- Vitals Modal for Nurse -->
                            <div class="modal fade" id="vitalsModal{{ $appt->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Clinical Vitals Intake: {{ $appt->patient->full_name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('nurse.vitals.store', $appt->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <!-- OpenEMR Style Allergy Alert Banner -->
                                                @if($appt->patient->allergies)
                                                    <div class="alert alert-danger py-2 small mb-3">
                                                        <strong><i class="bi bi-shield-fill-exclamation"></i> ALLERGY WARNING:</strong> {{ $appt->patient->allergies }}
                                                    </div>
                                                @endif

                                                <div class="row g-3">
                                                    <div class="col-6">
                                                        <label class="form-label small fw-bold">Systolic BP (mmHg)</label>
                                                        <input type="number" name="systolic_bp" class="form-control" placeholder="120" required>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small fw-bold">Diastolic BP (mmHg)</label>
                                                        <input type="number" name="diastolic_bp" class="form-control" placeholder="80" required>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small fw-bold">Pulse Rate (BPM)</label>
                                                        <input type="number" name="pulse_rate" class="form-control" placeholder="72" required>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small fw-bold">Temp (&deg;F)</label>
                                                        <input type="number" step="0.1" name="temperature" class="form-control" placeholder="98.6" required>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small fw-bold">Oxygen SpO2 (%)</label>
                                                        <input type="number" name="oxygen_spo2" class="form-control" placeholder="99" required>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small fw-bold">Weight (kg)</label>
                                                        <input type="number" step="0.1" name="weight_kg" class="form-control" placeholder="70.5" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-success btn-sm">Save Vitals &amp; Send to Doctor</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No patients currently waiting in triage queue.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
