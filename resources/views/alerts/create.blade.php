@extends('layouts.app')

@section('title', 'Create Alert')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Create New Alert</h4>
        <p class="card-subtitle text-muted">Set up automated alerts to monitor member activities</p>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('alerts.store') }}">
          @csrf
          
          <!-- Basic Information -->
          <div class="row mb-4">
            <div class="col-12">
              <h5 class="text-muted mb-3 border-bottom pb-2">
                <i class="icon-base ri ri-information-line me-2"></i>Basic Information
              </h5>
            </div>
            <div class="col-md-6 mb-3">
              <label for="name" class="form-label">Alert Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('name') is-invalid @enderror" 
                     id="name" name="name" value="{{ old('name') }}" required>
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label for="type" class="form-label">Alert Type <span class="text-danger">*</span></label>
              <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                <option value="">Select Alert Type</option>
                @foreach($alertTypes as $value => $label)
                  <option value="{{ $value }}" {{ old('type') == $value ? 'selected' : '' }}>
                    {{ $label }}
                  </option>
                @endforeach
              </select>
              @error('type')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-12 mb-3">
              <label for="description" class="form-label">Description</label>
              <textarea class="form-control @error('description') is-invalid @enderror" 
                        id="description" name="description" rows="3" 
                        placeholder="Describe what this alert monitors...">{{ old('description') }}</textarea>
              @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <!-- Alert Conditions -->
          <div class="row mb-4">
            <div class="col-12">
              <h5 class="text-muted mb-3 border-bottom pb-2">
                <i class="icon-base ri ri-settings-line me-2"></i>Alert Conditions
              </h5>
            </div>
            
            <!-- Spending Threshold Conditions -->
            <div class="col-12 mb-3" id="spending-conditions" style="display: none;">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="spending_amount" class="form-label">Amount Threshold (TZS)</label>
                  <input type="number" class="form-control" id="spending_amount" name="spending_amount" 
                         value="{{ old('spending_amount', 100000) }}" min="0" step="1000">
                </div>
                <div class="col-md-6 mb-3">
                  <label for="spending_period" class="form-label">Time Period</label>
                  <select class="form-select" id="spending_period" name="spending_period">
                    <option value="day" {{ old('spending_period') == 'day' ? 'selected' : '' }}>Per Day</option>
                    <option value="week" {{ old('spending_period') == 'week' ? 'selected' : '' }}>Per Week</option>
                    <option value="month" {{ old('spending_period') == 'month' ? 'selected' : 'selected' }}>Per Month</option>
                    <option value="year" {{ old('spending_period') == 'year' ? 'selected' : '' }}>Per Year</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Visit Frequency Conditions -->
            <div class="col-12 mb-3" id="visit-conditions" style="display: none;">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="visit_days" class="form-label">Days Without Visit</label>
                  <input type="number" class="form-control" id="visit_days" name="visit_days" 
                         value="{{ old('visit_days', 30) }}" min="1" max="365">
                  <small class="text-muted">Alert when member hasn't visited for this many days</small>
                </div>
              </div>
            </div>

            <!-- Points Threshold Conditions -->
            <div class="col-12 mb-3" id="points-conditions" style="display: none;">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="points_amount" class="form-label">Points Threshold</label>
                  <input type="number" class="form-control" id="points_amount" name="points_amount" 
                         value="{{ old('points_amount', 100) }}" min="0">
                </div>
                <div class="col-md-6 mb-3">
                  <label for="points_operator" class="form-label">Operator</label>
                  <select class="form-select" id="points_operator" name="points_operator">
                    <option value="gte" {{ old('points_operator') == 'gte' ? 'selected' : 'selected' }}>Greater than or equal to</option>
                    <option value="lte" {{ old('points_operator') == 'lte' ? 'selected' : '' }}>Less than or equal to</option>
                    <option value="eq" {{ old('points_operator') == 'eq' ? 'selected' : '' }}>Equal to</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Birthday Approaching Conditions -->
            <div class="col-12 mb-3" id="birthday-conditions" style="display: none;">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="birthday_days" class="form-label">Days Before Birthday</label>
                  <input type="number" class="form-control" id="birthday_days" name="birthday_days" 
                         value="{{ old('birthday_days', 7) }}" min="1" max="30">
                  <small class="text-muted">Alert this many days before member's birthday</small>
                </div>
              </div>
            </div>

            <!-- Membership Expiry Conditions -->
            <div class="col-12 mb-3" id="expiry-conditions" style="display: none;">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="expiry_days" class="form-label">Days Before Expiry</label>
                  <input type="number" class="form-control" id="expiry_days" name="expiry_days" 
                         value="{{ old('expiry_days', 30) }}" min="1" max="90">
                  <small class="text-muted">Alert this many days before membership expires</small>
                </div>
              </div>
            </div>
          </div>

          <!-- Alert Settings -->
          <div class="row mb-4">
            <div class="col-12">
              <h5 class="text-muted mb-3 border-bottom pb-2">
                <i class="icon-base ri ri-tools-line me-2"></i>Alert Settings
              </h5>
            </div>
            <div class="col-md-6 mb-3">
              <label for="severity" class="form-label">Severity Level <span class="text-danger">*</span></label>
              <select class="form-select @error('severity') is-invalid @enderror" id="severity" name="severity" required>
                <option value="">Select Severity</option>
                @foreach($severityLevels as $value => $label)
                  <option value="{{ $value }}" {{ old('severity') == $value ? 'selected' : '' }}>
                    {{ $label }}
                  </option>
                @endforeach
              </select>
              @error('severity')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label for="color" class="form-label">Alert Color</label>
              <div class="input-group">
                <input type="color" class="form-control form-control-color" id="color" name="color" 
                       value="{{ old('color', '#ffc107') }}" title="Choose alert color">
                <input type="text" class="form-control" value="{{ old('color', '#ffc107') }}" 
                       id="color_text" placeholder="#ffc107">
              </div>
            </div>
          </div>

          <!-- Display Options -->
          <div class="row mb-4">
            <div class="col-12">
              <h5 class="text-muted mb-3 border-bottom pb-2">
                <i class="icon-base ri ri-eye-line me-2"></i>Display Options
              </h5>
            </div>
            <div class="col-md-6 mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                       value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">
                  <strong>Active</strong>
                </label>
                <small class="form-text text-muted d-block">Enable this alert</small>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="show_dashboard" name="show_dashboard" 
                       value="1" {{ old('show_dashboard', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="show_dashboard">
                  <strong>Show on Dashboard</strong>
                </label>
                <small class="form-text text-muted d-block">Display alerts on main dashboard</small>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="show_quickview" name="show_quickview" 
                       value="1" {{ old('show_quickview', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="show_quickview">
                  <strong>Show on QuickView</strong>
                </label>
                <small class="form-text text-muted d-block">Display alerts on QuickView page</small>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="send_email" name="send_email" 
                       value="1" {{ old('send_email') ? 'checked' : '' }}>
                <label class="form-check-label" for="send_email">
                  <strong>Send Email Notifications</strong>
                </label>
                <small class="form-text text-muted d-block">Send email alerts when triggered</small>
              </div>
            </div>
          </div>

          <!-- Email Template (if email is enabled) -->
          <div class="row mb-4" id="email-template-section" style="display: none;">
            <div class="col-12">
              <h5 class="text-muted mb-3 border-bottom pb-2">
                <i class="icon-base ri ri-mail-line me-2"></i>Email Template
              </h5>
            </div>
            <div class="col-12 mb-3">
              <label for="email_template" class="form-label">Email Template</label>
              <textarea class="form-control" id="email_template" name="email_template" rows="5" 
                        placeholder="Custom email template (optional). Use {member_name}, {alert_name}, {triggered_at} as placeholders.">{{ old('email_template') }}</textarea>
              <small class="text-muted">Leave empty to use default template</small>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="row">
            <div class="col-12">
              <div class="d-flex justify-content-between">
                <a href="{{ route('alerts.index') }}" class="btn btn-secondary">
                  <i class="icon-base ri ri-arrow-left-line me-1"></i>
                  Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                  <i class="icon-base ri ri-save-line me-1"></i>
                  Create Alert
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const spendingConditions = document.getElementById('spending-conditions');
    const visitConditions = document.getElementById('visit-conditions');
    const pointsConditions = document.getElementById('points-conditions');
    const birthdayConditions = document.getElementById('birthday-conditions');
    const expiryConditions = document.getElementById('expiry-conditions');
    const emailTemplateSection = document.getElementById('email-template-section');
    const sendEmailCheckbox = document.getElementById('send_email');
    const colorInput = document.getElementById('color');
    const colorText = document.getElementById('color_text');

    // Show/hide conditions based on alert type
    function toggleConditions() {
        const selectedType = typeSelect.value;
        
        // Hide all condition sections
        [spendingConditions, visitConditions, pointsConditions, birthdayConditions, expiryConditions].forEach(el => {
            el.style.display = 'none';
        });

        // Show relevant condition section
        switch(selectedType) {
            case 'spending_threshold':
                spendingConditions.style.display = 'block';
                break;
            case 'visit_frequency':
                visitConditions.style.display = 'block';
                break;
            case 'points_threshold':
                pointsConditions.style.display = 'block';
                break;
            case 'birthday_approaching':
                birthdayConditions.style.display = 'block';
                break;
            case 'membership_expiry':
                expiryConditions.style.display = 'block';
                break;
        }
    }

    // Show/hide email template section
    function toggleEmailTemplate() {
        emailTemplateSection.style.display = sendEmailCheckbox.checked ? 'block' : 'none';
    }

    // Sync color picker and text input
    function syncColorInputs() {
        colorText.value = colorInput.value;
    }

    function syncColorPicker() {
        colorInput.value = colorText.value;
    }

    // Event listeners
    typeSelect.addEventListener('change', toggleConditions);
    sendEmailCheckbox.addEventListener('change', toggleEmailTemplate);
    colorInput.addEventListener('input', syncColorInputs);
    colorText.addEventListener('input', syncColorPicker);

    // Initialize on page load
    toggleConditions();
    toggleEmailTemplate();
    syncColorInputs();
});
</script>
@endsection
