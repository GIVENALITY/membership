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
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="icon-base ri ri-user-settings-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h4 class="mb-1">{{ __('app.welcome_back') }}, {{ $user->name }}!</h4>
                            <p class="mb-0 text-muted">{{ __('app.dashboard') }} - {{ $hotel->name }}</p>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge bg-primary fs-6">{{ __('app.manager') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded bg-label-success">
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
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="icon-base ri ri-restaurant-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">{{ number_format($stats['total_visits']) }}</h5>
                            <p class="mb-0 text-muted">{{ __('app.total_visits') }}</p>
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
                            <p class="mb-0 text-muted">{{ __('app.active_visits') }}</p>
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
                                    <i class="icon-base ri ri-calendar-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">{{ number_format($stats['today_visits']) }}</h5>
                            <p class="mb-0 text-muted">{{ __('app.today_visits') }}</p>
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
                        {{ __('app.quick_actions') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('members.create') }}" class="btn btn-primary w-100">
                                <i class="icon-base ri ri-user-add-line me-2"></i>
                                {{ __('app.add_new_member') }}
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('dining.index') }}" class="btn btn-success w-100">
                                <i class="icon-base ri ri-restaurant-line me-2"></i>
                                {{ __('app.manage_dining') }}
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('user-management.create') }}" class="btn btn-info w-100">
                                <i class="icon-base ri ri-team-line me-2"></i>
                                {{ __('app.add_team_member') }}
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('reports.members') }}" class="btn btn-warning w-100">
                                <i class="icon-base ri ri-file-chart-line me-2"></i>
                                {{ __('app.view_reports') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Statistics -->
    <div class="row">
        <!-- Recent Visits -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="icon-base ri ri-time-line me-2"></i>
                        {{ __('app.recent_visits') }}
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentVisits->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('app.member') }}</th>
                                        <th>{{ __('app.status') }}</th>
                                        <th>{{ __('app.amount') }}</th>
                                        <th>{{ __('app.time') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentVisits as $visit)
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
                                                @if($visit->is_checked_out)
                                                    <span class="badge bg-success">{{ __('app.completed') }}</span>
                                                @else
                                                    <span class="badge bg-warning">{{ __('app.active') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($visit->is_checked_out && $visit->final_amount)
                                                    <strong>TZS {{ number_format($visit->final_amount) }}</strong>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $visit->created_at->format('M j, H:i') }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="icon-base ri ri-time-line icon-3x text-muted mb-3"></i>
                            <h6>{{ __('app.no_recent_visits') }}</h6>
                            <p class="text-muted">{{ __('app.no_visits_recorded') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Monthly Statistics -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="icon-base ri ri-bar-chart-line me-2"></i>
                        {{ __('app.this_month') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">{{ __('app.visits') }}</span>
                            <span class="fw-bold">{{ number_format($monthlyStats['monthly_visits']) }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: {{ min(100, ($monthlyStats['monthly_visits'] / max(1, $stats['total_visits'])) * 100) }}%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">{{ __('app.revenue') }}</span>
                            <span class="fw-bold">TZS {{ number_format($monthlyStats['monthly_revenue']) }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: 75%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">{{ __('app.new_members') }}</span>
                            <span class="fw-bold">{{ number_format($monthlyStats['monthly_members']) }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-info" style="width: {{ min(100, ($monthlyStats['monthly_members'] / max(1, $stats['total_members'])) * 100) }}%"></div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('reports.members') }}" class="btn btn-outline-primary btn-sm">
                            <i class="icon-base ri ri-file-chart-line me-2"></i>
                            {{ __('app.view_detailed_reports') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Membership Types Overview -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="icon-base ri ri-vip-crown-line me-2"></i>
                        {{ __('app.membership_types') }}
                    </h5>
                </div>
                <div class="card-body">
                    @if($membershipTypes->count() > 0)
                        <div class="row">
                            @foreach($membershipTypes as $type)
                                <div class="col-md-4 mb-3">
                                    <div class="card border-primary">
                                        <div class="card-body text-center">
                                            <h6 class="card-title text-primary">{{ $type->name }}</h6>
                                            <p class="text-muted mb-2">{{ $type->description }}</p>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">{{ __('app.price') }}:</span>
                                                <span class="fw-bold">TZS {{ number_format($type->price) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">{{ __('app.discount') }}:</span>
                                                <span class="fw-bold text-success">{{ $type->discount_rate }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="icon-base ri ri-vip-crown-line icon-3x text-muted mb-3"></i>
                            <h6>{{ __('app.no_membership_types') }}</h6>
                            <p class="text-muted">{{ __('app.create_membership_types_to_get_started') }}</p>
                            <a href="{{ route('membership-types.create') }}" class="btn btn-primary">
                                <i class="icon-base ri ri-add-line me-2"></i>
                                {{ __('app.create_membership_type') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 