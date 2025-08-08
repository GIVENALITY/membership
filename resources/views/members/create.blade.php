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
        <form>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="firstName" class="form-label">First Name</label>
              <input type="text" class="form-control" id="firstName" placeholder="Enter first name" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="lastName" class="form-label">Last Name</label>
              <input type="text" class="form-control" id="lastName" placeholder="Enter last name" required>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" placeholder="Enter email address" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="phone" class="form-label">Phone Number</label>
              <input type="tel" class="form-control" id="phone" placeholder="+255 123 456 789" required>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="membershipId" class="form-label">Membership ID</label>
              <input type="text" class="form-control" id="membershipId" placeholder="MS001" readonly>
              <small class="text-muted">Auto-generated membership ID</small>
            </div>
            <div class="col-md-6 mb-3">
              <label for="joinDate" class="form-label">Join Date</label>
              <input type="date" class="form-control" id="joinDate" value="{{ date('Y-m-d') }}" required>
            </div>
          </div>
          
          <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea class="form-control" id="address" rows="3" placeholder="Enter address"></textarea>
          </div>
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="membershipType" class="form-label">Membership Type</label>
              <select class="form-select" id="membershipType" required>
                <option value="">Select membership type</option>
                <option value="basic">Basic</option>
                <option value="premium">Premium</option>
                <option value="vip">VIP</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label for="status" class="form-label">Status</label>
              <select class="form-select" id="status" required>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="suspended">Suspended</option>
              </select>
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
      </div>
    </div>
  </div>
</div>
@endsection 