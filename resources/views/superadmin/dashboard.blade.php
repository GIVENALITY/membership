@extends('layouts.app')

@section('title', __('app.superadmin_dashboard'))

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
                                <span class="avatar-initial rounded bg-label-dark">
                                    <i class="icon-base ri ri-settings-4-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h4 class="mb-1">{{ __('app.welcome_back') }}, {{ $user->name }}!</h4>
                            <p class="mb-0 text-muted">{{ __('app.superadmin_dashboard') }} - {{ __('app.global_system_management') }}</p>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge bg-dark fs-6">{{ __('app.superadmin') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Statistics -->
    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="icon-base ri ri-building-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">{{ number_format($stats['total_hotels']) }}</h5>
                            <p class="mb-0 text-muted">{{ __('app.total_hotels') }}</p>
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
                                    <i class="icon-base ri ri-team-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">{{ number_format($stats['total_users']) }}</h5>
                            <p class="mb-0 text-muted">{{ __('app.total_users') }}</p>
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
                                    <i class="icon-base ri ri-check-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">{{ number_format($stats['active_hotels']) }}</h5>
                            <p class="mb-0 text-muted">{{ __('app.active_hotels') }}</p>
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
                                    <i class="icon-base ri ri-user-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">{{ number_format($stats['active_users']) }}</h5>
                            <p class="mb-0 text-muted">{{ __('app.active_users') }}</p>
                        </div>
                    </div>
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
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('superadmin.translations') }}" class="btn btn-primary w-100">
                                <i class="icon-base ri ri-translate-2 me-2"></i>
                                {{ __('app.translation_management') }}
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('superadmin.hotels') }}" class="btn btn-success w-100">
                                <i class="icon-base ri ri-building-line me-2"></i>
                                {{ __('app.manage_hotels') }}
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('superadmin.users') }}" class="btn btn-info w-100">
                                <i class="icon-base ri ri-team-line me-2"></i>
                                {{ __('app.manage_users') }}
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('superadmin.logs') }}" class="btn btn-warning w-100">
                                <i class="icon-base ri ri-file-list-line me-2"></i>
                                {{ __('app.view_logs') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 