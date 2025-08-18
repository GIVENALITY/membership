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
                                        </div>

                                        <div class="mb-3">
                                            <label for="import_file" class="form-label">{{ __('app.select_file') }}</label>
                                            <input type="file" class="form-control" id="import_file" name="import_file" 
                                                   accept=".xlsx,.xls,.csv" required>
                                            <div class="form-text">
                                                {{ __('app.supported_formats') }}: XLSX, XLS, CSV ({{ __('app.max_size') }}: 10MB)
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
});
</script>
@endpush
@endsection
