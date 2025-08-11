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
              <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $member->first_name) }}" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Last Name</label>
              <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $member->last_name) }}" required>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" value="{{ old('email', $member->email) }}" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Phone</label>
              <input type="text" name="phone" class="form-control" value="{{ old('phone', $member->phone) }}" required>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Membership Type</label>
              <select name="membership_type_id" class="form-select" required>
                @foreach($membershipTypes as $type)
                  <option value="{{ $type->id }}" {{ (old('membership_type_id', $member->membership_type_id) == $type->id) ? 'selected' : '' }}>
                    {{ $type->name }} - {{ $type->formatted_price }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Status</label>
              <select name="status" class="form-select" required>
                <option value="active" {{ old('status', $member->status)=='active'?'selected':'' }}>Active</option>
                <option value="inactive" {{ old('status', $member->status)=='inactive'?'selected':'' }}>Inactive</option>
                <option value="suspended" {{ old('status', $member->status)=='suspended'?'selected':'' }}>Suspended</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" rows="3">{{ old('address', $member->address) }}</textarea>
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