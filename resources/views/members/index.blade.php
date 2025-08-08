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
        @if (session('success'))
          <div class="alert alert-success">
            {{ session('success') }}
          </div>
        @endif

        @if (session('error'))
          <div class="alert alert-danger">
            {{ session('error') }}
          </div>
        @endif

        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Member</th>
                <th>Membership ID</th>
                <th>Membership Type</th>
                <th>Phone</th>
                <th>Total Visits</th>
                <th>Last Visit</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($members as $member)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar avatar-sm me-3">
                        <img src="{{ asset('assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle" />
                      </div>
                      <div>
                        <h6 class="mb-0">{{ $member->full_name }}</h6>
                        <small class="text-muted">{{ $member->email }}</small>
                      </div>
                    </div>
                  </td>
                  <td><span class="badge bg-label-primary">{{ $member->membership_id }}</span></td>
                  <td>
                    @if($member->membershipType)
                      <span class="badge bg-label-info">{{ $member->membershipType->name }}</span>
                    @else
                      <span class="text-muted">N/A</span>
                    @endif
                  </td>
                  <td>{{ $member->phone }}</td>
                  <td>{{ $member->total_visits }} visits</td>
                  <td>{{ $member->last_visit_at ? $member->last_visit_at->diffForHumans() : 'Never' }}</td>
                  <td>
                    @if($member->status === 'active')
                      <span class="badge bg-label-success">Active</span>
                    @elseif($member->status === 'inactive')
                      <span class="badge bg-label-secondary">Inactive</span>
                    @else
                      <span class="badge bg-label-danger">Suspended</span>
                    @endif
                  </td>
                  <td>
                    <div class="dropdown">
                      <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Actions
                      </button>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('members.show', $member) }}"><i class="icon-base ri ri-eye-line me-2"></i>View</a></li>
                        <li><a class="dropdown-item" href="{{ route('members.edit', $member) }}"><i class="icon-base ri ri-edit-line me-2"></i>Edit</a></li>
                        <li><a class="dropdown-item" href="#"><i class="icon-base ri ri-restaurant-line me-2"></i>Record Visit</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                          <form action="{{ route('members.destroy', $member) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this member?')">
                              <i class="icon-base ri ri-delete-bin-line me-2"></i>Delete
                            </button>
                          </form>
                        </li>
                      </ul>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center">No members found. <a href="{{ route('members.create') }}">Add your first member</a></td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection 