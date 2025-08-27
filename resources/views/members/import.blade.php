@extends('layouts.app')

@section('title', __('app.import_members'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="icon-base ri ri-upload-line me-2"></i>
                        {{ __('app.import_members') }}
                    </h4>
                    <p class="card-subtitle text-muted">{{ __('app.import_members_description') }}</p>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <i class="icon-base ri ri-check-line me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <i class="icon-base ri ri-error-warning-line me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('import_errors') && count(session('import_errors')) > 0)
                        <div class="alert alert-warning alert-dismissible" role="alert">
                            <h6 class="alert-heading">
                                <i class="icon-base ri ri-error-warning-line me-2"></i>
                                {{ __('app.import_errors_occurred') }}
                            </h6>
                            <ul class="mb-0">
                                @foreach(session('import_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Import Options -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="icon-base ri ri-upload-line me-2"></i>
                                        {{ __('app.upload_file') }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('members.import.process') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        
                                        <div class="mb-3">
                                            <label for="hotel_id" class="form-label">{{ __('app.select_hotel') }}</label>
                                            <select class="form-select" id="hotel_id" name="hotel_id" required>
                                                <option value="">{{ __('app.choose_hotel') }}</option>
                                                @foreach($hotels as $hotel)
                                                    <option value="{{ $hotel->id }}" {{ $hotel->name === 'Bravo Coco' ? 'selected' : '' }}>
                                                        {{ $hotel->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">{{ __('app.membership_type_assignment') }}</label>
                                            <div class="alert alert-info">
                                                <i class="icon-base ri ri-information-line me-2"></i>
                                                {{ __('app.membership_type_assignment_description') }}
                                            </div>
                                            
                                            @if($exampleMembershipTypes->isNotEmpty())
                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        <strong>{{ __('app.available_membership_types') }}:</strong>
                                                        {{ $exampleMembershipTypes->pluck('name')->implode(', ') }}
                                                    </small>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="mb-3">
                                            <label for="import_file" class="form-label">{{ __('app.select_file') }}</label>
                                            <input type="file" class="form-control" id="import_file" name="import_file" 
                                                   accept=".xlsx,.xls,.csv" required>
                                            <div class="form-text">
                                                {{ __('app.supported_formats') }}: XLSX, XLS, CSV ({{ __('app.max_size') }}: 10MB)
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="duplicate_handling" class="form-label">{{ __('app.duplicate_handling') }}</label>
                                            <select class="form-select" id="duplicate_handling" name="duplicate_handling">
                                                <option value="error">{{ __('app.duplicate_handling_error') }}</option>
                                                <option value="skip">{{ __('app.duplicate_handling_skip') }}</option>
                                                <option value="update">{{ __('app.duplicate_handling_update') }}</option>
                                            </select>
                                            <div class="form-text">
                                                {{ __('app.duplicate_handling_description') }}
                                            </div>
                                        </div>

                                        <!-- Field Selection for Updates -->
                                        <div class="mb-3" id="update-fields-section" style="display: none;">
                                            <label class="form-label">{{ __('app.update_fields') }}</label>
                                            <div class="alert alert-info">
                                                <i class="icon-base ri ri-information-line me-2"></i>
                                                {{ __('app.select_fields_to_update') }}
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="update_fields[]" value="first_name" id="update_first_name">
                                                        <label class="form-check-label" for="update_first_name">First Name</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="update_fields[]" value="last_name" id="update_last_name">
                                                        <label class="form-check-label" for="update_last_name">Last Name</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="update_fields[]" value="email" id="update_email">
                                                        <label class="form-check-label" for="update_email">Email</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="update_fields[]" value="phone" id="update_phone">
                                                        <label class="form-check-label" for="update_phone">Phone</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="update_fields[]" value="address" id="update_address">
                                                        <label class="form-check-label" for="update_address">Address</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="update_fields[]" value="birth_date" id="update_birth_date">
                                                        <label class="form-check-label" for="update_birth_date">Birth Date</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="update_fields[]" value="join_date" id="update_join_date">
                                                        <label class="form-check-label" for="update_join_date">Join Date</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="update_fields[]" value="membership_type_name" id="update_membership_type">
                                                        <label class="form-check-label" for="update_membership_type">Membership Type</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="update_fields[]" value="allergies" id="update_allergies">
                                                        <label class="form-check-label" for="update_allergies">Allergies</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="update_fields[]" value="dietary_preferences" id="update_dietary">
                                                        <label class="form-check-label" for="update_dietary">Dietary Preferences</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="update_fields[]" value="special_requests" id="update_special_requests">
                                                        <label class="form-check-label" for="update_special_requests">Special Requests</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="update_fields[]" value="additional_notes" id="update_notes">
                                                        <label class="form-check-label" for="update_notes">Additional Notes</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="update_fields[]" value="emergency_contact_name" id="update_emergency_name">
                                                        <label class="form-check-label" for="update_emergency_name">Emergency Contact Name</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="update_fields[]" value="emergency_contact_phone" id="update_emergency_phone">
                                                        <label class="form-check-label" for="update_emergency_phone">Emergency Contact Phone</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="update_fields[]" value="emergency_contact_relationship" id="update_emergency_relationship">
                                                        <label class="form-check-label" for="update_emergency_relationship">Emergency Contact Relationship</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllFields()">Select All</button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAllFields()">Deselect All</button>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-primary">
                                            <i class="icon-base ri ri-upload-line me-2"></i>
                                            {{ __('app.import_members') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="icon-base ri ri-file-list-line me-2"></i>
                                        {{ __('app.import_from_storage') }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">{{ __('app.import_from_storage_description') }}</p>
                                    
                                    <form action="{{ route('members.import.storage') }}" method="POST">
                                        @csrf
                                        
                                        <div class="mb-3">
                                            <label for="storage_hotel_id" class="form-label">{{ __('app.select_hotel') }}</label>
                                            <select class="form-select" id="storage_hotel_id" name="hotel_id" required>
                                                <option value="">{{ __('app.choose_hotel') }}</option>
                                                @foreach($hotels as $hotel)
                                                    <option value="{{ $hotel->id }}" {{ $hotel->name === 'Bravo Coco' ? 'selected' : '' }}>
                                                        {{ $hotel->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">{{ __('app.membership_type_assignment') }}</label>
                                            <div class="alert alert-info">
                                                <i class="icon-base ri ri-information-line me-2"></i>
                                                {{ __('app.membership_type_assignment_description') }}
                                            </div>
                                            
                                            @if($exampleMembershipTypes->isNotEmpty())
                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        <strong>{{ __('app.available_membership_types') }}:</strong>
                                                        {{ $exampleMembershipTypes->pluck('name')->implode(', ') }}
                                                    </small>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="mb-3">
                                            <label for="storage_duplicate_handling" class="form-label">{{ __('app.duplicate_handling') }}</label>
                                            <select class="form-select" id="storage_duplicate_handling" name="duplicate_handling">
                                                <option value="error">{{ __('app.duplicate_handling_error') }}</option>
                                                <option value="skip">{{ __('app.duplicate_handling_skip') }}</option>
                                                <option value="update">{{ __('app.duplicate_handling_update') }}</option>
                                            </select>
                                            <div class="form-text">
                                                {{ __('app.duplicate_handling_description') }}
                                            </div>
                                        </div>

                                        <!-- Field Selection for Updates (Storage) -->
                                        <div class="mb-3" id="storage-update-fields-section" style="display: none;">
                                            <label class="form-label">{{ __('app.update_fields') }}</label>
                                            <div class="alert alert-info">
                                                <i class="icon-base ri ri-information-line me-2"></i>
                                                {{ __('app.select_fields_to_update') }}
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="update_fields[]" value="first_name" id="storage_update_first_name">
                                                        <label class="form-check-label" for="storage_update_first_name">First Name</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="update_fields[]" value="last_name" id="storage_update_last_name">
                                                        <label class="form-check-label" for="storage_update_last_name">Last Name</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="update_fields[]" value="email" id="storage_update_email">
                                                        <label class="form-check-label" for="storage_update_email">Email</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="update_fields[]" value="phone" id="storage_update_phone">
                                                        <label class="form-check-label" for="storage_update_phone">Phone</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="update_fields[]" value="address" id="storage_update_address">
                                                        <label class="form-check-label" for="storage_update_address">Address</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="update_fields[]" value="birth_date" id="storage_update_birth_date">
                                                        <label class="form-check-label" for="storage_update_birth_date">Birth Date</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="update_fields[]" value="join_date" id="storage_update_join_date">
                                                        <label class="form-check-label" for="storage_update_join_date">Join Date</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="update_fields[]" value="membership_type_name" id="storage_update_membership_type">
                                                        <label class="form-check-label" for="storage_update_membership_type">Membership Type</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="update_fields[]" value="allergies" id="storage_update_allergies">
                                                        <label class="form-check-label" for="storage_update_allergies">Allergies</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="update_fields[]" value="dietary_preferences" id="storage_update_dietary">
                                                        <label class="form-check-label" for="storage_update_dietary">Dietary Preferences</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="update_fields[]" value="special_requests" id="storage_update_special_requests">
                                                        <label class="form-check-label" for="storage_update_special_requests">Special Requests</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="update_fields[]" value="additional_notes" id="storage_update_notes">
                                                        <label class="form-check-label" for="storage_update_notes">Additional Notes</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="update_fields[]" value="emergency_contact_name" id="storage_update_emergency_name">
                                                        <label class="form-check-label" for="storage_update_emergency_name">Emergency Contact Name</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="update_fields[]" value="emergency_contact_phone" id="storage_update_emergency_phone">
                                                        <label class="form-check-label" for="storage_update_emergency_phone">Emergency Contact Phone</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="update_fields[]" value="emergency_contact_relationship" id="storage_update_emergency_relationship">
                                                        <label class="form-check-label" for="storage_update_emergency_relationship">Emergency Contact Relationship</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllStorageFields()">Select All</button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAllStorageFields()">Deselect All</button>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-success">
                                            <i class="icon-base ri ri-file-list-line me-2"></i>
                                            {{ __('app.import_from_storage') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- File Format Instructions -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="icon-base ri ri-information-line me-2"></i>
                                        {{ __('app.file_format_instructions') }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>{{ __('app.required_columns') }}:</h6>
                                            <ul>
                                                <li><strong>{{ __('app.first_name') }}</strong> - {{ __('app.member_first_name') }}</li>
                                                <li><strong>{{ __('app.last_name') }}</strong> - {{ __('app.member_last_name') }}</li>
                                                <li><strong>{{ __('app.email') }}</strong> - {{ __('app.member_email') }}</li>
                                                <li><strong>{{ __('app.phone') }}</strong> - {{ __('app.member_phone') }}</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>{{ __('app.optional_columns') }}:</h6>
                                            <ul>
                                                <li><strong>{{ __('app.address') }}</strong> - {{ __('app.member_address') }}</li>
                                                <li><strong>{{ __('app.birth_date') }}</strong> - {{ __('app.member_birth_date') }}</li>
                                                <li><strong>{{ __('app.join_date') }}</strong> - {{ __('app.member_join_date') }}</li>
                                                <li><strong>{{ __('app.membership_id') }}</strong> - {{ __('app.member_membership_id') }}</li>
                                                <li><strong>{{ __('app.membership_type_name') }}</strong> - {{ __('app.member_membership_type_name') }}</li>
                                                <li><strong>{{ __('app.membership_type_id') }}</strong> - {{ __('app.member_membership_type_id') }}</li>
                                                <li><strong>{{ __('app.allergies') }}</strong> - {{ __('app.member_allergies') }}</li>
                                                <li><strong>{{ __('app.dietary_preferences') }}</strong> - {{ __('app.member_dietary_preferences') }}</li>
                                            </ul>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-info mt-3">
                                        <h6 class="alert-heading">
                                            <i class="icon-base ri ri-lightbulb-line me-2"></i>
                                            {{ __('app.import_tips') }}
                                        </h6>
                                        <ul class="mb-0">
                                            <li>{{ __('app.import_tip_1') }}</li>
                                            <li>{{ __('app.import_tip_2') }}</li>
                                            <li>{{ __('app.import_tip_3') }}</li>
                                            <li>{{ __('app.import_tip_4') }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sample Template -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="icon-base ri ri-download-line me-2"></i>
                                        {{ __('app.sample_template') }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">{{ __('app.sample_template_description') }}</p>
                                    <a href="{{ route('members.import.template') }}" class="btn btn-outline-primary">
                                        <i class="icon-base ri ri-download-line me-2"></i>
                                        {{ __('app.download_template') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle hotel selection for file upload
    const hotelSelect = document.getElementById('hotel_id');
    const membershipTypeSelect = document.getElementById('membership_type_id');
    
    hotelSelect.addEventListener('change', function() {
        loadMembershipTypes(this.value, membershipTypeSelect);
    });
    
    // Handle hotel selection for storage import
    const storageHotelSelect = document.getElementById('storage_hotel_id');
    const storageMembershipTypeSelect = document.getElementById('storage_membership_type_id');
    
    storageHotelSelect.addEventListener('change', function() {
        loadMembershipTypes(this.value, storageMembershipTypeSelect);
    });
    
    // Handle duplicate handling selection for file upload
    const duplicateHandlingSelect = document.getElementById('duplicate_handling');
    const updateFieldsSection = document.getElementById('update-fields-section');
    
    duplicateHandlingSelect.addEventListener('change', function() {
        if (this.value === 'update') {
            updateFieldsSection.style.display = 'block';
        } else {
            updateFieldsSection.style.display = 'none';
        }
    });
    
    // Handle duplicate handling selection for storage import
    const storageDuplicateHandlingSelect = document.getElementById('storage_duplicate_handling');
    const storageUpdateFieldsSection = document.getElementById('storage-update-fields-section');
    
    storageDuplicateHandlingSelect.addEventListener('change', function() {
        if (this.value === 'update') {
            storageUpdateFieldsSection.style.display = 'block';
        } else {
            storageUpdateFieldsSection.style.display = 'none';
        }
    });
    
    // Load membership types for selected hotel
    function loadMembershipTypes(hotelId, targetSelect) {
        if (!hotelId) {
            targetSelect.innerHTML = '<option value="">{{ __('app.choose_membership_type') }}</option>';
            return;
        }
        
        fetch(`/members/import/membership-types?hotel_id=${hotelId}`)
            .then(response => response.json())
            .then(data => {
                targetSelect.innerHTML = '<option value="">{{ __('app.choose_membership_type') }}</option>';
                data.forEach(type => {
                    const option = document.createElement('option');
                    option.value = type.id;
                    option.textContent = type.name;
                    targetSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading membership types:', error);
            });
    }
    
    // Load membership types for initially selected hotels
    if (hotelSelect.value) {
        loadMembershipTypes(hotelSelect.value, membershipTypeSelect);
    }
    if (storageHotelSelect.value) {
        loadMembershipTypes(storageHotelSelect.value, storageMembershipTypeSelect);
    }
    
    // Initialize field selection visibility
    if (duplicateHandlingSelect.value === 'update') {
        updateFieldsSection.style.display = 'block';
    }
    if (storageDuplicateHandlingSelect.value === 'update') {
        storageUpdateFieldsSection.style.display = 'block';
    }
});

// Field selection functions for file upload
function selectAllFields() {
    const checkboxes = document.querySelectorAll('#update-fields-section input[type="checkbox"]');
    checkboxes.forEach(checkbox => checkbox.checked = true);
}

function deselectAllFields() {
    const checkboxes = document.querySelectorAll('#update-fields-section input[type="checkbox"]');
    checkboxes.forEach(checkbox => checkbox.checked = false);
}

// Field selection functions for storage import
function selectAllStorageFields() {
    const checkboxes = document.querySelectorAll('#storage-update-fields-section input[type="checkbox"]');
    checkboxes.forEach(checkbox => checkbox.checked = true);
}

function deselectAllStorageFields() {
    const checkboxes = document.querySelectorAll('#storage-update-fields-section input[type="checkbox"]');
    checkboxes.forEach(checkbox => checkbox.checked = false);
}
</script>
@endpush
@endsection
