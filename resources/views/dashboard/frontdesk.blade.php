@extends('layouts.app')

@section('title', __('app.dashboard') . ' - ' . ($hotel->name ?? 'Membership MS'))

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
                                    <i class="icon-base ri ri-user-add-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h4 class="mb-1">{{ __('app.welcome_back') }}, {{ $user->name }}!</h4>
                            <p class="mb-0 text-muted">{{ __('app.frontdesk_dashboard') }} - {{ $hotel->name }}</p>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge bg-info fs-6">{{ __('app.frontdesk') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Summary -->
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
                            <p class="mb-0 text-muted">{{ __('app.today_checkins') }}</p>
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
                                    <i class="icon-base ri ri-team-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">{{ number_format($stats['total_members']) }}</h5>
                            <p class="mb-0 text-muted">{{ __('app.total_members') }}</p>
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
                                    <i class="icon-base ri ri-cake-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">{{ number_format($birthdayMembers->count()) }}</h5>
                            <p class="mb-0 text-muted">{{ __('app.birthday_today') }}</p>
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
                                    <i class="icon-base ri ri-time-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">{{ number_format($stats['active_visits']) }}</h5>
                            <p class="mb-0 text-muted">{{ __('app.active_visits') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Birthday Alerts -->
    @if($birthdayMembers->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="icon-base ri ri-cake-line me-2"></i>
                        {{ __('app.birthday_alerts') }}
                    </h5>
                    <p class="card-subtitle text-muted">{{ __('app.members_celebrating_today') }}</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($birthdayMembers as $member)
                        <div class="col-md-4 mb-3">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <div class="avatar avatar-lg mb-3">
                                        <span class="avatar-initial rounded bg-label-warning">
                                            <i class="icon-base ri ri-cake-line"></i>
                                        </span>
                                    </div>
                                    <h6 class="mb-1">{{ $member->name }}</h6>
                                    <p class="text-muted mb-2">{{ $member->membership_id }}</p>
                                    <span class="badge bg-warning">{{ __('app.birthday_today') }}</span>
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
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="icon-base ri ri-team-line me-2"></i>
                        {{ __('app.recent_members') }}
                    </h5>
                    <p class="card-subtitle text-muted">{{ __('app.latest_registered_members') }}</p>
                </div>
                <div class="card-body">
                    @if($recentMembers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('app.member') }}</th>
                                        <th>{{ __('app.membership_id') }}</th>
                                        <th>{{ __('app.phone') }}</th>
                                        <th>{{ __('app.membership_type') }}</th>
                                        <th>{{ __('app.registered') }}</th>
                                        <th>{{ __('app.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentMembers as $member)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <span class="avatar-initial rounded bg-label-primary">
                                                        {{ substr($member->name, 0, 1) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $member->name }}</h6>
                                                    <small class="text-muted">{{ $member->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $member->membership_id }}</span>
                                        </td>
                                        <td>{{ $member->phone }}</td>
                                        <td>
                                            @if($member->membershipType)
                                                <span class="badge bg-secondary">{{ $member->membershipType->name }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $member->created_at->format('M d, Y') }}</small>
                                        </td>
                                        <td>
                                            <a href="{{ route('members.show', $member) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="icon-base ri ri-eye-line me-1"></i>
                                                {{ __('app.view') }}
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="text-muted">
                                <i class="icon-base ri ri-team-line fs-1 mb-3"></i>
                                <h6>{{ __('app.no_recent_members') }}</h6>
                                <p>{{ __('app.no_members_registered_recently') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="icon-base ri ri-flashlight-line me-2"></i>
                        {{ __('app.quick_actions') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('dining.index') }}" class="btn btn-success w-100">
                                <i class="icon-base ri ri-restaurant-line me-2"></i>
                                {{ __('app.record_visit') }}
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('members.create') }}" class="btn btn-primary w-100">
                                <i class="icon-base ri ri-user-add-line me-2"></i>
                                {{ __('app.add_member') }}
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('members.search-page') }}" class="btn btn-info w-100">
                                <i class="icon-base ri ri-search-line me-2"></i>
                                {{ __('app.search_members') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 