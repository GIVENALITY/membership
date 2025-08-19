@extends('layouts.app')

@section('title', 'Restaurant Settings')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">
          <i class="icon-base ri ri-settings-3-line me-2"></i>
          Restaurant Settings
        </h4>
        <p class="card-subtitle text-muted">Configure roles, modules, and features for your restaurant</p>
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

        <form method="POST" action="{{ route('restaurant.settings.update') }}">
          @csrf
          
          <!-- User Roles Configuration -->
          <div class="card mb-4">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="icon-base ri ri-user-settings-line me-2"></i>
                User Roles Configuration
              </h5>
              <small class="text-muted">Select which user roles are available for your restaurant</small>
            </div>
            <div class="card-body">
              <div class="row">
                @foreach($availableRoles as $role => $description)
                  <div class="col-md-6 mb-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" 
                             id="role_{{ $role }}" name="enabled_roles[]" 
                             value="{{ $role }}"
                             {{ in_array($role, $settings['enabled_roles'] ?? ['admin', 'manager', 'frontdesk']) ? 'checked' : '' }}>
                      <label class="form-check-label" for="role_{{ $role }}">
                        <strong>{{ ucfirst($role) }}</strong>
                        <br>
                        <small class="text-muted">{{ $description }}</small>
                      </label>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>

          <!-- Module Configuration -->
          <div class="card mb-4">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="icon-base ri ri-apps-line me-2"></i>
                Module Configuration
              </h5>
              <small class="text-muted">Enable or disable specific features and modules</small>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" 
                           id="receipt_required" name="receipt_required" 
                           {{ ($settings['receipt_required'] ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="receipt_required">
                      <strong>Require Receipt Upload</strong>
                      <br>
                      <small class="text-muted">Require receipt upload during member checkout</small>
                    </label>
                  </div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" 
                           id="physical_cards_enabled" name="physical_cards_enabled" 
                           {{ ($settings['physical_cards_enabled'] ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="physical_cards_enabled">
                      <strong>Physical Cards</strong>
                      <br>
                      <small class="text-muted">Track physical card issuance and delivery</small>
                    </label>
                  </div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" 
                           id="virtual_cards_enabled" name="virtual_cards_enabled" 
                           {{ ($settings['virtual_cards_enabled'] ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="virtual_cards_enabled">
                      <strong>Virtual Cards</strong>
                      <br>
                      <small class="text-muted">Generate and manage virtual membership cards</small>
                    </label>
                  </div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" 
                           id="points_system_enabled" name="points_system_enabled" 
                           {{ ($settings['points_system_enabled'] ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="points_system_enabled">
                      <strong>Points System</strong>
                      <br>
                      <small class="text-muted">Member points and rewards system</small>
                    </label>
                  </div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" 
                           id="dining_management_enabled" name="dining_management_enabled" 
                           {{ ($settings['dining_management_enabled'] ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="dining_management_enabled">
                      <strong>Dining Management</strong>
                      <br>
                      <small class="text-muted">Record and track dining visits</small>
                    </label>
                  </div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" 
                           id="reports_enabled" name="reports_enabled" 
                           {{ ($settings['reports_enabled'] ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="reports_enabled">
                      <strong>Reports Module</strong>
                      <br>
                      <small class="text-muted">Generate and view reports</small>
                    </label>
                  </div>
                </div>

                <div class="col-md-6 mb-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" 
                           id="user_management_enabled" name="user_management_enabled" 
                           {{ ($settings['user_management_enabled'] ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="user_management_enabled">
                      <strong>User Management</strong>
                      <br>
                      <small class="text-muted">Manage staff accounts</small>
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Current Settings Summary -->
          <div class="card mb-4">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="icon-base ri ri-information-line me-2"></i>
                Current Configuration Summary
              </h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <h6>Enabled Roles:</h6>
                  <ul class="list-unstyled">
                    @foreach($settings['enabled_roles'] ?? ['admin', 'manager', 'frontdesk'] as $role)
                      <li><span class="badge bg-label-success">{{ ucfirst($role) }}</span></li>
                    @endforeach
                  </ul>
                </div>
                <div class="col-md-6">
                  <h6>Enabled Features:</h6>
                  <ul class="list-unstyled">
                    @if($settings['receipt_required'] ?? false)
                      <li><span class="badge bg-label-info">Receipt Required</span></li>
                    @endif
                    @if($settings['physical_cards_enabled'] ?? true)
                      <li><span class="badge bg-label-primary">Physical Cards</span></li>
                    @endif
                    @if($settings['virtual_cards_enabled'] ?? true)
                      <li><span class="badge bg-label-primary">Virtual Cards</span></li>
                    @endif
                    @if($settings['points_system_enabled'] ?? true)
                      <li><span class="badge bg-label-warning">Points System</span></li>
                    @endif
                    @if($settings['dining_management_enabled'] ?? true)
                      <li><span class="badge bg-label-success">Dining Management</span></li>
                    @endif
                    @if($settings['reports_enabled'] ?? true)
                      <li><span class="badge bg-label-info">Reports</span></li>
                    @endif
                    @if($settings['user_management_enabled'] ?? true)
                      <li><span class="badge bg-label-secondary">User Management</span></li>
                    @endif
                  </ul>
                </div>
              </div>
            </div>
          </div>

          <div class="text-end">
            <button type="submit" class="btn btn-primary">
              <i class="icon-base ri ri-save-line me-2"></i>
              Save Settings
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
