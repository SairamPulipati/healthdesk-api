@extends('layouts.app')
@section('title', 'My Account & Security')

@section('content')
<div class="row g-4 justify-content-center">
    <div class="col-lg-8">
        <h4 class="fw-bold mb-1"><i class="bi bi-person-gear text-primary me-2"></i>My Profile &amp; Security Settings</h4>
        <p class="text-muted small mb-4">Manage Personal Details, Contact Addresses &amp; Password</p>

        <!-- Demographics Card -->
        <div class="card card-custom p-4 mb-4">
            <h6 class="fw-bold mb-3 border-bottom pb-2">Personal Demographics</h6>
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf @method('PUT')
                <div class="row g-3">
                    <!-- Locked System Identifiers -->
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Account Name</label>
                        <input type="text" class="form-control bg-light" value="{{ $user->name }}" readonly disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Official Email (Locked for Security)</label>
                        <input type="email" class="form-control bg-light" value="{{ $user->email }}" readonly disabled>
                        <small class="text-muted" style="font-size: 0.75rem;">Contact Admin to request email changes.</small>
                    </div>

                    <!-- Editable Demographic Fields -->
                    @php
                        $profile = $user->patientProfile ?? $user->staffProfile;
                    @endphp
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control" value="{{ $profile && $profile->date_of_birth ? $profile->date_of_birth->format('Y-m-d') : '' }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Blood Group</label>
                        <input type="text" name="blood_group" class="form-control" value="{{ $profile->blood_group ?? '' }}" placeholder="e.g. O+, B+">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">City</label>
                        <input type="text" name="city" class="form-control" value="{{ $profile->city ?? 'Hyderabad' }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-bold">Residential Street Address</label>
                        <input type="text" name="address" class="form-control" value="{{ $profile->address ?? '' }}" placeholder="Flat/House No, Street, Landmark">
                    </div>

                    @if($user->isPatient())
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Emergency Contact Name</label>
                            <input type="text" name="emergency_contact_name" class="form-control" value="{{ $user->patientProfile->emergency_contact_name ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Emergency Contact Phone</label>
                            <input type="text" name="emergency_contact_phone" class="form-control" value="{{ $user->patientProfile->emergency_contact_phone ?? '' }}">
                        </div>
                    @endif

                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary btn-sm px-4">Save Demographics</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Password Change Card -->
        <div class="card card-custom p-4">
            <h6 class="fw-bold mb-3 border-bottom pb-2">Change Password</h6>
            <form action="{{ route('profile.password') }}" method="POST">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label small fw-bold">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required placeholder="Enter current password">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">New Password</label>
                        <input type="password" name="password" class="form-control" required placeholder="At least 6 characters">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required placeholder="Re-type new password">
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-dark btn-sm px-4">Update Password</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
