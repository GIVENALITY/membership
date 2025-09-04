@extends('layouts.app')

@section('title', __('app.members'))

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">{{ __('app.members') }}</h4>
        <div class="d-flex gap-2">
          <a href="{{ route('members.approval.index') }}" class="btn btn-warning">
            <i class="icon-base ri ri-user-check-line me-2"></i>
            Approval Workflow
          </a>
          <a href="{{ route('members.cards.index') }}" class="btn btn-info">
            <i class="icon-base ri ri-credit-card-line me-2"></i>
            {{ __('app.virtual_cards') }}
          </a>
          <a href="{{ route('members.physical-cards.index') }}" class="btn btn-warning">
            <i class="icon-base ri ri-credit-card-2-line me-2"></i>
            {{ __('app.physical_cards') }}
          </a>
          <a href="{{ route('members.import') }}" class="btn btn-success">
            <i class="icon-base ri ri-upload-line me-2"></i>
            {{ __('app.import_members') }}
          </a>
          <a href="{{ route('members.create') }}" class="btn btn-primary">
            <i class="icon-base ri ri-user-add-line me-2"></i>
            {{ __('app.add_member') }}
          </a>
        </div>
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
                <th>{{ __('app.details') }}</th>
                <th>{{ __('app.activity') }}</th>
                <th>{{ __('app.status') }}</th>
                <th width="120">{{ __('app.actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse($members as $member)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar avatar-sm me-2">
                        <img src="{{ asset('assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle" />
                      </div>
                      <div>
                        <h6 class="mb-0">{{ $member->full_name }}</h6>
                        <small class="text-muted">{{ $member->email }}</small>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="small">
                      <div class="mb-1">
                        <span class="badge bg-label-primary">{{ $member->membership_id }}</span>
                        @if($member->membershipType)
                          <span class="badge bg-label-info">{{ $member->membershipType->name }}</span>
                        @endif
                      </div>
                      <div class="text-muted">{{ $member->phone }}</div>
                    </div>
                  </td>
                  <td>
                    <div class="small">
                      <div class="fw-semibold">{{ $member->total_visits }} {{ __('app.total_visits') }}</div>
                      <div class="text-muted">{{ $member->last_visit_at ? $member->last_visit_at->diffForHumans() : __('app.never') }}</div>
                    </div>
                  </td>
                  <td>
                    <div class="small">
                      <div class="mb-1">
                        @if($member->status === 'active')
                          <span class="badge bg-label-success">{{ __('app.active') }}</span>
                        @elseif($member->status === 'inactive')
                          <span class="badge bg-label-secondary">{{ __('app.inactive') }}</span>
                        @else
                          <span class="badge bg-label-danger">{{ __('app.suspended') }}</span>
                        @endif
                      </div>
                      
                      @if($member->card_image_path)
                        <span class="badge bg-label-success">
                          <i class="icon-base ri ri-check-line me-1"></i>
                          {{ __('app.card_available') }}
                        </span>
                      @else
                        <span class="badge bg-label-warning">
                          <i class="icon-base ri ri-error-warning-line me-1"></i>
                          {{ __('app.no_card') }}
                        </span>
                      @endif
                    </div>
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
                        @if($member->card_image_path)
                          <li><a class="dropdown-item" href="{{ route('members.cards.view', $member) }}" target="_blank"><i class="icon-base ri ri-credit-card-line me-2"></i>{{ __('app.view_card') }}</a></li>
                          <li><a class="dropdown-item" href="{{ route('members.cards.download', $member) }}"><i class="icon-base ri ri-download-line me-2"></i>{{ __('app.download_card') }}</a></li>
                        @else
                          <li>
                            <form action="{{ route('members.cards.generate', $member) }}" method="POST" class="d-inline">
                              @csrf
                              <button type="submit" class="dropdown-item">
                                <i class="icon-base ri ri-add-line me-2"></i>{{ __('app.generate_card') }}
                              </button>
                            </form>
                          </li>
                        @endif
                        
                        <li><hr class="dropdown-divider"></li>
                        
                        @if($member->physical_card_status === 'not_issued')
                          <li>
                            <a class="dropdown-item" href="{{ route('members.physical-cards.issue-form', $member) }}">
                              <i class="icon-base ri ri-credit-card-2-line me-2"></i>{{ __('app.issue_card') }}
                            </a>
                          </li>
                        @else
                          <li>
                            <a class="dropdown-item" href="{{ route('members.physical-cards.index') }}?status={{ $member->physical_card_status }}">
                              <i class="icon-base ri ri-credit-card-2-line me-2"></i>{{ __('app.physical_card_status') }}: {{ $member->getPhysicalCardStatusText() }}
                            </a>
                          </li>
                        @endif
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