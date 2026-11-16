@extends('layouts.app')
@section('title', 'Physician Clinical Portal')

@section('content')
<div class="row g-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-hospital text-danger me-2"></i>Physician Clinical Queue</h4>
            <p class="text-muted small mb-0">SOAP Encounters, Diagnoses, E-Prescriptions &amp; Clinical PDF Sign-Off</p>
        </div>
    </div>

    <!-- Doctor's Scheduled Encounters -->
    <div class="col-12">
        <div class="card card-custom p-4">
            <h6 class="fw-bold mb-3">Today's Patient Schedule</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>MRN &amp; Patient</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Triage Vitals (Recorded by Nurse)</th>
                            <th>Clinical SOAP Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $appt)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $appt->patient->full_name }}</div>
                                    <span class="badge bg-secondary-subtle text-secondary font-monospace">{{ $appt->patient->mrn }}</span>
                                    @if($appt->patient->allergies)
                                        <div class="small text-danger fw-semibold mt-1"><i class="bi bi-shield-fill-exclamation"></i> Allergy: {{ $appt->patient->allergies }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $appt->appointment_time->format('h:i A') }}</div>
                                </td>
                                <td>
                                    @if($appt->status === 'ready_for_doc')
                                        <span class="badge bg-warning-subtle text-warning text-dark border border-warning-subtle px-2 py-1">Ready in Exam Room</span>
                                    @elseif($appt->status === 'completed')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Encounter Signed</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary px-2 py-1">{{ ucfirst(str_replace('_', ' ', $appt->status)) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($appt->encounter && $appt->encounter->systolic_bp)
                                        <div class="small">
                                            <strong>BP:</strong> {{ $appt->encounter->systolic_bp }}/{{ $appt->encounter->diastolic_bp }} mmHg &bull;
                                            <strong>HR:</strong> {{ $appt->encounter->pulse_rate }} bpm &bull;
                                            <strong>SpO2:</strong> {{ $appt->encounter->oxygen_spo2 }}%
                                        </div>
                                    @else
                                        <span class="badge bg-light text-muted">Awaiting Triage Vitals</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#soapModal{{ $appt->id }}">
                                        <i class="bi bi-file-earmark-medical me-1"></i> Open SOAP Encounter
                                    </button>
                                </td>
                            </tr>

                            <!-- SOAP Encounter Modal -->
                            <div class="modal fade" id="soapModal{{ $appt->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Clinical Encounter: {{ $appt->patient->full_name }} ({{ $appt->patient->mrn }})</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('doctor.encounter.save', $appt->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <!-- PR #1930 Allergy & PCP Combined Alert Banner -->
                                                <div class="alert alert-warning py-2 small d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong><i class="bi bi-shield-fill-exclamation text-danger"></i> ALLERGY ALERT:</strong> {{ $appt->patient->allergies ?? 'None' }}
                                                    </div>
                                                    <div>
                                                        <strong>Primary Care Physician (PCP):</strong> {{ $appt->patient->pcp_name ?? 'Unassigned' }}
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Subjective Notes (History of Present Illness)</label>
                                                    <textarea name="subjective_notes" rows="2" class="form-control" required>{{ $appt->encounter->subjective_notes ?? 'Patient reports symptoms of...' }}</textarea>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Objective Notes (Physical Exam &amp; Vitals Review)</label>
                                                    <textarea name="objective_notes" rows="2" class="form-control" required>{{ $appt->encounter->objective_notes ?? 'Lungs clear, cardiovascular rhythm normal.' }}</textarea>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Assessment (ICD-10 Diagnoses)</label>
                                                    <textarea name="assessment" rows="2" class="form-control" required>{{ $appt->encounter->assessment ?? 'I10 - Essential Hypertension' }}</textarea>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Clinical Plan &amp; Rx</label>
                                                    <textarea name="clinical_plan" rows="2" class="form-control" required>{{ $appt->encounter->clinical_plan ?? 'Prescribed medication. Follow up in 14 days.' }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-pen me-1"></i> Sign &amp; Lock Clinical Encounter
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No appointments found in your schedule.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
