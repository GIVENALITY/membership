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
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="icon-base ri ri-bill-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h4 class="mb-1">{{ __('app.welcome_back') }}, {{ $user->name }}!</h4>
                            <p class="mb-0 text-muted">{{ __('app.cashier_dashboard') }} - {{ $hotel->name }}</p>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge bg-warning fs-6">{{ __('app.cashier') }}</span>
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
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="icon-base ri ri-time-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">{{ number_format($activeVisits->count()) }}</h5>
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
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="icon-base ri ri-check-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">{{ number_format($todayVisits) }}</h5>
                            <p class="mb-0 text-muted">{{ __('app.today_checkouts') }}</p>
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
                                    <i class="icon-base ri ri-money-dollar-circle-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">{{ number_format($todayRevenue) }} TZS</h5>
                            <p class="mb-0 text-muted">{{ __('app.today_revenue') }}</p>
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
    </div>

    <!-- Active Visits - Main Focus -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="icon-base ri ri-time-line me-2"></i>
                        {{ __('app.active_visits_ready_for_checkout') }}
                    </h5>
                    <p class="card-subtitle text-muted">{{ __('app.process_payments_for_active_visits') }}</p>
                </div>
                <div class="card-body">
                    @if($activeVisits->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('app.member') }}</th>
                                        <th>{{ __('app.membership_id') }}</th>
                                        <th>{{ __('app.number_of_people') }}</th>
                                        <th>{{ __('app.checkin_time') }}</th>
                                        <th>{{ __('app.notes') }}</th>
                                        <th>{{ __('app.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activeVisits as $visit)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <span class="avatar-initial rounded bg-label-primary">
                                                        {{ substr($visit->member->name, 0, 1) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $visit->member->name }}</h6>
                                                    <small class="text-muted">{{ $visit->member->phone }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $visit->member->membership_id }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $visit->number_of_people }} {{ __('app.people') }}</span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $visit->created_at->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            @if($visit->notes)
                                                <small class="text-muted">{{ Str::limit($visit->notes, 30) }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('cashier.index') }}?visit_id={{ $visit->id }}" 
                                               class="btn btn-primary btn-sm">
                                                <i class="icon-base ri ri-bill-line me-1"></i>
                                                {{ __('app.process_payment') }}
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
                                <i class="icon-base ri ri-time-line fs-1 mb-3"></i>
                                <h6>{{ __('app.no_active_visits') }}</h6>
                                <p>{{ __('app.no_visits_ready_for_checkout') }}</p>
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
                            <a href="{{ route('cashier.index') }}" class="btn btn-primary w-100">
                                <i class="icon-base ri ri-bill-line me-2"></i>
                                {{ __('app.cashier_desk') }}
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('dining.index') }}" class="btn btn-success w-100">
                                <i class="icon-base ri ri-restaurant-line me-2"></i>
                                {{ __('app.view_dining_management') }}
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('discounts.index') }}" class="btn btn-info w-100">
                                <i class="icon-base ri ri-percent-line me-2"></i>
                                {{ __('app.manage_discounts') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 