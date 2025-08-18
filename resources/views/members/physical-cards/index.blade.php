@extends('layouts.app')

@section('title', __('app.physical_cards'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">
                        <i class="icon-base ri ri-credit-card-2-line me-2"></i>
                        {{ __('app.physical_cards') }}
                    </h4>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#massIssueModal">
                            <i class="icon-base ri ri-add-line me-2"></i>
                            {{ __('app.mass_issue_cards') }}
                        </button>
                    </div>
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

                    @if(session('physical_card_errors') && count(session('physical_card_errors')) > 0)
                        <div class="alert alert-warning alert-dismissible" role="alert">
                            <h6 class="alert-heading">
                                <i class="icon-base ri ri-error-warning-line me-2"></i>
                                {{ __('app.physical_card_errors') }}
                            </h6>
                            <ul class="mb-0">
                                @foreach(session('physical_card_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $totalMembers }}</h4>
                                            <small>{{ __('app.total_members') }}</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="icon-base ri ri-group-line fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-secondary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $notIssued }}</h4>
                                            <small>{{ __('app.not_issued') }}</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="icon-base ri ri-time-line fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $issued }}</h4>
                                            <small>{{ __('app.issued') }}</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="icon-base ri ri-check-line fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $delivered }}</h4>
                                            <small>{{ __('app.delivered') }}</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="icon-base ri ri-check-double-line fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $lost }}</h4>
                                            <small>{{ __('app.lost') }}</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="icon-base ri ri-error-warning-line fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $replaced }}</h4>
                                            <small>{{ __('app.replaced') }}</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="icon-base ri ri-refresh-line fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <form method="GET" action="{{ route('members.physical-cards.index') }}" class="d-flex gap-2">
                                @if(auth()->user()->role === 'super_admin')
                                    <select name="hotel_id" class="form-select" onchange="this.form.submit()">
                                        <option value="">{{ __('app.all_hotels') }}</option>
                                        @foreach($hotels as $hotel)
                                            <option value="{{ $hotel->id }}" {{ $hotelId == $hotel->id ? 'selected' : '' }}>
                                                {{ $hotel->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                                
                                <select name="status" class="form-select" onchange="this.form.submit()">
                                    <option value="">{{ __('app.all_statuses') }}</option>
                                    <option value="not_issued" {{ $statusFilter === 'not_issued' ? 'selected' : '' }}>{{ __('app.not_issued') }}</option>
                                    <option value="issued" {{ $statusFilter === 'issued' ? 'selected' : '' }}>{{ __('app.issued') }}</option>
                                    <option value="delivered" {{ $statusFilter === 'delivered' ? 'selected' : '' }}>{{ __('app.delivered') }}</option>
                                    <option value="lost" {{ $statusFilter === 'lost' ? 'selected' : '' }}>{{ __('app.lost') }}</option>
                                    <option value="replaced" {{ $statusFilter === 'replaced' ? 'selected' : '' }}>{{ __('app.replaced') }}</option>
                                </select>
                            </form>
                        </div>
                        <div class="col-md-4 text-end">
                            @if($notIssued > 0)
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#massIssueModal">
                                    <i class="icon-base ri ri-add-line me-2"></i>
                                    {{ __('app.issue_all_pending_cards') }} ({{ $notIssued }})
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Members Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('app.member') }}</th>
                                    <th>{{ __('app.membership_id') }}</th>
                                    <th>{{ __('app.membership_type') }}</th>
                                    <th>{{ __('app.hotel') }}</th>
                                    <th>{{ __('app.physical_card_status') }}</th>
                                    <th>{{ __('app.issued_date') }}</th>
                                    <th>{{ __('app.delivered_date') }}</th>
                                    <th>{{ __('app.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($members as $member)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3">
                                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                                        {{ strtoupper(substr($member->first_name, 0, 1)) }}{{ strtoupper(substr($member->last_name, 0, 1)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $member->full_name }}</h6>
                                                    <small class="text-muted">{{ $member->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-label-info">{{ $member->membership_id }}</span>
                                        </td>
                                        <td>
                                            @if($member->membershipType)
                                                <span class="badge bg-label-primary">{{ $member->membershipType->name }}</span>
                                            @else
                                                <span class="text-muted">{{ __('app.no_type') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($member->hotel)
                                                <span class="badge bg-label-secondary">{{ $member->hotel->name }}</span>
                                            @else
                                                <span class="text-muted">{{ __('app.no_hotel') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $member->getPhysicalCardStatusBadgeClass() }}">
                                                {{ $member->getPhysicalCardStatusText() }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($member->physical_card_issued_date)
                                                <small>{{ $member->physical_card_issued_date->format('M d, Y') }}</small>
                                                @if($member->physical_card_issued_by)
                                                    <br><small class="text-muted">{{ $member->physical_card_issued_by }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($member->physical_card_delivered_date)
                                                <small>{{ $member->physical_card_delivered_date->format('M d, Y') }}</small>
                                                @if($member->physical_card_delivered_by)
                                                    <br><small class="text-muted">{{ $member->physical_card_delivered_by }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    {{ __('app.actions') }}
                                                </button>
                                                <ul class="dropdown-menu">
                                                    @if($member->physical_card_status === 'not_issued')
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('members.physical-cards.issue-form', $member) }}">
                                                                <i class="icon-base ri ri-add-line me-2"></i>
                                                                {{ __('app.issue_card') }}
                                                            </a>
                                                        </li>
                                                    @else
                                                        <li>
                                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#updateStatusModal{{ $member->id }}">
                                                                <i class="icon-base ri ri-edit-line me-2"></i>
                                                                {{ __('app.update_status') }}
                                                            </a>
                                                        </li>
                                                    @endif
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('members.show', $member) }}">
                                                            <i class="icon-base ri ri-user-line me-2"></i>
                                                            {{ __('app.view_member') }}
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="icon-base ri ri-user-line fs-1 mb-3"></i>
                                                <p>{{ __('app.no_members_found') }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $members->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mass Issue Modal -->
<div class="modal fade" id="massIssueModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="icon-base ri ri-add-line me-2"></i>
                    {{ __('app.mass_issue_cards') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('members.physical-cards.mass-issue') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="icon-base ri ri-information-line me-2"></i>
                        {{ __('app.mass_issue_description') }}
                    </div>
                    
                    @if(auth()->user()->role === 'super_admin')
                        <div class="mb-3">
                            <label for="modal_hotel_id" class="form-label">{{ __('app.select_hotel') }}</label>
                            <select class="form-select" id="modal_hotel_id" name="hotel_id">
                                <option value="">{{ __('app.all_hotels') }}</option>
                                @foreach($hotels as $hotel)
                                    <option value="{{ $hotel->id }}" {{ $hotelId == $hotel->id ? 'selected' : '' }}>
                                        {{ $hotel->name }} ({{ $hotel->members()->where('physical_card_status', 'not_issued')->count() }} {{ __('app.not_issued') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="status" class="form-label">{{ __('app.issue_status') }}</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="issued">{{ __('app.issued') }}</option>
                            <option value="delivered">{{ __('app.delivered') }}</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">{{ __('app.notes') }}</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="{{ __('app.optional_notes') }}"></textarea>
                    </div>

                    <div class="mb-3" id="deliveredByGroup" style="display: none;">
                        <label for="delivered_by" class="form-label">{{ __('app.delivered_by') }}</label>
                        <input type="text" class="form-control" id="delivered_by" name="delivered_by" placeholder="{{ __('app.who_delivered') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('app.issue_summary') }}</label>
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <h4 class="text-secondary mb-0">{{ $notIssued }}</h4>
                                        <small>{{ __('app.not_issued') }}</small>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="text-success mb-0">{{ $issued + $delivered + $replaced }}</h4>
                                        <small>{{ __('app.issued_total') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('app.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary" {{ $notIssued == 0 ? 'disabled' : '' }}>
                        <i class="icon-base ri ri-add-line me-2"></i>
                        {{ __('app.issue_cards') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Status Modals -->
@foreach($members as $member)
    @if($member->physical_card_status !== 'not_issued')
        <div class="modal fade" id="updateStatusModal{{ $member->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="icon-base ri ri-edit-line me-2"></i>
                            {{ __('app.update_card_status') }} - {{ $member->full_name }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('members.physical-cards.update-status', $member) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="status{{ $member->id }}" class="form-label">{{ __('app.status') }}</label>
                                <select class="form-select" id="status{{ $member->id }}" name="status" required>
                                    <option value="not_issued" {{ $member->physical_card_status === 'not_issued' ? 'selected' : '' }}>{{ __('app.not_issued') }}</option>
                                    <option value="issued" {{ $member->physical_card_status === 'issued' ? 'selected' : '' }}>{{ __('app.issued') }}</option>
                                    <option value="delivered" {{ $member->physical_card_status === 'delivered' ? 'selected' : '' }}>{{ __('app.delivered') }}</option>
                                    <option value="lost" {{ $member->physical_card_status === 'lost' ? 'selected' : '' }}>{{ __('app.lost') }}</option>
                                    <option value="replaced" {{ $member->physical_card_status === 'replaced' ? 'selected' : '' }}>{{ __('app.replaced') }}</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="notes{{ $member->id }}" class="form-label">{{ __('app.notes') }}</label>
                                <textarea class="form-control" id="notes{{ $member->id }}" name="notes" rows="3" placeholder="{{ __('app.optional_notes') }}">{{ $member->physical_card_notes }}</textarea>
                            </div>

                            <div class="mb-3" id="deliveredByGroup{{ $member->id }}" style="display: none;">
                                <label for="delivered_by{{ $member->id }}" class="form-label">{{ __('app.delivered_by') }}</label>
                                <input type="text" class="form-control" id="delivered_by{{ $member->id }}" name="delivered_by" placeholder="{{ __('app.who_delivered') }}" value="{{ $member->physical_card_delivered_by }}">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                {{ __('app.cancel') }}
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="icon-base ri ri-save-line me-2"></i>
                                {{ __('app.update_status') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show/hide delivered_by field based on status selection
    const statusSelect = document.getElementById('status');
    const deliveredByGroup = document.getElementById('deliveredByGroup');
    
    if (statusSelect && deliveredByGroup) {
        statusSelect.addEventListener('change', function() {
            deliveredByGroup.style.display = this.value === 'delivered' ? 'block' : 'none';
        });
    }

    // Handle individual member status modals
    @foreach($members as $member)
        @if($member->physical_card_status !== 'not_issued')
            const statusSelect{{ $member->id }} = document.getElementById('status{{ $member->id }}');
            const deliveredByGroup{{ $member->id }} = document.getElementById('deliveredByGroup{{ $member->id }}');
            
            if (statusSelect{{ $member->id }} && deliveredByGroup{{ $member->id }}) {
                statusSelect{{ $member->id }}.addEventListener('change', function() {
                    deliveredByGroup{{ $member->id }}.style.display = this.value === 'delivered' ? 'block' : 'none';
                });
            }
        @endif
    @endforeach

    // Update statistics when filters change
    const hotelSelect = document.querySelector('select[name="hotel_id"]');
    const statusFilterSelect = document.querySelector('select[name="status"]');
    
    if (hotelSelect || statusFilterSelect) {
        [hotelSelect, statusFilterSelect].forEach(select => {
            if (select) {
                select.addEventListener('change', function() {
                    updateStats();
                });
            }
        });
    }

    function updateStats() {
        const hotelId = hotelSelect ? hotelSelect.value : '';
        const status = statusFilterSelect ? statusFilterSelect.value : '';
        
        fetch(`{{ route('members.physical-cards.stats') }}?hotel_id=${hotelId}&status=${status}`)
            .then(response => response.json())
            .then(data => {
                // Update statistics cards
                document.querySelector('.bg-primary h4').textContent = data.total;
                document.querySelector('.bg-secondary h4').textContent = data.not_issued;
                document.querySelector('.bg-warning h4').textContent = data.issued;
                document.querySelector('.bg-success h4').textContent = data.delivered;
                document.querySelector('.bg-danger h4').textContent = data.lost;
                document.querySelector('.bg-info h4').textContent = data.replaced;
            })
            .catch(error => console.error('Error updating stats:', error));
    }
});
</script>
@endpush
