@extends('layouts.app')

@section('title', 'Member Alerts')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Member Alerts</h4>
        <a href="{{ route('alerts.create') }}" class="btn btn-primary">
          <i class="icon-base ri ri-add-line me-1"></i>
          Create Alert
        </a>
      </div>
      <div class="card-body">
        @if(session('success'))
          <div class="alert alert-success">
            {{ session('success') }}
          </div>
        @endif

        <!-- Active Alerts -->
        <div class="row">
          <div class="col-12">
            <h5 class="mb-3">
              <i class="icon-base ri ri-notification-line me-2"></i>
              Alert Rules ({{ $alerts->count() }})
            </h5>
          </div>
        </div>

        @if($alerts->count() > 0)
          <div class="row">
            @foreach($alerts as $alert)
              <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100" style="border-left: 4px solid {{ $alert->color }}">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-2">
                          <span class="avatar-initial rounded" style="background-color: {{ $alert->color }}; color: white;">
                            <i class="icon-base {{ $alert->icon }}"></i>
                          </span>
                        </div>
                        <div>
                          <h6 class="mb-0">{{ $alert->name }}</h6>
                          <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $alert->type)) }}</small>
                        </div>
                      </div>
                      <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                          <i class="icon-base ri ri-more-2-fill"></i>
                        </button>
                        <ul class="dropdown-menu">
                          <li><a class="dropdown-item" href="{{ route('alerts.edit', $alert) }}">
                            <i class="icon-base ri ri-edit-line me-2"></i>Edit
                          </a></li>
                          <li><a class="dropdown-item" href="{{ route('alerts.triggers', $alert) }}">
                            <i class="icon-base ri ri-list-check me-2"></i>View Triggers
                          </a></li>
                          <li><a class="dropdown-item" href="#" onclick="testAlert({{ $alert->id }})">
                            <i class="icon-base ri ri-bug-line me-2"></i>Test Alert
                          </a></li>
                          <li><hr class="dropdown-divider"></li>
                          <li><a class="dropdown-item text-danger" href="#" onclick="deleteAlert({{ $alert->id }})">
                            <i class="icon-base ri ri-delete-bin-line me-2"></i>Delete
                          </a></li>
                        </ul>
                      </div>
                    </div>

                    @if($alert->description)
                      <p class="text-muted small mb-2">{{ $alert->description }}</p>
                    @endif

                    <div class="mb-2">
                      <span class="badge {{ $alert->badge_color }}">{{ ucfirst($alert->severity) }}</span>
                      @if($alert->is_active)
                        <span class="badge bg-label-success">Active</span>
                      @else
                        <span class="badge bg-label-secondary">Inactive</span>
                      @endif
                    </div>

                    <div class="small text-muted">
                      <div class="row">
                        <div class="col-6">
                          <i class="icon-base ri ri-dashboard-line me-1"></i>
                          {{ $alert->show_dashboard ? 'Dashboard' : 'Hidden' }}
                        </div>
                        <div class="col-6">
                          <i class="icon-base ri ri-eye-line me-1"></i>
                          {{ $alert->show_quickview ? 'QuickView' : 'Hidden' }}
                        </div>
                      </div>
                      @if($alert->send_email)
                        <div class="mt-1">
                          <i class="icon-base ri ri-mail-line me-1"></i>
                          Email notifications enabled
                        </div>
                      @endif
                    </div>

                    <div class="mt-3">
                      <a href="{{ route('alerts.edit', $alert) }}" class="btn btn-sm btn-outline-primary">
                        <i class="icon-base ri ri-edit-line me-1"></i>
                        Edit
                      </a>
                      <a href="{{ route('alerts.triggers', $alert) }}" class="btn btn-sm btn-outline-info">
                        <i class="icon-base ri ri-list-check me-1"></i>
                        Triggers
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="text-center py-5">
            <div class="mb-3">
              <i class="icon-base ri ri-notification-off-line text-muted" style="font-size: 4rem;"></i>
            </div>
            <h5 class="text-muted">No Alerts Created</h5>
            <p class="text-muted">Create your first alert to start monitoring member activities.</p>
            <a href="{{ route('alerts.create') }}" class="btn btn-primary">
              <i class="icon-base ri ri-add-line me-1"></i>
              Create Your First Alert
            </a>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Active Triggers -->
