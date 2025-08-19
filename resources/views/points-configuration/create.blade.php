@extends('layouts.app')

@section('title', 'Create Points Configuration')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Create Points Configuration</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('points-configuration.store') }}" method="POST">
                        @csrf
                        
                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Configuration Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Configuration Type *</label>
                                <select class="form-select @error('type') is-invalid @enderror" name="type" id="configType" required>
                                    <option value="">Select Type</option>
                                    @foreach($configurationTypes as $value => $label)
                                        <option value="{{ $value }}" {{ old('type') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="3">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Sort Order</label>
                                <input type="number" class="form-control" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label">Active</label>
                                </div>
                            </div>
                        </div>

                        <!-- Dining Visit Configuration -->
                        <div id="diningVisitConfig" class="config-section" style="display: none;">
                            <h5 class="mb-3">Dining Visit Rules</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Points per Person</label>
                                    <input type="number" class="form-control" name="points_per_person" 
                                           value="{{ old('points_per_person', 1) }}" min="0">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Points per Amount Spent (TZS)</label>
                                    <input type="number" class="form-control" name="points_per_amount" 
                                           value="{{ old('points_per_amount', 0) }}" min="0">
                                    <small class="text-muted">e.g., 1000 = 1 point per 1000 TZS spent</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Points per Person Spending (TZS)</label>
                                    <input type="number" class="form-control" name="points_per_person_spending" 
                                           value="{{ old('points_per_person_spending', 0) }}" min="0">
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label class="form-label">Minimum Spending per Person (TZS)</label>
                                    <input type="number" class="form-control" name="min_spending_per_person" 
                                           value="{{ old('min_spending_per_person', 0) }}" min="0">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Maximum People for Points</label>
                                    <input type="number" class="form-control" name="max_people" 
                                           value="{{ old('max_people', 0) }}" min="0">
                                    <small class="text-muted">0 = no limit</small>
                                </div>
                            </div>
                        </div>

                        <!-- Special Event Configuration -->
                        <div id="specialEventConfig" class="config-section" style="display: none;">
                            <h5 class="mb-3">Special Event Rules</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Base Points</label>
                                    <input type="number" class="form-control" name="base_points" 
                                           value="{{ old('base_points', 0) }}" min="0">
                                </div>
                            </div>
                        </div>

                        <!-- Referral Configuration -->
                        <div id="referralConfig" class="config-section" style="display: none;">
                            <h5 class="mb-3">Referral Rules</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Base Points</label>
                                    <input type="number" class="form-control" name="base_points" 
                                           value="{{ old('base_points', 0) }}" min="0">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Points for New Member Referral</label>
                                    <input type="number" class="form-control" name="points_new_member" 
                                           value="{{ old('points_new_member', 0) }}" min="0">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Points for Returning Member Referral</label>
                                    <input type="number" class="form-control" name="points_returning_member" 
                                           value="{{ old('points_returning_member', 0) }}" min="0">
                                </div>
                            </div>
                        </div>

                        <!-- Social Media Configuration -->
                        <div id="socialMediaConfig" class="config-section" style="display: none;">
                            <h5 class="mb-3">Social Media Rules</h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Base Points</label>
                                    <input type="number" class="form-control" name="base_points" 
                                           value="{{ old('base_points', 0) }}" min="0">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Facebook Points</label>
                                    <input type="number" class="form-control" name="points_facebook" 
                                           value="{{ old('points_facebook', 0) }}" min="0">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Instagram Points</label>
                                    <input type="number" class="form-control" name="points_instagram" 
                                           value="{{ old('points_instagram', 0) }}" min="0">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Twitter Points</label>
                                    <input type="number" class="form-control" name="points_twitter" 
                                           value="{{ old('points_twitter', 0) }}" min="0">
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-3">
                                    <label class="form-label">General Social Media Points</label>
                                    <input type="number" class="form-control" name="points_general" 
                                           value="{{ old('points_general', 0) }}" min="0">
                                </div>
                            </div>
                        </div>

                        <!-- Birthday Bonus Configuration -->
                        <div id="birthdayBonusConfig" class="config-section" style="display: none;">
                            <h5 class="mb-3">Birthday Bonus Rules</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Bonus Points</label>
                                    <input type="number" class="form-control" name="bonus_points" 
                                           value="{{ old('bonus_points', 0) }}" min="0">
                                </div>
                            </div>
                        </div>

                        <!-- Holiday Bonus Configuration -->
                        <div id="holidayBonusConfig" class="config-section" style="display: none;">
                            <h5 class="mb-3">Holiday Bonus Rules</h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Base Points</label>
                                    <input type="number" class="form-control" name="base_points" 
                                           value="{{ old('base_points', 0) }}" min="0">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Christmas Points</label>
                                    <input type="number" class="form-control" name="points_christmas" 
                                           value="{{ old('points_christmas', 0) }}" min="0">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">New Year Points</label>
                                    <input type="number" class="form-control" name="points_new_year" 
                                           value="{{ old('points_new_year', 0) }}" min="0">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Valentine's Points</label>
                                    <input type="number" class="form-control" name="points_valentine" 
                                           value="{{ old('points_valentine', 0) }}" min="0">
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-3">
                                    <label class="form-label">General Holiday Points</label>
                                    <input type="number" class="form-control" name="points_general" 
                                           value="{{ old('points_general', 0) }}" min="0">
                                </div>
                            </div>
                        </div>

                        <!-- Custom Configuration -->
                        <div id="customConfig" class="config-section" style="display: none;">
                            <h5 class="mb-3">Custom Rules</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Base Points</label>
                                    <input type="number" class="form-control" name="base_points" 
                                           value="{{ old('base_points', 0) }}" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="icon-base ri ri-save-line me-2"></i>Create Configuration
                                </button>
                                <a href="{{ route('points-configuration.index') }}" class="btn btn-secondary">
                                    <i class="icon-base ri ri-arrow-left-line me-2"></i>Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const configType = document.getElementById('configType');
    const configSections = document.querySelectorAll('.config-section');

    function showConfigSection(type) {
        // Hide all sections
        configSections.forEach(section => {
            section.style.display = 'none';
        });

        // Show the selected section
        const sectionId = type + 'Config';
        const section = document.getElementById(sectionId);
        if (section) {
            section.style.display = 'block';
        }
    }

    configType.addEventListener('change', function() {
        showConfigSection(this.value);
    });

    // Show initial section if type is pre-selected
    if (configType.value) {
        showConfigSection(configType.value);
    }
});
</script>
@endsection
