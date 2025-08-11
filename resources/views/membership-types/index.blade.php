@extends('layouts.app')

@section('title', 'Membership Types')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Membership Types</h4>
        <div class="d-flex gap-2">
          <a href="{{ route('membership-types.create') }}" class="btn btn-primary">
            <i class="icon-base ri ri-add-line me-2"></i>
            Add New Type
          </a>
          @if($membershipTypes->count() > 0)
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAllModal">
              <i class="icon-base ri ri-delete-bin-line me-2"></i>
              Delete All
            </button>
          @endif
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

        <div class="row">
          @forelse($membershipTypes as $type)
            <div class="col-md-6 col-lg-4 mb-4">
              <div class="card h-100 {{ $type->is_active ? 'border-success' : 'border-secondary' }}">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h5 class="card-title mb-0">{{ $type->name }}</h5>
                  <div>
                    @if($type->is_active)
                      <span class="badge bg-label-success">Active</span>
                    @else
                      <span class="badge bg-label-secondary">Inactive</span>
                    @endif
                  </div>
                </div>
                <div class="card-body">
                  @if($type->description)
                    <p class="text-muted mb-3">{{ $type->description }}</p>
                  @endif
                  
                  <div class="mb-3">
                    <h6 class="text-primary mb-1">{{ $type->formatted_price }}</h6>
                    <small class="text-muted">{{ $type->visits_limit_text }}</small>
                  </div>

                  <div class="mb-3">
                    <h6 class="mb-2">Perks:</h6>
                    {!! $type->perks_list !!}
                  </div>

                  <div class="mb-3">
                    <span class="badge bg-label-info">Base Discount: {{ $type->discount_rate }}%</span>
                  </div>

                  @if(!empty($type->discount_progression))
                    <div class="mb-3">
                      <h6 class="mb-2">Discount Progression:</h6>
                      {!! $type->discount_progression_html !!}
                    </div>
                  @endif

                  <div class="mb-3">
                    <div class="row">
                      <div class="col-6">
                        <small class="text-muted d-block">Points Required: {{ $type->points_required_for_discount }}</small>
                      </div>
                      <div class="col-6">
                        <small class="text-muted d-block">Consecutive Bonus: {{ $type->consecutive_visits_for_bonus }} visits</small>
                      </div>
                    </div>
                    @if($type->has_special_birthday_discount)
                      <small class="text-muted d-block">🎂 Birthday: {{ $type->birthday_discount_rate }}%</small>
                    @endif
                  </div>

                  <!-- Points Reset Policy -->
                  <div class="mb-3">
                    <h6 class="mb-2">Points Reset Policy:</h6>
                    <div class="alert alert-sm {{ $type->points_reset_after_redemption ? 'alert-warning' : 'alert-success' }}">
                      <i class="icon-base ri {{ $type->points_reset_after_redemption ? 'ri-refresh-line' : 'ri-check-line' }} me-2"></i>
                      <small>{{ $type->points_reset_policy }}</small>
                    </div>
                    @if($type->points_reset_notes && $type->points_reset_notes !== 'No additional notes')
                      <small class="text-muted d-block">{{ $type->points_reset_notes }}</small>
                    @endif
                  </div>

                  <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">{{ $type->members()->count() }} member(s)</small>
                    <div class="dropdown">
                      <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Actions
                      </button>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('membership-types.show', $type) }}"><i class="icon-base ri ri-eye-line me-2"></i>View</a></li>
                        <li><a class="dropdown-item" href="{{ route('membership-types.edit', $type) }}"><i class="icon-base ri ri-edit-line me-2"></i>Edit</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                          <form action="{{ route('membership-types.destroy', $type) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this membership type?')">
                              <i class="icon-base ri ri-delete-bin-line me-2"></i>Delete
                            </button>
                          </form>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          @empty
            <div class="col-12">
              <div class="text-center py-5">
                <i class="icon-base ri ri-user-star-line icon-4x text-muted mb-3"></i>
                <h5>No Membership Types Found</h5>
                <p class="text-muted">Create your first membership type to get started.</p>
                <a href="{{ route('membership-types.create') }}" class="btn btn-primary">
                  <i class="icon-base ri ri-add-line me-2"></i>
                  Create First Type
                </a>
              </div>
            </div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Delete All Modal -->
<div class="modal fade" id="deleteAllModal" tabindex="-1" aria-labelledby="deleteAllModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-danger" id="deleteAllModalLabel">
          <i class="icon-base ri ri-alert-line me-2"></i>
          Delete All Membership Types
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-danger">
          <h6><i class="icon-base ri ri-error-warning-line me-2"></i>Warning: This action cannot be undone!</h6>
          <p class="mb-0">This will permanently delete <strong>ALL</strong> membership types and reset your system. All member data will be affected.</p>
        </div>
        
        <div class="mb-3">
          <h6>What will be deleted:</h6>
          <ul class="list-unstyled">
            <li><i class="icon-base ri ri-delete-bin-line text-danger me-2"></i>All membership types ({{ $membershipTypes->count() }} types)</li>
            <li><i class="icon-base ri ri-delete-bin-line text-danger me-2"></i>All member associations with types</li>
            <li><i class="icon-base ri ri-delete-bin-line text-danger me-2"></i>All discount progression rules</li>
            <li><i class="icon-base ri ri-delete-bin-line text-danger me-2"></i>All points reset configurations</li>
          </ul>
        </div>

        <div class="mb-3">
          <h6>What will be preserved:</h6>
          <ul class="list-unstyled">
            <li><i class="icon-base ri ri-check-line text-success me-2"></i>Member basic information</li>
            <li><i class="icon-base ri ri-check-line text-success me-2"></i>Dining visit history</li>
            <li><i class="icon-base ri ri-check-line text-success me-2"></i>Points history</li>
            <li><i class="icon-base ri ri-check-line text-success me-2"></i>Hotel settings</li>
          </ul>
        </div>

        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="confirmDeleteAll" required>
          <label class="form-check-label" for="confirmDeleteAll">
            I understand this action will delete all membership types and cannot be undone
          </label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form action="{{ route('membership-types.delete-all') }}" method="POST" style="display: inline;">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger" id="deleteAllBtn" disabled>
            <i class="icon-base ri ri-delete-bin-line me-2"></i>
            Delete All Membership Types
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const confirmCheckbox = document.getElementById('confirmDeleteAll');
  const deleteBtn = document.getElementById('deleteAllBtn');
  
  if (confirmCheckbox && deleteBtn) {
    confirmCheckbox.addEventListener('change', function() {
      deleteBtn.disabled = !this.checked;
    });
  }
});
</script>

@endsection 