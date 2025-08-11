@extends('layouts.app')

@section('title', 'Create Membership Type')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Create New Membership Type</h4>
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

        @if (session('error'))
          <div class="alert alert-danger">
            {{ session('error') }}
          </div>
        @endif

        <form method="POST" action="{{ route('membership-types.store') }}">
          @csrf
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="name" class="form-label">Name</label>
              <input type="text" class="form-control @error('name') is-invalid @enderror" 
                     id="name" name="name" placeholder="e.g., Basic, Premium, VIP" 
                     value="{{ old('name') }}" required>
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label for="billing_cycle" class="form-label">Billing Cycle</label>
              <select class="form-select @error('billing_cycle') is-invalid @enderror" 
                      id="billing_cycle" name="billing_cycle" required>
                <option value="">Select billing cycle</option>
                <option value="monthly" {{ old('billing_cycle') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                <option value="yearly" {{ old('billing_cycle') == 'yearly' ? 'selected' : '' }}>Yearly</option>
              </select>
              @error('billing_cycle')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="price" class="form-label">Price (TZS)</label>
              <input type="number" class="form-control @error('price') is-invalid @enderror" 
                     id="price" name="price" step="100" placeholder="0" 
                     value="{{ old('price') }}" required>
              @error('price')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label for="discount_rate" class="form-label">Base Discount Rate (%)</label>
              <input type="number" class="form-control @error('discount_rate') is-invalid @enderror" 
                     id="discount_rate" name="discount_rate" step="0.1" min="0" max="100" 
                     placeholder="5.0" value="{{ old('discount_rate', 5.0) }}" required>
              @error('discount_rate')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror" 
                      id="description" name="description" rows="3" 
                      placeholder="Describe this membership type...">{{ old('description') }}</textarea>
            @error('description')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="max_visits_per_month" class="form-label">Max Visits Per Month</label>
              <input type="number" class="form-control @error('max_visits_per_month') is-invalid @enderror" 
                     id="max_visits_per_month" name="max_visits_per_month" min="1" 
                     placeholder="Leave empty for unlimited" value="{{ old('max_visits_per_month') }}">
              <small class="text-muted">Leave empty for unlimited visits</small>
              @error('max_visits_per_month')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label for="sort_order" class="form-label">Sort Order</label>
              <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                     id="sort_order" name="sort_order" min="0" placeholder="0" 
                     value="{{ old('sort_order', 0) }}">
              <small class="text-muted">Lower numbers appear first</small>
              @error('sort_order')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <!-- Discount Progression Section -->
          <div class="card mb-4">
            <div class="card-header">
              <h6 class="mb-0">
                <i class="icon-base ri ri-trending-up-line me-2"></i>
                Discount Progression Rules
              </h6>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="points_required_for_discount" class="form-label">Points Required for Discount</label>
                  <input type="number" class="form-control @error('points_required_for_discount') is-invalid @enderror" 
                         id="points_required_for_discount" name="points_required_for_discount" min="1" 
                         placeholder="5" value="{{ old('points_required_for_discount', 5) }}">
                  <small class="text-muted">Minimum points needed to qualify for enhanced discounts</small>
                  @error('points_required_for_discount')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-6 mb-3">
                  <label for="consecutive_visits_for_bonus" class="form-label">Consecutive Visits for Bonus</label>
                  <input type="number" class="form-control @error('consecutive_visits_for_bonus') is-invalid @enderror" 
                         id="consecutive_visits_for_bonus" name="consecutive_visits_for_bonus" min="1" 
                         placeholder="5" value="{{ old('consecutive_visits_for_bonus', 5) }}">
                  <small class="text-muted">Number of consecutive visits needed for bonus discount</small>
                  @error('consecutive_visits_for_bonus')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="consecutive_visit_bonus_rate" class="form-label">Consecutive Visit Bonus Rate (%)</label>
                  <input type="number" class="form-control @error('consecutive_visit_bonus_rate') is-invalid @enderror" 
                         id="consecutive_visit_bonus_rate" name="consecutive_visit_bonus_rate" step="0.1" min="0" max="100" 
                         placeholder="20.0" value="{{ old('consecutive_visit_bonus_rate', 20.0) }}">
                  <small class="text-muted">Special discount rate for consecutive visits</small>
                  @error('consecutive_visit_bonus_rate')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-6 mb-3">
                  <label for="birthday_discount_rate" class="form-label">Birthday Discount Rate (%)</label>
                  <input type="number" class="form-control @error('birthday_discount_rate') is-invalid @enderror" 
                         id="birthday_discount_rate" name="birthday_discount_rate" step="0.1" min="0" max="100" 
                         placeholder="25.0" value="{{ old('birthday_discount_rate', 25.0) }}">
                  <small class="text-muted">Special discount rate for birthday visits</small>
                  @error('birthday_discount_rate')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <div class="form-check">
                    <input type="hidden" name="has_special_birthday_discount" value="0">
                    <input class="form-check-input" type="checkbox" id="has_special_birthday_discount" 
                           name="has_special_birthday_discount" value="1"
                           {{ old('has_special_birthday_discount', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="has_special_birthday_discount">
                      Enable Birthday Discount
                    </label>
                  </div>
                </div>
                <div class="col-md-6 mb-3">
                  <div class="form-check">
                    <input type="hidden" name="has_consecutive_visit_bonus" value="0">
                    <input class="form-check-input" type="checkbox" id="has_consecutive_visit_bonus" 
                           name="has_consecutive_visit_bonus" value="1"
                           {{ old('has_consecutive_visit_bonus', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="has_consecutive_visit_bonus">
                      Enable Consecutive Visit Bonus
                    </label>
                  </div>
                </div>
              </div>

              <!-- Points Reset Configuration -->
              <div class="card mb-4">
                <div class="card-header">
                  <h6 class="mb-0">
                    <i class="icon-base ri ri-refresh-line me-2"></i>
                    Points Reset Configuration
                  </h6>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <div class="form-check">
                        <input type="hidden" name="points_reset_after_redemption" value="0">
                        <input class="form-check-input" type="checkbox" id="points_reset_after_redemption" 
                               name="points_reset_after_redemption" value="1"
                               {{ old('points_reset_after_redemption', false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="points_reset_after_redemption">
                          Enable Points Reset After Redemption
                        </label>
                        <small class="text-muted d-block">When enabled, points will reset to 0 after being used for discounts</small>
                      </div>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="points_reset_threshold" class="form-label">Points Reset Threshold</label>
                      <input type="number" class="form-control @error('points_reset_threshold') is-invalid @enderror" 
                             id="points_reset_threshold" name="points_reset_threshold" min="1" 
                             placeholder="Leave empty for immediate reset" value="{{ old('points_reset_threshold') }}">
                      <small class="text-muted">Points will reset when reaching this threshold (optional)</small>
                      @error('points_reset_threshold')
                        <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  
                  <div class="mb-3">
                    <label for="points_reset_notes" class="form-label">Points Reset Notes</label>
                    <textarea class="form-control @error('points_reset_notes') is-invalid @enderror" 
                              id="points_reset_notes" name="points_reset_notes" rows="2" 
                              placeholder="Optional notes about points reset policy...">{{ old('points_reset_notes') }}</textarea>
                    <small class="text-muted">Additional information about the points reset policy</small>
                    @error('points_reset_notes')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>

              <!-- Discount Progression Table -->
              <div class="mb-3">
                <label class="form-label">Discount Progression by Visits</label>
                <div id="progression-container">
                  <div class="progression-item mb-2">
                    <div class="row">
                      <div class="col-md-6">
                        <input type="number" class="form-control" name="progression_visits[]" 
                               placeholder="Number of visits" min="1" required>
                      </div>
                      <div class="col-md-5">
                        <input type="number" class="form-control" name="progression_discounts[]" 
                               placeholder="Discount %" step="0.1" min="0" max="100" required>
                      </div>
                      <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger remove-progression" style="display: none;">
                          <i class="icon-base ri ri-delete-bin-line"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm" id="add-progression">
                  <i class="icon-base ri ri-add-line me-1"></i>
                  Add Progression Level
                </button>
                <small class="text-muted d-block mt-1">Define how discount increases with visit count</small>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Perks/Benefits</label>
            <div id="perks-container">
              <div class="perk-item mb-2">
                <div class="input-group">
                  <input type="text" class="form-control" name="perks[]" 
                         placeholder="e.g., 10% discount on all meals" required>
                  <button type="button" class="btn btn-outline-danger remove-perk" style="display: none;">
                    <i class="icon-base ri ri-delete-bin-line"></i>
                  </button>
                </div>
              </div>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" id="add-perk">
              <i class="icon-base ri ri-add-line me-1"></i>
              Add Perk
            </button>
            @error('perks')
              <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <div class="form-check">
              <input type="hidden" name="is_active" value="0">
              <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                     {{ old('is_active', true) ? 'checked' : '' }}>
              <label class="form-check-label" for="is_active">
                Active (available for new members)
              </label>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('membership-types.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
              <i class="icon-base ri ri-add-line me-2"></i>
              Create Membership Type
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const perksContainer = document.getElementById('perks-container');
  const addPerkBtn = document.getElementById('add-perk');
  const perkItems = perksContainer.querySelectorAll('.perk-item');

  // Progression table functionality
  const progressionContainer = document.getElementById('progression-container');
  const addProgressionBtn = document.getElementById('add-progression');

  // Show/hide remove buttons for progression
  function updateProgressionRemoveButtons() {
    const items = progressionContainer.querySelectorAll('.progression-item');
    items.forEach((item, index) => {
      const removeBtn = item.querySelector('.remove-progression');
      removeBtn.style.display = items.length > 1 ? 'block' : 'none';
    });
  }

  // Add new progression level
  addProgressionBtn.addEventListener('click', function() {
    const newItem = document.createElement('div');
    newItem.className = 'progression-item mb-2';
    newItem.innerHTML = `
      <div class="row">
        <div class="col-md-6">
          <input type="number" class="form-control" name="progression_visits[]" 
                 placeholder="Number of visits" min="1" required>
        </div>
        <div class="col-md-5">
          <input type="number" class="form-control" name="progression_discounts[]" 
                 placeholder="Discount %" step="0.1" min="0" max="100" required>
        </div>
        <div class="col-md-1">
          <button type="button" class="btn btn-outline-danger remove-progression">
            <i class="icon-base ri ri-delete-bin-line"></i>
          </button>
        </div>
      </div>
    `;
    progressionContainer.appendChild(newItem);
    updateProgressionRemoveButtons();
  });

  // Remove progression level
  progressionContainer.addEventListener('click', function(e) {
    if (e.target.closest('.remove-progression')) {
      e.target.closest('.progression-item').remove();
      updateProgressionRemoveButtons();
    }
  });

  // Initialize progression remove buttons
  updateProgressionRemoveButtons();

  // Show/hide remove buttons based on number of perks
  function updateRemoveButtons() {
    const items = perksContainer.querySelectorAll('.perk-item');
    items.forEach((item, index) => {
      const removeBtn = item.querySelector('.remove-perk');
      removeBtn.style.display = items.length > 1 ? 'block' : 'none';
    });
  }

  // Add new perk
  addPerkBtn.addEventListener('click', function() {
    const newPerk = document.createElement('div');
    newPerk.className = 'perk-item mb-2';
    newPerk.innerHTML = `
      <div class="input-group">
        <input type="text" class="form-control" name="perks[]" 
               placeholder="e.g., Priority seating" required>
        <button type="button" class="btn btn-outline-danger remove-perk">
          <i class="icon-base ri ri-delete-bin-line"></i>
        </button>
      </div>
    `;
    perksContainer.appendChild(newPerk);
    updateRemoveButtons();
  });

  // Remove perk
  perksContainer.addEventListener('click', function(e) {
    if (e.target.closest('.remove-perk')) {
      e.target.closest('.perk-item').remove();
      updateRemoveButtons();
    }
  });

  // Initialize remove buttons
  updateRemoveButtons();
});
</script>
@endsection 