@extends('layouts.app')

@section('title', 'Points System Settings - ' . (Auth::user()->hotel->name ?? 'Membership MS'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="icon-base ri ri-star-line me-2"></i>
                        Points System Settings
                    </h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Current Points Rules -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="icon-base ri ri-information-line me-2"></i>
                            Current Points Rules
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0">Points Earning Rules</h6>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                            <li><i class="icon-base ri ri-check-line text-success me-2"></i>1 point per person per visit</li>
                                            <li><i class="icon-base ri ri-check-line text-success me-2"></i>Minimum 50k spending per person required</li>
                                            <li><i class="icon-base ri ri-check-line text-success me-2"></i>Maximum 4 people for points calculation</li>
                                            <li><i class="icon-base ri ri-check-line text-success me-2"></i>Points are earned at checkout</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">Points Benefits</h6>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                            <li><i class="icon-base ri ri-check-line text-info me-2"></i>5+ points qualify for enhanced discounts</li>
                                            <li><i class="icon-base ri ri-check-line text-info me-2"></i>Points never expire</li>
                                            <li><i class="icon-base ri ri-check-line text-info me-2"></i>Points are hotel-specific</li>
                                            <li><i class="icon-base ri ri-check-line text-info me-2"></i>Points history is tracked</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Membership Types Points Configuration -->
                    <div class="mb-4">
                        <h5 class="text-warning mb-3">
                            <i class="icon-base ri ri-vip-crown-line me-2"></i>
                            Membership Types Points Configuration
                        </h5>
                        <div class="row">
                            @forelse($membershipTypes ?? [] as $type)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100 border-warning">
                                        <div class="card-header bg-warning text-dark">
                                            <h6 class="mb-0">{{ $type->name }}</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-2">
                                                <strong>Points Required:</strong> {{ $type->points_required_for_discount }}
                                            </div>
                                            
                                            <!-- Points Reset Policy -->
                                            <div class="mb-2">
                                                <strong>Points Reset:</strong>
                                                <div class="alert alert-sm {{ $type->points_reset_after_redemption ? 'alert-warning' : 'alert-success' }} mt-1 mb-0">
                                                    <small>{{ $type->points_reset_policy }}</small>
                                                </div>
                                            </div>
                                            
                                            @if($type->points_reset_notes && $type->points_reset_notes !== 'No additional notes')
                                                <div class="mb-2">
                                                    <small class="text-muted">{{ $type->points_reset_notes }}</small>
                                                </div>
                                            @endif
                                            
                                            <a href="{{ route('membership-types.edit', $type) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="icon-base ri ri-edit-line me-1"></i>Edit Configuration
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-warning">
                                        <i class="icon-base ri ri-alert-line me-2"></i>
                                        No membership types configured. 
                                        <a href="{{ route('membership-types.create') }}" class="alert-link">Create your first membership type</a> to configure points settings.
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Points Statistics -->
                    <div class="mb-4">
                        <h5 class="text-info mb-3">
                            <i class="icon-base ri ri-bar-chart-line me-2"></i>
                            Points Statistics
                        </h5>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="card border-success">
                                    <div class="card-body text-center">
                                        <h3 class="text-success">{{ \App\Models\Member::where('hotel_id', Auth::user()->hotel_id)->sum('total_points_earned') }}</h3>
                                        <p class="mb-0 text-muted">Total Points Earned</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card border-warning">
                                    <div class="card-body text-center">
                                        <h3 class="text-warning">{{ \App\Models\Member::where('hotel_id', Auth::user()->hotel_id)->sum('current_points_balance') }}</h3>
                                        <p class="mb-0 text-muted">Current Points Balance</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card border-info">
                                    <div class="card-body text-center">
                                        <h3 class="text-info">{{ \App\Models\Member::where('hotel_id', Auth::user()->hotel_id)->where('qualifies_for_discount', true)->count() }}</h3>
                                        <p class="mb-0 text-muted">Members with 5+ Points</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card border-primary">
                                    <div class="card-body text-center">
                                        <h3 class="text-primary">{{ \App\Models\MemberPoint::where('hotel_id', Auth::user()->hotel_id)->count() }}</h3>
                                        <p class="mb-0 text-muted">Points Transactions</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Points Management Actions -->
                    <div class="mb-4">
                        <h5 class="text-dark mb-3">
                            <i class="icon-base ri ri-tools-line me-2"></i>
                            Points Management Actions
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">Points Configuration</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('membership-types.create') }}" class="btn btn-outline-primary btn-sm">
                                                <i class="icon-base ri ri-add-line me-2"></i>
                                                Create New Membership Type
                                            </a>
                                            <a href="{{ route('membership-types.index') }}" class="btn btn-outline-info btn-sm">
                                                <i class="icon-base ri ri-list-check me-2"></i>
                                                View All Membership Types
                                            </a>
                                            <a href="{{ route('discounts.index') }}" class="btn btn-outline-success btn-sm">
                                                <i class="icon-base ri ri-percent-line me-2"></i>
                                                Manage Discount Rules
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-secondary">
                                    <div class="card-header bg-secondary text-white">
                                        <h6 class="mb-0">Points Reports</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <button type="button" class="btn btn-outline-secondary btn-sm">
                                                <i class="icon-base ri ri-file-list-line me-2"></i>
                                                Points History Report
                                            </button>
                                            <button type="button" class="btn btn-outline-warning btn-sm">
                                                <i class="icon-base ri ri-star-line me-2"></i>
                                                Points Distribution Report
                                            </button>
                                            <button type="button" class="btn btn-outline-info btn-sm">
                                                <i class="icon-base ri ri-download-line me-2"></i>
                                                Export Points Data
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Points System Information -->
                    <div class="alert alert-info">
                        <h6><i class="icon-base ri ri-information-line me-2"></i>Points System Information</h6>
                        <p class="mb-0">
                            The points system is designed to encourage customer loyalty and repeat visits. 
                            Points are automatically calculated and awarded based on spending and visit frequency. 
                            Each membership type can have different points requirements and reset policies to suit your business strategy.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 