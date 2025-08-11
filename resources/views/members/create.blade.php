@extends('layouts.app')

@section('title', 'Add New Member')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Add New Member</h4>
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

        @if (session('error'))
          <div class="alert alert-danger">
            {{ session('error') }}
          </div>
        @endif

        <form method="POST" action="{{ route('members.store') }}">
          @csrf
                      <div class="row">
              <div class="col-md-6 mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                       id="first_name" name="first_name" placeholder="Enter first name" 
                       value="{{ old('first_name') }}" required>
                @error('first_name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-6 mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                       id="last_name" name="last_name" placeholder="Enter last name" 
                       value="{{ old('last_name') }}" required>
                @error('last_name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       id="email" name="email" placeholder="Enter email address" 
                       value="{{ old('email') }}" required>
                @error('email')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-6 mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                       id="phone" name="phone" placeholder="+255 123 456 789" 
                       value="{{ old('phone') }}" required>
                @error('phone')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="membership_id" class="form-label">Membership ID</label>
                <input type="text" class="form-control" id="membership_id" 
                       value="{{ $membershipId ?? 'MS001' }}" readonly>
                <small class="text-muted">Auto-generated membership ID</small>
              </div>
              <div class="col-md-6 mb-3">
                <label for="birth_date" class="form-label">Birth Date</label>
                <input type="date" class="form-control @error('birth_date') is-invalid @enderror" 
                       id="birth_date" name="birth_date" value="{{ old('birth_date') }}" required>
                @error('birth_date')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            
            <div class="mb-3">
              <label for="address" class="form-label">Address</label>
              <textarea class="form-control @error('address') is-invalid @enderror" 
                        id="address" name="address" rows="3" placeholder="Enter address">{{ old('address') }}</textarea>
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
                    <label for="allergies" class="form-label">Allergies</label>
                    <textarea class="form-control @error('allergies') is-invalid @enderror" 
                              id="allergies" name="allergies" rows="3" 
                              placeholder="e.g., Peanuts, Shellfish, Dairy, etc. (Leave blank if none)">{{ old('allergies') }}</textarea>
                    @error('allergies')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="dietary_preferences" class="form-label">Dietary Preferences</label>
                    <textarea class="form-control @error('dietary_preferences') is-invalid @enderror" 
                              id="dietary_preferences" name="dietary_preferences" rows="3" 
                              placeholder="e.g., Vegetarian, Vegan, Halal, Kosher, etc.">{{ old('dietary_preferences') }}</textarea>
                    @error('dietary_preferences')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="special_requests" class="form-label">Special Requests</label>
                    <textarea class="form-control @error('special_requests') is-invalid @enderror" 
                              id="special_requests" name="special_requests" rows="3" 
                              placeholder="e.g., Wheelchair accessible, Quiet table, etc.">{{ old('special_requests') }}</textarea>
                    @error('special_requests')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="additional_notes" class="form-label">Additional Notes</label>
                    <textarea class="form-control @error('additional_notes') is-invalid @enderror" 
                              id="additional_notes" name="additional_notes" rows="3" 
                              placeholder="Any other important information about the member">{{ old('additional_notes') }}</textarea>
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
                      <label for="emergency_contact_name" class="form-label">Contact Name</label>
                      <input type="text" class="form-control @error('emergency_contact_name') is-invalid @enderror" 
                             id="emergency_contact_name" name="emergency_contact_name" 
                             placeholder="Full name" value="{{ old('emergency_contact_name') }}">
                      @error('emergency_contact_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                      <label for="emergency_contact_phone" class="form-label">Contact Phone</label>
                      <input type="tel" class="form-control @error('emergency_contact_phone') is-invalid @enderror" 
                             id="emergency_contact_phone" name="emergency_contact_phone" 
                             placeholder="+255 123 456 789" value="{{ old('emergency_contact_phone') }}">
                      @error('emergency_contact_phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                      <label for="emergency_contact_relationship" class="form-label">Relationship</label>
                      <input type="text" class="form-control @error('emergency_contact_relationship') is-invalid @enderror" 
                             id="emergency_contact_relationship" name="emergency_contact_relationship" 
                             placeholder="e.g., Spouse, Parent, Friend" value="{{ old('emergency_contact_relationship') }}">
                      @error('emergency_contact_relationship')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="membership_type_id" class="form-label">Membership Type</label>
                <select class="form-select @error('membership_type_id') is-invalid @enderror" 
                        id="membership_type_id" name="membership_type_id" required>
                  <option value="">Select membership type</option>
                  @foreach($membershipTypes as $type)
                    <option value="{{ $type->id }}" {{ old('membership_type_id') == $type->id ? 'selected' : '' }}>
                      {{ $type->name }} - {{ $type->formatted_price }}
                    </option>
                  @endforeach
                </select>
                @error('membership_type_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-6 mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select @error('status') is-invalid @enderror" 
                        id="status" name="status" required>
                  <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                  <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                  <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
                @error('status')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          
          <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('members.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
              <i class="icon-base ri ri-user-add-line me-2"></i>
              Create Member
            </button>
          </div>
        </form>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection 