@extends('layouts.app')

@section('title', 'Discount Rules Management - ' . (Auth::user()->hotel->name ?? 'Membership MS'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="icon-base ri ri-percent-line me-2"></i>
                        Discount Rules Management
                    </h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Overview Section -->
                    <div class="alert alert-info">
                        <h6><i class="icon-base ri ri-information-line me-2"></i>How Discounts Work</h6>
                        <p class="mb-0">The system uses a multi-layered discount approach. Discounts are calculated automatically based on membership type, visit count, points earned, and special occasions.</p>
                    </div>

                    <!-- Current Membership Types -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="icon-base ri ri-vip-crown-line me-2"></i>
                            Current Membership Types & Their Discount Rules
                        </h5>
                        <div class="row">
                            @forelse($membershipTypes ?? [] as $type)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100 border-primary">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0">{{ $type->name }}</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-2">
                                                <strong>Base Discount:</strong> {{ $type->discount_rate }}%
                                            </div>
                                            
                                            @if(!empty($type->discount_progression))
                                                <div class="mb-2">
                                                    <strong>Progression:</strong>
                                                    <ul class="list-unstyled small">
                                                        @foreach($type->discount_progression as $prog)
                                                            <li>{{ $prog['visits'] }} visits → {{ $prog['discount'] }}%</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                            
                                            <div class="mb-2">
                                                <strong>Points Required:</strong> {{ $type->points_required_for_discount }}
                                            </div>
                                            
                                            @if($type->has_special_birthday_discount)
                                                <div class="mb-2">
                                                    <strong>Birthday Rate:</strong> {{ $type->birthday_discount_rate }}%
                                                </div>
                                            @endif
                                            
                                            @if($type->has_consecutive_visit_bonus)
                                                <div class="mb-2">
                                                    <strong>Consecutive Bonus:</strong> {{ $type->consecutive_visit_bonus_rate }}% after {{ $type->consecutive_visits_for_bonus }} visits
                                                </div>
                                            @endif
                                            
                                            <a href="{{ route('membership-types.edit', $type) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="icon-base ri ri-edit-line me-1"></i>Edit Rules
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-warning">
                                        <i class="icon-base ri ri-alert-line me-2"></i>
                                        No membership types configured. 
                                        <a href="{{ route('membership-types.create') }}" class="alert-link">Create your first membership type</a> to set up discount rules.
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Points System Rules -->
                    <div class="mb-4">
                        <h5 class="text-success mb-3">
                            <i class="icon-base ri ri-star-line me-2"></i>
                            Points System Rules
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

                    <!-- Special Discount Rules -->
                    <div class="mb-4">
                        <h5 class="text-warning mb-3">
                            <i class="icon-base ri ri-gift-line me-2"></i>
                            Special Discount Rules
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="mb-0">Birthday Discounts</h6>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                            <li><i class="icon-base ri ri-cake-line text-warning me-2"></i>Automatic detection of birthday visits</li>
                                            <li><i class="icon-base ri ri-cake-line text-warning me-2"></i>Higher discount rates on birthday</li>
                                            <li><i class="icon-base ri ri-cake-line text-warning me-2"></i>Configurable per membership type</li>
                                            <li><i class="icon-base ri ri-cake-line text-warning me-2"></i>7-day window around birthday</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-danger">
                                    <div class="card-header bg-danger text-white">
                                        <h6 class="mb-0">Consecutive Visit Bonuses</h6>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                            <li><i class="icon-base ri ri-time-line text-danger me-2"></i>Rewards regular customers</li>
                                            <li><i class="icon-base ri ri-time-line text-danger me-2"></i>Configurable visit thresholds</li>
                                            <li><i class="icon-base ri ri-time-line text-danger me-2"></i>Higher discount rates for loyalty</li>
                                            <li><i class="icon-base ri ri-time-line text-danger me-2"></i>Resets if customer misses a day</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Discount Calculation Examples -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="icon-base ri ri-calculator-line me-2"></i>
                            Discount Calculation Examples
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Basic Member Example</h6>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Scenario:</strong> Basic member with 8 visits, 6 points, birthday visit</p>
                                        <ul class="list-unstyled small">
                                            <li>• Base Rate: 5%</li>
                                            <li>• Progression (8 visits): 10%</li>
                                            <li>• Points (6 pts): Enhanced rate</li>
                                            <li>• Birthday: 20% (highest)</li>
                                            <li><strong>Final Rate: 20%</strong></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">VIP Member Example</h6>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Scenario:</strong> VIP member with 12 visits, 8 points, consecutive bonus</p>
                                        <ul class="list-unstyled small">
                                            <li>• Base Rate: 15%</li>
                                            <li>• Progression (12 visits): 25%</li>
                                            <li>• Points (8 pts): Enhanced rate</li>
                                            <li>• Consecutive Bonus: 25%</li>
                                            <li><strong>Final Rate: 25%</strong></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Management Actions -->
                    <div class="text-center">
                        <h5 class="text-dark mb-3">Manage Discount Rules</h5>
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <a href="{{ route('membership-types.create') }}" class="btn btn-primary">
                                <i class="icon-base ri ri-add-line me-2"></i>
                                Create New Membership Type
                            </a>
                            <a href="{{ route('membership-types.index') }}" class="btn btn-outline-primary">
                                <i class="icon-base ri ri-list-check me-2"></i>
                                View All Membership Types
                            </a>
                            <a href="{{ route('dining.history') }}" class="btn btn-outline-info">
                                <i class="icon-base ri ri-bar-chart-line me-2"></i>
                                View Discount Analytics
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 