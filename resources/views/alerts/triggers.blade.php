@extends('layouts.app')

@section('title', 'Alert Triggers')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div>
          <h4 class="card-title">Alert Triggers: {{ $alert->name }}</h4>
          <p class="card-subtitle text-muted">{{ ucfirst(str_replace('_', ' ', $alert->type)) }} Alert</p>
        </div>
        <a href="{{ route('alerts.index') }}" class="btn btn-secondary">
          <i class="icon-base ri ri-arrow-left-line me-1"></i>
          Back to Alerts
        </a>
      </div>
      <div class="card-body">
        @if(session('success'))
          <div class="alert alert-success">
            {{ session('success') }}
          </div>
        @endif

        <!-- Alert Info -->
        <div class="row mb-4">
          <div class="col-md-6">
            <div class="d-flex align-items-center mb-3">
              <div class="avatar avatar-sm me-3">
                <span class="avatar-initial rounded" style="background-color: {{ $alert->color }}; color: white;">
                  <i class="icon-base {{ $alert->icon }}"></i>
                </span>
              </div>
              <div>
                <h6 class="mb-0">{{ $alert->name }}</h6>
                <small class="text-muted">{{ $alert->description }}</small>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="d-flex justify-content-end">
              <span class="badge {{ $alert->badge_color }} me-2">{{ ucfirst($alert->severity) }}</span>
              @if($alert->is_active)
                <span class="badge bg-label-success">Active</span>
              @else
                <span class="badge bg-label-secondary">Inactive</span>
              @endif
            </div>
          </div>
        </div>

        <!-- Statistics -->
        <div class="row mb-4">
          <div class="col-md-3 mb-3">
            <div class="card bg-label-primary">
              <div class="card-body text-center">
                <h4 class="mb-1">{{ $triggers->total() }}</h4>
                <small class="text-muted">Total Triggers</small>
              </div>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <div class="card bg-label-warning">
              <div class="card-body text-center">
                <h4 class="mb-1">{{ $triggers->where('status', 'active')->count() }}</h4>
                <small class="text-muted">Active</small>
              </div>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <div class="card bg-label-info">
              <div class="card-body text-center">
                <h4 class="mb-1">{{ $triggers->where('status', 'acknowledged')->count() }}</h4>
                <small class="text-muted">Acknowledged</small>
              </div>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <div class="card bg-label-success">
              <div class="card-body text-center">
                <h4 class="mb-1">{{ $triggers->where('status', 'resolved')->count() }}</h4>
                <small class="text-muted">Resolved</small>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="card">
              <div class="card-body">
                <form method="GET" class="row g-3">
                  <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                      <option value="">All Statuses</option>
                      <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                      <option value="acknowledged" {{ request('status') == 'acknowledged' ? 'selected' : '' }}>Acknowledged</option>
                      <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" 
                           value="{{ request('date_from') }}">
                  </div>
                  <div class="col-md-3">
                    <label for="date_to" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" 
                           value="{{ request('date_to') }}">
                  </div>
                  <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                      <i class="icon-base ri ri-search-line me-1"></i>
                      Filter
                    </button>
                    <a href="{{ route('alerts.triggers', $alert) }}" class="btn btn-outline-secondary">
                      <i class="icon-base ri ri-refresh-line me-1"></i>
                      Clear
                    </a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>

        <!-- Triggers Table -->
        @if($triggers->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Member</th>
                  <th>Triggered</th>
                  <th>Status</th>
                  <th>Details</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($triggers as $trigger)
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
                      <div class="small">
                        <div>{{ $trigger->triggered_at->format('M d, Y') }}</div>
                        <div class="text-muted">{{ $trigger->triggered_at->format('g:i A') }}</div>
                        <div class="text-muted">{{ $trigger->time_since_triggered }}</div>
                      </div>
                    </td>
                    <td>
                      <span class="badge {{ $trigger->status_badge_color }}">{{ $trigger->status_text }}</span>
                      @if($trigger->acknowledged_at)
                        <div class="small text-muted mt-1">
                          Acknowledged: {{ $trigger->acknowledged_at->format('M d, g:i A') }}
                        </div>
                      @endif
                      @if($trigger->resolved_at)
                        <div class="small text-muted mt-1">
                          Resolved: {{ $trigger->resolved_at->format('M d, g:i A') }}
                        </div>
                      @endif
                    </td>
                    <td>
                      <div class="small">
                        @if($trigger->trigger_data)
                          @switch($alert->type)
                            @case('spending_threshold')
                              <div>Spent: TZS {{ number_format($trigger->trigger_data['current_spending'] ?? 0) }}</div>
                              <div>Threshold: TZS {{ number_format($trigger->trigger_data['threshold'] ?? 0) }}</div>
                              @break
                            @case('visit_frequency')
                              <div>Days since last visit: {{ $trigger->trigger_data['days_since_last_visit'] ?? 'N/A' }}</div>
                              <div>Threshold: {{ $trigger->trigger_data['threshold_days'] ?? 0 }} days</div>
                              @break
                            @case('points_threshold')
                              <div>Current points: {{ $trigger->trigger_data['current_points'] ?? 0 }}</div>
                              <div>Threshold: {{ $trigger->trigger_data['threshold_points'] ?? 0 }}</div>
                              @break
                            @case('birthday_approaching')
                              <div>Days until birthday: {{ $trigger->trigger_data['days_until_birthday'] ?? 'N/A' }}</div>
                              @break
                            @default
                              <div>Alert triggered</div>
                          @endswitch
                        @endif
                      </div>
                    </td>
                    <td>
                      <div class="btn-group btn-group-sm">
                        @if($trigger->status === 'active')
                          <button class="btn btn-outline-info" onclick="acknowledgeTrigger({{ $trigger->id }})" title="Acknowledge">
                            <i class="icon-base ri ri-check-line"></i>
                          </button>
                          <button class="btn btn-outline-success" onclick="resolveTrigger({{ $trigger->id }})" title="Resolve">
                            <i class="icon-base ri ri-close-line"></i>
                          </button>
                        @elseif($trigger->status === 'acknowledged')
                          <button class="btn btn-outline-success" onclick="resolveTrigger({{ $trigger->id }})" title="Resolve">
                            <i class="icon-base ri ri-close-line"></i>
                          </button>
                        @endif
                        <button class="btn btn-outline-secondary" onclick="viewTriggerDetails({{ $trigger->id }})" title="View Details">
                          <i class="icon-base ri ri-eye-line"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="d-flex justify-content-center mt-4">
            {{ $triggers->links() }}
          </div>
        @else
          <div class="text-center py-5">
            <div class="mb-3">
              <i class="icon-base ri ri-notification-off-line text-muted" style="font-size: 4rem;"></i>
            </div>
            <h5 class="text-muted">No Triggers Found</h5>
            <p class="text-muted">This alert hasn't been triggered yet.</p>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Acknowledge Trigger Modal -->
<div class="modal fade" id="acknowledgeTriggerModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Acknowledge Trigger</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="acknowledgeTriggerForm" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label for="acknowledgeNotes" class="form-label">Notes (Optional)</label>
            <textarea class="form-control" id="acknowledgeNotes" name="notes" rows="3" 
                      placeholder="Add any notes about this acknowledgment..."></textarea>
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

<!-- Resolve Trigger Modal -->
<div class="modal fade" id="resolveTriggerModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Resolve Trigger</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="resolveTriggerForm" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label for="resolveNotes" class="form-label">Resolution Notes (Optional)</label>
            <textarea class="form-control" id="resolveNotes" name="notes" rows="3" 
                      placeholder="Add notes about how this trigger was resolved..."></textarea>
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

<!-- Trigger Details Modal -->
<div class="modal fade" id="triggerDetailsModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Trigger Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="triggerDetailsContent">
        <!-- Content will be loaded here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
function acknowledgeTrigger(triggerId) {
  const form = document.getElementById('acknowledgeTriggerForm');
  form.action = `/alerts/triggers/${triggerId}/acknowledge`;
  new bootstrap.Modal(document.getElementById('acknowledgeTriggerModal')).show();
}

function resolveTrigger(triggerId) {
  const form = document.getElementById('resolveTriggerForm');
  form.action = `/alerts/triggers/${triggerId}/resolve`;
  new bootstrap.Modal(document.getElementById('resolveTriggerModal')).show();
}

function viewTriggerDetails(triggerId) {
  // For now, just show a simple message
  // In a real implementation, you might want to fetch detailed data via AJAX
  const content = document.getElementById('triggerDetailsContent');
  content.innerHTML = `
    <div class="text-center py-4">
      <i class="icon-base ri ri-information-line text-muted" style="font-size: 3rem;"></i>
      <h6 class="mt-3">Trigger Details</h6>
      <p class="text-muted">Detailed trigger information would be displayed here.</p>
    </div>
  `;
  new bootstrap.Modal(document.getElementById('triggerDetailsModal')).show();
}
</script>
@endsection
