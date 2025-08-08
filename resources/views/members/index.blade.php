@extends('layouts.app')

@section('title', 'Members')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Members</h4>
        <a href="{{ route('members.create') }}" class="btn btn-primary">
          <i class="icon-base ri ri-user-add-line me-2"></i>
          Add New Member
        </a>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Member</th>
                <th>Membership ID</th>
                <th>Phone</th>
                <th>Total Visits</th>
                <th>Last Visit</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-3">
                      <img src="{{ asset('assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle" />
                    </div>
                    <div>
                      <h6 class="mb-0">John Doe</h6>
                      <small class="text-muted">john@example.com</small>
                    </div>
                  </div>
                </td>
                <td><span class="badge bg-label-primary">MS001</span></td>
                <td>+255 123 456 789</td>
                <td>12 visits</td>
                <td>2 hours ago</td>
                <td><span class="badge bg-label-success">Active</span></td>
                <td>
                  <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                      Actions
                    </button>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="#"><i class="icon-base ri ri-eye-line me-2"></i>View</a></li>
                      <li><a class="dropdown-item" href="#"><i class="icon-base ri ri-edit-line me-2"></i>Edit</a></li>
                      <li><a class="dropdown-item" href="#"><i class="icon-base ri ri-restaurant-line me-2"></i>Record Visit</a></li>
                      <li><hr class="dropdown-divider"></li>
                      <li><a class="dropdown-item text-danger" href="#"><i class="icon-base ri ri-delete-bin-line me-2"></i>Delete</a></li>
                    </ul>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-3">
                      <img src="{{ asset('assets/img/avatars/2.png') }}" alt="Avatar" class="rounded-circle" />
                    </div>
                    <div>
                      <h6 class="mb-0">Jane Smith</h6>
                      <small class="text-muted">jane@example.com</small>
                    </div>
                  </div>
                </td>
                <td><span class="badge bg-label-primary">MS002</span></td>
                <td>+255 987 654 321</td>
                <td>8 visits</td>
                <td>1 day ago</td>
                <td><span class="badge bg-label-success">Active</span></td>
                <td>
                  <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                      Actions
                    </button>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="#"><i class="icon-base ri ri-eye-line me-2"></i>View</a></li>
                      <li><a class="dropdown-item" href="#"><i class="icon-base ri ri-edit-line me-2"></i>Edit</a></li>
                      <li><a class="dropdown-item" href="#"><i class="icon-base ri ri-restaurant-line me-2"></i>Record Visit</a></li>
                      <li><hr class="dropdown-divider"></li>
                      <li><a class="dropdown-item text-danger" href="#"><i class="icon-base ri ri-delete-bin-line me-2"></i>Delete</a></li>
                    </ul>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection 