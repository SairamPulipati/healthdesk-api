<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; color: #1e293b; line-height: 1.5; }
        .card { max-width: 560px; margin: 20px auto; border: 1px solid #e2e8f0; border-radius: 8px; padding: 24px; background: #ffffff; }
        .header { border-bottom: 2px solid #0284c7; padding-bottom: 12px; margin-bottom: 16px; }
        .brand { font-size: 20px; font-weight: bold; color: #0284c7; }
        .details-box { background: #f8fafc; border-radius: 6px; padding: 14px; margin: 16px 0; font-size: 14px; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 4px; font-weight: bold; font-size: 12px; text-transform: uppercase; }
        .badge-booked { background: #e0f2fe; color: #0369a1; }
        .badge-confirmed { background: #dcfce7; color: #15803d; }
        .badge-cancelled { background: #fee2e2; color: #b91c1c; }
        .badge-completed { background: #f3e8ff; color: #7e22ce; }
        .footer { font-size: 12px; color: #64748b; text-align: center; margin-top: 24px; border-top: 1px solid #f1f5f9; padding-top: 12px; }
    </style>
</head>
<body>
<div class="card">
    <div class="header">
        <div class="brand">&#9829; HealthDesk Clinical Notifications</div>
        <small style="color: #64748b;">Enterprise Electronic Medical Records &amp; Scheduling</small>
    </div>

    <p>Dear <strong>{{ $appointment->patient->full_name }}</strong>,</p>

    @if($actionType === 'booked')
        <p>Your appointment has been successfully placed in our clinical schedule.</p>
    @elseif($actionType === 'confirmed')
        <p>Great news! Your consultation with your attending physician is now <strong>Confirmed</strong>.</p>
    @elseif($actionType === 'cancelled')
        <p>Your scheduled appointment has been <strong>Cancelled</strong>. Please contact front desk if this was an error.</p>
    @elseif($actionType === 'completed')
        <p>Your clinical encounter has concluded and the official chart has been signed by your physician.</p>
    @endif

    <div class="details-box">
        <p style="margin: 0 0 6px 0;"><strong>Appointment Reference:</strong> #{{ $appointment->id }}</p>
        <p style="margin: 0 0 6px 0;"><strong>Medical Record Number (MRN):</strong> {{ $appointment->patient->mrn }}</p>
        <p style="margin: 0 0 6px 0;"><strong>Attending Physician:</strong> {{ $appointment->doctor->name }} ({{ $appointment->doctor->staffProfile->specialization ?? 'General Medicine' }})</p>
        <p style="margin: 0 0 6px 0;"><strong>Scheduled Time:</strong> {{ $appointment->appointment_time->format('l, d M Y \a\t h:i A') }}</p>
        <p style="margin: 0 0 6px 0;"><strong>Status:</strong> <span class="badge badge-{{ $actionType }}">{{ ucfirst($appointment->status) }}</span></p>
        @if($appointment->chief_complaint)
            <p style="margin: 0;"><strong>Chief Complaint / Reason:</strong> {{ $appointment->chief_complaint }}</p>
        @endif
    </div>

    <p style="font-size: 13px; color: #475569;">
        Please arrive 15 minutes prior to your scheduled time with your insurance card ({{ $appointment->patient->insurance_payer }}) and photo ID.
    </p>

    <div class="footer">
        HealthDesk Healthcare Portal &bull; Automated notification &bull; Do not reply directly to this email.
    </div>
</div>
</body>
</html>