@if($activeTriggers->count() > 0)
<div class="row mt-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">
          <i class="icon-base ri ri-alarm-warning-line me-2 text-warning"></i>
          Active Alerts ({{ $activeTriggers->count() }})
        </h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Member</th>
                <th>Alert</th>
                <th>Triggered</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($activeTriggers as $trigger)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar avatar-sm me-3">
                        <div class="bg-label-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                          <i class="icon-base ri ri-user-line text-primary"></i>
                        </div>
                      </div>
                      <div>
                        <h6 class="mb-0">{{ $trigger->member->full_name }}</h6>
                        <small class="text-muted">{{ $trigger->member->membership_id }}</small>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar avatar-sm me-2">
                        <span class="avatar-initial rounded" style="background-color: {{ $trigger->alert->color }}; color: white;">
                          <i class="icon-base {{ $trigger->alert->icon }}"></i>
                        </span>
                      </div>
                      <div>
                        <div class="fw-semibold">{{ $trigger->alert->name }}</div>
                        <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $trigger->alert->type)) }}</small>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="small">
                      <div>{{ $trigger->triggered_at->format('M d, Y') }}</div>
                      <div class="text-muted">{{ $trigger->triggered_at->format('g:i A') }}</div>
                    </div>
                  </td>
                  <td>
                    <span class="badge {{ $trigger->status_badge_color }}">{{ $trigger->status_text }}</span>
                  </td>
                  <td>
                    <div class="btn-group btn-group-sm">
                      <button class="btn btn-outline-info" onclick="acknowledgeAlert({{ $trigger->id }})">
                        <i class="icon-base ri ri-check-line"></i>
                      </button>
                      <button class="btn btn-outline-success" onclick="resolveAlert({{ $trigger->id }})">
                        <i class="icon-base ri ri-close-line"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endif

<!-- Delete Alert Modal -->
<div class="modal fade" id="deleteAlertModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete Alert</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this alert? This action cannot be undone.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form id="deleteAlertForm" method="POST" style="display: inline;">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">Delete Alert</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Acknowledge Alert Modal -->
<div class="modal fade" id="acknowledgeAlertModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Acknowledge Alert</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="acknowledgeAlertForm" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label for="acknowledgeNotes" class="form-label">Notes (Optional)</label>
            <textarea class="form-control" id="acknowledgeNotes" name="notes" rows="3" placeholder="Add any notes about this acknowledgment..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-info">Acknowledge</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Resolve Alert Modal -->
<div class="modal fade" id="resolveAlertModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Resolve Alert</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="resolveAlertForm" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label for="resolveNotes" class="form-label">Resolution Notes (Optional)</label>
            <textarea class="form-control" id="resolveNotes" name="notes" rows="3" placeholder="Add notes about how this alert was resolved..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Resolve</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function deleteAlert(alertId) {
  if (confirm('Are you sure you want to delete this alert?')) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/alerts/${alertId}`;
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    
    const methodField = document.createElement('input');
    methodField.type = 'hidden';
    methodField.name = '_method';
    methodField.value = 'DELETE';
    
    form.appendChild(csrfToken);
    form.appendChild(methodField);
    document.body.appendChild(form);
    form.submit();
  }
}

function testAlert(alertId) {
  fetch(`/alerts/${alertId}/test`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert(data.message);
    } else {
      alert('Error testing alert: ' + data.message);
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Error testing alert');
  });
}

function acknowledgeAlert(triggerId) {
  const form = document.getElementById('acknowledgeAlertForm');
  form.action = `/alerts/triggers/${triggerId}/acknowledge`;
  new bootstrap.Modal(document.getElementById('acknowledgeAlertModal')).show();
}

function resolveAlert(triggerId) {
  const form = document.getElementById('resolveAlertForm');
  form.action = `/alerts/triggers/${triggerId}/resolve`;
  new bootstrap.Modal(document.getElementById('resolveAlertModal')).show();
}
</script>
@endsection
