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
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="membership_type" class="form-label">Membership Type</label>
                <select class="form-select @error('membership_type') is-invalid @enderror" 
                        id="membership_type" name="membership_type" required>
                  <option value="">Select membership type</option>
                  <option value="basic" {{ old('membership_type') == 'basic' ? 'selected' : '' }}>Basic</option>
                  <option value="premium" {{ old('membership_type') == 'premium' ? 'selected' : '' }}>Premium</option>
                  <option value="vip" {{ old('membership_type') == 'vip' ? 'selected' : '' }}>VIP</option>
                </select>
                @error('membership_type')
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