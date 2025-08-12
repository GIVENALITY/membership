@extends('layouts.app')

@section('title', 'Edit Member')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Edit Member</h4>
        <span class="badge bg-label-primary">{{ $member->membership_id }}</span>
      </div>
      <div class="card-body">
        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('members.update', $member) }}">
          @csrf
          @method('PUT')

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">First Name</label>
              <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $member->first_name) }}" required>
              @error('first_name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Last Name</label>
              <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $member->last_name) }}" required>
              @error('last_name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $member->email) }}" required>
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Phone</label>
              <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $member->phone) }}" required>
              @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Birth Date</label>
              <input type="date" name="birth_date" class="form-control @error('birth_date') is-invalid @enderror" value="{{ old('birth_date', $member->birth_date ? $member->birth_date->format('Y-m-d') : '') }}">
              @error('birth_date')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Membership Type</label>
              <select name="membership_type_id" class="form-select @error('membership_type_id') is-invalid @enderror" required>
                @foreach($membershipTypes as $type)
                  <option value="{{ $type->id }}" {{ (old('membership_type_id', $member->membership_type_id) == $type->id) ? 'selected' : '' }}>
                    {{ $type->name }} - {{ $type->formatted_price }}
                  </option>
                @endforeach
              </select>
              @error('membership_type_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Status</label>
              <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                <option value="active" {{ old('status', $member->status)=='active'?'selected':'' }}>Active</option>
                <option value="inactive" {{ old('status', $member->status)=='inactive'?'selected':'' }}>Inactive</option>
                <option value="suspended" {{ old('status', $member->status)=='suspended'?'selected':'' }}>Suspended</option>
              </select>
              @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address', $member->address) }}</textarea>
            @error('address')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- Member Details Section -->
          <div class="card mb-3">
            <div class="card-header">
              <h6 class="mb-0">
                <i class="icon-base ri ri-heart-line me-2"></i>
                Member Details & Preferences
              </h6>
              <small class="text-muted">This information helps us provide better service</small>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Allergies</label>
                  <textarea name="allergies" class="form-control @error('allergies') is-invalid @enderror" rows="3" placeholder="e.g., Peanuts, Shellfish, Dairy, etc. (Leave blank if none)">{{ old('allergies', $member->allergies) }}</textarea>
                  @error('allergies')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Dietary Preferences</label>
                  <textarea name="dietary_preferences" class="form-control @error('dietary_preferences') is-invalid @enderror" rows="3" placeholder="e.g., Vegetarian, Vegan, Halal, Kosher, etc.">{{ old('dietary_preferences', $member->dietary_preferences) }}</textarea>
                  @error('dietary_preferences')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Special Requests</label>
                  <textarea name="special_requests" class="form-control @error('special_requests') is-invalid @enderror" rows="3" placeholder="e.g., Wheelchair accessible, Quiet table, etc.">{{ old('special_requests', $member->special_requests) }}</textarea>
                  @error('special_requests')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Additional Notes</label>
                  <textarea name="additional_notes" class="form-control @error('additional_notes') is-invalid @enderror" rows="3" placeholder="Any other important information about the member">{{ old('additional_notes', $member->additional_notes) }}</textarea>
                  @error('additional_notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <!-- Emergency Contact -->
              <div class="border-top pt-3">
                <h6 class="mb-3">
                  <i class="icon-base ri ri-phone-line me-2"></i>
                  Emergency Contact
                </h6>
                <div class="row">
                  <div class="col-md-4 mb-3">
                    <label class="form-label">Contact Name</label>
                    <input type="text" name="emergency_contact_name" class="form-control @error('emergency_contact_name') is-invalid @enderror" placeholder="Full name" value="{{ old('emergency_contact_name', $member->emergency_contact_name) }}">
                    @error('emergency_contact_name')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-4 mb-3">
                    <label class="form-label">Contact Phone</label>
                    <input type="tel" name="emergency_contact_phone" class="form-control @error('emergency_contact_phone') is-invalid @enderror" placeholder="+255 123 456 789" value="{{ old('emergency_contact_phone', $member->emergency_contact_phone) }}">
                    @error('emergency_contact_phone')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-4 mb-3">
                    <label class="form-label">Relationship</label>
                    <input type="text" name="emergency_contact_relationship" class="form-control @error('emergency_contact_relationship') is-invalid @enderror" placeholder="e.g., Spouse, Parent, Friend" value="{{ old('emergency_contact_relationship', $member->emergency_contact_relationship) }}">
                    @error('emergency_contact_relationship')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('members.show', $member) }}" class="btn btn-outline-secondary">Cancel</a>
            <button class="btn btn-primary" type="submit"><i class="icon-base ri ri-save-line me-1"></i> Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection 