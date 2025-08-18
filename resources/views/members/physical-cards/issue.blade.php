@extends('layouts.app')

@section('title', __('app.issue_physical_card') . ' - ' . $member->full_name)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">
                        <i class="icon-base ri ri-credit-card-2-line me-2"></i>
                        {{ __('app.issue_physical_card') }} - {{ $member->full_name }}
                    </h4>
                    <a href="{{ route('members.physical-cards.index') }}" class="btn btn-secondary">
                        <i class="icon-base ri ri-arrow-left-line me-2"></i>
                        {{ __('app.back_to_physical_cards') }}
                    </a>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <i class="icon-base ri ri-error-warning-line me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Member Details -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>{{ __('app.member_details') }}</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p><strong>{{ __('app.name') }}:</strong> {{ $member->full_name }}</p>
                                    <p><strong>{{ __('app.membership_id') }}:</strong> {{ $member->membership_id }}</p>
                                    <p><strong>{{ __('app.email') }}:</strong> {{ $member->email }}</p>
                                    <p><strong>{{ __('app.phone') }}:</strong> {{ $member->phone }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>{{ __('app.membership_info') }}</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p><strong>{{ __('app.membership_type') }}:</strong> 
                                        {{ $member->membershipType ? $member->membershipType->name : __('app.no_type') }}
                                    </p>
                                    <p><strong>{{ __('app.hotel') }}:</strong> 
                                        {{ $member->hotel ? $member->hotel->name : __('app.no_hotel') }}
                                    </p>
                                    <p><strong>{{ __('app.join_date') }}:</strong> 
                                        {{ $member->join_date ? $member->join_date->format('M d, Y') : __('app.not_available') }}
                                    </p>
                                    <p><strong>{{ __('app.current_status') }}:</strong> 
                                        <span class="badge {{ $member->getPhysicalCardStatusBadgeClass() }}">
                                            {{ $member->getPhysicalCardStatusText() }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Issue Form -->
                    <form action="{{ route('members.physical-cards.issue', $member) }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">{{ __('app.issue_status') }} <span class="text-danger">*</span></label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="issued">{{ __('app.issued') }} - {{ __('app.card_issued_but_not_delivered') }}</option>
                                        <option value="delivered">{{ __('app.delivered') }} - {{ __('app.card_issued_and_delivered') }}</option>
                                    </select>
                                    <div class="form-text">{{ __('app.select_whether_card_is_just_issued_or_also_delivered') }}</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3" id="deliveredByGroup" style="display: none;">
                                    <label for="delivered_by" class="form-label">{{ __('app.delivered_by') }}</label>
                                    <input type="text" class="form-control" id="delivered_by" name="delivered_by" 
                                           placeholder="{{ __('app.who_delivered_the_card') }}">
                                    <div class="form-text">{{ __('app.optional_name_of_person_who_delivered') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">{{ __('app.notes') }}</label>
                            <textarea class="form-control" id="notes" name="notes" rows="4" 
                                      placeholder="{{ __('app.any_additional_notes_about_card_issuance') }}"></textarea>
                            <div class="form-text">{{ __('app.optional_notes_about_card_issuance_delivery_or_any_special_instructions') }}</div>
                        </div>

                        <div class="alert alert-info">
                            <i class="icon-base ri ri-information-line me-2"></i>
                            <strong>{{ __('app.important') }}:</strong> {{ __('app.physical_card_issuance_note') }}
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('members.physical-cards.index') }}" class="btn btn-secondary">
                                <i class="icon-base ri ri-arrow-left-line me-2"></i>
                                {{ __('app.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="icon-base ri ri-check-line me-2"></i>
                                {{ __('app.issue_card') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('status');
    const deliveredByGroup = document.getElementById('deliveredByGroup');
    const deliveredByInput = document.getElementById('delivered_by');
    
    if (statusSelect && deliveredByGroup) {
        statusSelect.addEventListener('change', function() {
            if (this.value === 'delivered') {
                deliveredByGroup.style.display = 'block';
                deliveredByInput.setAttribute('required', 'required');
            } else {
                deliveredByGroup.style.display = 'none';
                deliveredByInput.removeAttribute('required');
                deliveredByInput.value = '';
            }
        });
    }
});
</script>
@endpush
