@extends('layouts.app')

@section('title', 'Cashier Dashboard - ' . ($hotel->name ?? 'Membership MS'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Welcome Section -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="icon-base ri ri-bank-card-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h4 class="mb-1">Welcome, {{ $user->name }}!</h4>
                            <p class="mb-0 text-muted">Cashier Dashboard - {{ $hotel->name }}</p>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge bg-warning fs-6">Cashier</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Statistics -->
    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="icon-base ri ri-check-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">{{ number_format($todayVisits) }}</h5>
                            <p class="mb-0 text-muted">Today's Completed</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="icon-base ri ri-time-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">{{ number_format($stats['active_visits']) }}</h5>
                            <p class="mb-0 text-muted">Active Visits</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="icon-base ri ri-money-dollar-circle-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">TZS {{ number_format($todayRevenue) }}</h5>
                            <p class="mb-0 text-muted">Today's Revenue</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="icon-base ri ri-team-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">{{ number_format($stats['total_members']) }}</h5>
                            <p class="mb-0 text-muted">Total Members</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="icon-base ri ri-flashlight-line me-2"></i>
                        Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('dining.index') }}" class="btn btn-warning w-100">
                                <i class="icon-base ri ri-restaurant-line me-2"></i>
                                Process Payments
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('members.search-page') }}" class="btn btn-info w-100">
                                <i class="icon-base ri ri-search-line me-2"></i>
                                Search Members
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('cashier.index') }}" class="btn btn-success w-100">
                                <i class="icon-base ri ri-bank-card-line me-2"></i>
                                Cashier Station
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Visits -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="icon-base ri ri-time-line me-2"></i>
                        Active Visits (Ready for Payment)
                    </h5>
                    <span class="badge bg-warning">{{ $activeVisits->count() }} Active</span>
                </div>
                <div class="card-body">
                    @if($activeVisits->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Member</th>
                                        <th>Guests</th>
                                        <th>Check-in Time</th>
                                        <th>Duration</th>
                                        <th>Notes</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activeVisits as $visit)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-3">
                                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                                            {{ substr($visit->member->first_name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $visit->member->first_name }} {{ $visit->member->last_name }}</h6>
                                                        <small class="text-muted">{{ $visit->member->membership_id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-info">{{ $visit->number_of_people }} people</span>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $visit->created_at->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $visit->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                @if($visit->notes)
                                                    <small class="text-muted">{{ Str::limit($visit->notes, 30) }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('dining.index') }}" class="btn btn-sm btn-warning">
                                                    <i class="icon-base ri ri-bank-card-line me-1"></i>
                                                    Process Payment
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="icon-base ri ri-time-line icon-3x text-muted mb-3"></i>
                            <h6>No Active Visits</h6>
                            <p class="text-muted">All members have been checked out</p>
                            <a href="{{ route('dining.index') }}" class="btn btn-primary">
                                <i class="icon-base ri ri-restaurant-line me-2"></i>
                                Go to Dining Management
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Tips -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="icon-base ri ri-information-line me-2"></i>
                        Payment Process Guide
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <div class="avatar avatar-lg mb-3">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        <i class="icon-base ri ri-search-line"></i>
                                    </span>
                                </div>
                                <h6>1. Find Member</h6>
                                <p class="text-muted small">Search for member in "Current Visits" list</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <div class="avatar avatar-lg mb-3">
                                    <span class="avatar-initial rounded bg-label-warning">
                                        <i class="icon-base ri ri-calculator-line"></i>
                                    </span>
                                </div>
                                <h6>2. Enter Amount</h6>
                                <p class="text-muted small">Input bill amount - system calculates discount automatically</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <div class="avatar avatar-lg mb-3">
                                    <span class="avatar-initial rounded bg-label-success">
                                        <i class="icon-base ri ri-check-line"></i>
                                    </span>
                                </div>
                                <h6>3. Complete Payment</h6>
                                <p class="text-muted small">Upload receipt and close the visit</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 