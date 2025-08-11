@extends('layouts.app')

@section('title', $member->full_name . ' - Dining History - ' . (Auth::user()->hotel->name ?? 'Membership MS'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg me-3">
                            <span class="avatar-initial rounded-circle bg-label-primary">
                                {{ substr($member->first_name, 0, 1) }}
                            </span>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">{{ $member->full_name }}</h5>
                            <small class="text-muted">{{ $member->membership_id }} - Dining History</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('members.show', $member) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="icon-base ri ri-user-line me-1"></i>
                            Member Profile
                        </a>
                        <a href="{{ route('dining.history') }}" class="btn btn-outline-primary btn-sm">
                            <i class="icon-base ri ri-arrow-left-line me-1"></i>
                            Back to History
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Member Analytics -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-3">
                            <span class="avatar-initial rounded-circle bg-label-primary">
                                <i class="icon-base ri ri-restaurant-line"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-1">Total Visits</h6>
                            <h4 class="mb-0">{{ number_format($analytics['total_visits']) }}</h4>
                            <small class="text-muted">
                                {{ $analytics['completed_visits'] }} completed
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-3">
                            <span class="avatar-initial rounded-circle bg-label-success">
                                <i class="icon-base ri ri-money-dollar-circle-line"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-1">Total Spent</h6>
                            <h4 class="mb-0">TZS {{ number_format($analytics['total_spent']) }}</h4>
                            <small class="text-muted">
                                Avg: TZS {{ number_format($analytics['avg_bill_amount']) }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-3">
                            <span class="avatar-initial rounded-circle bg-label-warning">
                                <i class="icon-base ri ri-percent-line"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-1">Total Discounts</h6>
                            <h4 class="mb-0">TZS {{ number_format($analytics['total_discounts']) }}</h4>
                            <small class="text-muted">
                                Current: {{ $member->current_discount_rate }}%
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-3">
                            <span class="avatar-initial rounded-circle bg-label-info">
                                <i class="icon-base ri ri-time-line"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-1">Last Visit</h6>
                            @if($analytics['last_visit'])
                                <h6 class="mb-0">{{ $analytics['last_visit']->created_at->format('M j, Y') }}</h6>
                                <small class="text-muted">
                                    {{ $analytics['last_visit']->created_at->diffForHumans() }}
                                </small>
                            @else
                                <h6 class="mb-0 text-muted">No visits yet</h6>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Member Details Card -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="icon-base ri ri-user-line me-2"></i>
                        Member Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <p><strong>Name:</strong> {{ $member->full_name }}</p>
                            <p><strong>Membership ID:</strong> {{ $member->membership_id }}</p>
                            <p><strong>Phone:</strong> {{ $member->phone ?? 'Not provided' }}</p>
                            <p><strong>Email:</strong> {{ $member->email ?? 'Not provided' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p><strong>Status:</strong> 
                                @if($member->status === 'active')
                                    <span class="badge bg-label-success">Active</span>
                                @elseif($member->status === 'inactive')
                                    <span class="badge bg-label-secondary">Inactive</span>
                                @else
                                    <span class="badge bg-label-danger">Suspended</span>
                                @endif
                            </p>
                            <p><strong>Current Discount:</strong> {{ $member->current_discount_rate }}%</p>
                            <p><strong>Joined:</strong> {{ $member->join_date->format('M j, Y') }}</p>
                            <p><strong>Membership Type:</strong> {{ optional($member->membershipType)->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="icon-base ri ri-heart-line me-2"></i>
                        Preferences & Notes
                    </h6>
                </div>
                <div class="card-body">
                    @if($member->allergies || $member->dietary_preferences || $member->special_requests || $member->additional_notes)
                        @if($member->allergies)
                            <p><strong class="text-danger">⚠️ Allergies:</strong> {{ $member->allergies }}</p>
                        @endif
                        @if($member->dietary_preferences)
                            <p><strong>🍽️ Dietary:</strong> {{ $member->dietary_preferences }}</p>
                        @endif
                        @if($member->special_requests)
                            <p><strong>🎯 Special Requests:</strong> {{ $member->special_requests }}</p>
                        @endif
                        @if($member->additional_notes)
                            <p><strong>📝 Notes:</strong> {{ $member->additional_notes }}</p>
                        @endif
                    @else
                        <p class="text-muted">No special preferences or notes recorded.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Dining History Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="icon-base ri ri-history-line me-2"></i>
                        Dining History for {{ $member->first_name }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Visit Date</th>
                                    <th>People</th>
                                    <th>Amount</th>
                                    <th>Discount</th>
                                    <th>Final</th>
                                    <th>Duration</th>
                                    <th>Recorded By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($visits as $visit)
                                <tr>
                                    <td>
                                        <div>{{ $visit->created_at->format('M j, Y') }}</div>
                                        <small class="text-muted">{{ $visit->created_at->format('H:i') }}</small>
                                        @if($visit->checked_out_at)
                                            <br><small class="text-success">Checked out: {{ $visit->checked_out_at->format('H:i') }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-label-info">{{ $visit->number_of_people }} people</span>
                                    </td>
                                    <td>
                                        @if($visit->amount_spent)
                                            <strong>TZS {{ number_format($visit->amount_spent) }}</strong>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($visit->discount_amount)
                                            <span class="text-success">-TZS {{ number_format($visit->discount_amount) }}</span>
                                            <br><small class="text-muted">({{ $visit->discount_percentage }}%)</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($visit->final_amount)
                                            <strong>TZS {{ number_format($visit->final_amount) }}</strong>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($visit->duration)
                                            <span class="text-muted">{{ $visit->duration }} min</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <small>{{ $visit->recordedBy ? $visit->recordedBy->name : 'N/A' }}</small>
                                            @if($visit->checkedOutBy && $visit->checkedOutBy->id !== $visit->recordedBy->id)
                                                <br><small class="text-muted">Checked out by: {{ $visit->checkedOutBy->name }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            @if($visit->receipt_path)
                                                <a href="{{ $visit->receipt_url }}" target="_blank" 
                                                   class="btn btn-sm btn-outline-primary" title="View Receipt">
                                                    <i class="icon-base ri ri-file-text-line"></i>
                                                </a>
                                            @endif
                                            @if($visit->notes)
                                                <button type="button" class="btn btn-sm btn-outline-info" 
                                                        data-bs-toggle="tooltip" title="{{ $visit->notes }}">
                                                    <i class="icon-base ri ri-information-line"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="icon-base ri ri-restaurant-line" style="font-size: 3rem;"></i>
                                            <p class="mt-2">No dining visits found for {{ $member->first_name }}</p>
                                            <a href="{{ route('dining.index') }}" class="btn btn-primary btn-sm">
                                                Record First Visit
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($visits->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $visits->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-js')
<script>
// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});
</script>
@endpush 