@extends('layouts.app')

@section('title', __('app.members'))

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">{{ __('app.members') }}</h4>
        <a href="{{ route('members.create') }}" class="btn btn-primary">
          <i class="icon-base ri ri-user-add-line me-2"></i>
          {{ __('app.add_member') }}
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
                <th>{{ __('app.member') }}</th>
                <th>{{ __('app.membership_id') }}</th>
                <th>{{ __('app.membership_type') }}</th>
                <th>{{ __('app.phone') }}</th>
                <th>{{ __('app.total_visits') }}</th>
                <th>{{ __('app.last_visit') }}</th>
                <th>{{ __('app.status') }}</th>
                <th>{{ __('app.actions') }}</th>
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
                  <td>{{ $member->total_visits }} {{ __('app.total_visits') }}</td>
                  <td>{{ $member->last_visit_at ? $member->last_visit_at->diffForHumans() : 'Never' }}</td>
                  <td>
                    @if($member->status === 'active')
                      <span class="badge bg-label-success">{{ __('app.active') }}</span>
                    @elseif($member->status === 'inactive')
                      <span class="badge bg-label-secondary">{{ __('app.inactive') }}</span>
                    @else
                      <span class="badge bg-label-danger">{{ __('app.suspended') }}</span>
                    @endif
                  </td>
                  <td>
                    <div class="dropdown">
                      <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        {{ __('app.actions') }}
                      </button>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('members.show', $member) }}"><i class="icon-base ri ri-eye-line me-2"></i>{{ __('app.view') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('members.edit', $member) }}"><i class="icon-base ri ri-edit-line me-2"></i>{{ __('app.edit') }}</a></li>
                        <li><a class="dropdown-item" href="#"><i class="icon-base ri ri-restaurant-line me-2"></i>{{ __('app.record_visit') }}</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                          <form action="{{ route('members.destroy', $member) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('{{ __('app.confirm_delete_member') }}')">
                              <i class="icon-base ri ri-delete-bin-line me-2"></i>{{ __('app.delete') }}
                            </button>
                          </form>
                        </li>
                      </ul>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="text-center py-4">
                    <div class="d-flex flex-column align-items-center">
                      <i class="icon-base ri ri-user-star-line icon-4x text-muted mb-3"></i>
                      <h5>{{ __('app.no_members_found') }}</h5>
                      <p class="text-muted">{{ __('app.no_members_description') }}</p>
                      <a href="{{ route('members.create') }}" class="btn btn-primary">
                        <i class="icon-base ri ri-user-add-line me-2"></i>
                        {{ __('app.add_first_member') }}
                      </a>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if($members->hasPages())
          <div class="d-flex justify-content-center mt-4">
            {{ $members->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection 