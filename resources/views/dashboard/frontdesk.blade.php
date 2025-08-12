@extends('layouts.app')

@section('title', 'Front Desk Dashboard - ' . ($hotel->name ?? 'Membership MS'))

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
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="icon-base ri ri-user-heart-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h4 class="mb-1">Welcome, {{ $user->name }}!</h4>
                            <p class="mb-0 text-muted">Front Desk Dashboard - {{ $hotel->name }}</p>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge bg-info fs-6">Front Desk</span>
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
                                    <i class="icon-base ri ri-user-add-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">{{ number_format($todayCheckins) }}</h5>
                            <p class="mb-0 text-muted">Today's Check-ins</p>
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

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded bg-label-danger">
                                    <i class="icon-base ri ri-cake-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">{{ number_format($birthdayMembers->count()) }}</h5>
                            <p class="mb-0 text-muted">Birthday Today</p>
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
                            <a href="{{ route('dining.index') }}" class="btn btn-success w-100">
                                <i class="icon-base ri ri-user-add-line me-2"></i>
                                Check-in Member
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('members.search-page') }}" class="btn btn-info w-100">
                                <i class="icon-base ri ri-search-line me-2"></i>
                                Search Members
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('members.create') }}" class="btn btn-primary w-100">
                                <i class="icon-base ri ri-user-add-line me-2"></i>
                                Add New Member
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Birthday Alerts -->
    @if($birthdayMembers->count() > 0)
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="icon-base ri ri-cake-line me-2"></i>
                        Birthday Celebrations Today!
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($birthdayMembers as $member)
                            <div class="col-md-4 mb-3">
                                <div class="card border-warning">
                                    <div class="card-body text-center">
                                        <div class="avatar avatar-lg mb-3">
                                            <span class="avatar-initial rounded-circle bg-label-warning">
                                                {{ substr($member->first_name, 0, 1) }}
                                            </span>
                                        </div>
                                        <h6 class="mb-1">{{ $member->first_name }} {{ $member->last_name }}</h6>
                                        <p class="text-muted mb-2">{{ $member->membership_id }}</p>
                                        <span class="badge bg-warning">🎂 Birthday Special Available!</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Members -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="icon-base ri ri-team-line me-2"></i>
                        Recent Members
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentMembers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Member</th>
                                        <th>Membership Type</th>
                                        <th>Phone</th>
                                        <th>Joined</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentMembers as $member)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-3">
                                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                                            {{ substr($member->first_name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $member->first_name }} {{ $member->last_name }}</h6>
                                                        <small class="text-muted">{{ $member->membership_id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($member->membershipType)
                                                    <span class="badge bg-primary">{{ $member->membershipType->name }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $member->phone ?: 'N/A' }}</small>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $member->created_at->format('M j, Y') }}</small>
                                            </td>
                                            <td>
                                                <a href="{{ route('members.show', $member) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="icon-base ri ri-eye-line"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="icon-base ri ri-team-line icon-3x text-muted mb-3"></i>
                            <h6>No Recent Members</h6>
                            <p class="text-muted">No new members have joined yet</p>
                            <a href="{{ route('members.create') }}" class="btn btn-primary">
                                <i class="icon-base ri ri-user-add-line me-2"></i>
                                Add First Member
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Check-in Guide -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="icon-base ri ri-information-line me-2"></i>
                        Check-in Process
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar avatar-sm me-3">
                                <span class="avatar-initial rounded bg-label-primary">1</span>
                            </div>
                            <div>
                                <h6 class="mb-0">Search Member</h6>
                                <small class="text-muted">Find member by name or ID</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar avatar-sm me-3">
                                <span class="avatar-initial rounded bg-label-success">2</span>
                            </div>
                            <div>
                                <h6 class="mb-0">Review Details</h6>
                                <small class="text-muted">Check preferences & notes</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar avatar-sm me-3">
                                <span class="avatar-initial rounded bg-label-warning">3</span>
                            </div>
                            <div>
                                <h6 class="mb-0">Record Visit</h6>
                                <small class="text-muted">Enter guest count & notes</small>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('dining.index') }}" class="btn btn-success">
                            <i class="icon-base ri ri-user-add-line me-2"></i>
                            Start Check-in
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 