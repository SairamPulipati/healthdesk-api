@extends('layouts.app')
@section('title', 'Front Desk Arrival Lobby')

@section('content')
<div class="row g-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-person-badge text-warning me-2"></i>Reception &amp; Front Desk Lobby</h4>
            <p class="text-muted small mb-0">Patient Arrival Check-in, Insurance Verification &amp; Copay Collection</p>
        </div>
    </div>

    <!-- Main Lobby Table -->
    <div class="col-12">
        <div class="card card-custom p-4">
            <h6 class="fw-bold mb-3">Today's Arrival Queue ({{ $todayAppointments->count() }})</h6>
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
                            <th class="text-end">Front Desk Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($todayAppointments as $appt)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $appt->patient->full_name }}</div>
                                    <span class="badge bg-secondary-subtle text-secondary font-monospace">{{ $appt->patient->mrn }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-primary">{{ $appt->patient->insurance_payer }}</div>
                                    <small class="text-muted">Policy: {{ $appt->patient->insurance_policy_no ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $appt->doctor->name }}</div>
                                    <small class="text-muted">{{ $appt->doctor->staffProfile->specialization ?? 'General' }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $appt->appointment_time->format('h:i A') }}</div>
                                    <small class="text-muted">{{ $appt->appointment_time->format('d M') }}</small>
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
                                    @else
                                        <span class="badge bg-info-subtle text-info text-dark px-2 py-1">{{ ucfirst($appt->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold text-success">₹{{ number_format($appt->copay_collected, 2) }}</span>
                                </td>
                                <td class="text-end">
                                    @if($appt->status === 'scheduled')
                                        <form action="{{ route('reception.checkin', $appt->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-warning btn-sm text-dark fw-semibold">
                                                <i class="bi bi-box-arrow-in-right me-1"></i> Check-In &amp; Collect Copay
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-light btn-sm text-muted" disabled>
                                            <i class="bi bi-check2-all text-success"></i> Checked-In
                                        </button>
                                    @endif
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
@endsection
