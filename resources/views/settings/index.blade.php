@extends('layouts.app')

@section('title', 'General Settings - ' . (Auth::user()->hotel->name ?? 'Membership MS'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="icon-base ri ri-settings-4-line me-2"></i>
                        General Settings
                    </h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- System Information -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="icon-base ri ri-information-line me-2"></i>
                            System Information
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">Application Details</h6>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                            <li><strong>Application Name:</strong> Membership MS</li>
                                            <li><strong>Version:</strong> 1.0.0</li>
                                            <li><strong>Environment:</strong> {{ config('app.env') }}</li>
                                            <li><strong>Timezone:</strong> {{ config('app.timezone') }}</li>
                                            <li><strong>Currency:</strong> TZS (Tanzanian Shilling)</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">Hotel Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                            <li><strong>Hotel Name:</strong> {{ Auth::user()->hotel->name ?? 'Not Set' }}</li>
                                            <li><strong>Email:</strong> {{ Auth::user()->hotel->email ?? 'Not Set' }}</li>
                                            <li><strong>Phone:</strong> {{ Auth::user()->hotel->phone ?? 'Not Set' }}</li>
                                            <li><strong>Location:</strong> {{ Auth::user()->hotel->city ?? 'Not Set' }}, {{ Auth::user()->hotel->country ?? 'Not Set' }}</li>
                                            <li><strong>Status:</strong> 
                                                @if(Auth::user()->hotel && Auth::user()->hotel->is_active)
                                                    <span class="badge bg-label-success">Active</span>
                                                @else
                                                    <span class="badge bg-label-secondary">Inactive</span>
                                                @endif
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Settings -->
                    <div class="mb-4">
                        <h5 class="text-success mb-3">
                            <i class="icon-base ri ri-dashboard-line me-2"></i>
                            Quick Settings
                        </h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="icon-base ri ri-star-line icon-3x text-warning mb-3"></i>
                                        <h6>Points System</h6>
                                        <p class="text-muted small">Configure points earning rules and reset policies</p>
                                        <a href="{{ route('settings.points') }}" class="btn btn-outline-primary btn-sm">
                                            <i class="icon-base ri ri-settings-line me-1"></i>
                                            Configure
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="icon-base ri ri-mail-line icon-3x text-info mb-3"></i>
                                        <h6>Email Templates</h6>
                                        <p class="text-muted small">Customize welcome and birthday email templates</p>
                                        <a href="{{ route('settings.email') }}" class="btn btn-outline-primary btn-sm">
                                            <i class="icon-base ri ri-settings-line me-1"></i>
                                            Configure
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="icon-base ri ri-percent-line icon-3x text-success mb-3"></i>
                                        <h6>Discount Rules</h6>
                                        <p class="text-muted small">Manage discount progression and special offers</p>
                                        <a href="{{ route('settings.discounts') }}" class="btn btn-outline-primary btn-sm">
                                            <i class="icon-base ri ri-settings-line me-1"></i>
                                            Configure
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Statistics -->
                    <div class="mb-4">
                        <h5 class="text-info mb-3">
                            <i class="icon-base ri ri-bar-chart-line me-2"></i>
                            System Statistics
                        </h5>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="card border-success">
                                    <div class="card-body text-center">
                                        <h3 class="text-success">{{ \App\Models\Member::where('hotel_id', Auth::user()->hotel_id)->count() }}</h3>
                                        <p class="mb-0 text-muted">Total Members</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card border-primary">
                                    <div class="card-body text-center">
                                        <h3 class="text-primary">{{ \App\Models\MembershipType::where('hotel_id', Auth::user()->hotel_id)->count() }}</h3>
                                        <p class="mb-0 text-muted">Membership Types</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card border-warning">
                                    <div class="card-body text-center">
                                        <h3 class="text-warning">{{ \App\Models\DiningVisit::where('hotel_id', Auth::user()->hotel_id)->count() }}</h3>
                                        <p class="mb-0 text-muted">Total Visits</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card border-info">
                                    <div class="card-body text-center">
                                        <h3 class="text-info">{{ \App\Models\DiningVisit::where('hotel_id', Auth::user()->hotel_id)->whereDate('created_at', today())->count() }}</h3>
                                        <p class="mb-0 text-muted">Today's Visits</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Maintenance Actions -->
                    <div class="mb-4">
                        <h5 class="text-warning mb-3">
                            <i class="icon-base ri ri-tools-line me-2"></i>
                            Maintenance Actions
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="mb-0">System Maintenance</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <button type="button" class="btn btn-outline-warning btn-sm">
                                                <i class="icon-base ri ri-refresh-line me-2"></i>
                                                Clear Cache
                                            </button>
                                            <button type="button" class="btn btn-outline-info btn-sm">
                                                <i class="icon-base ri ri-database-line me-2"></i>
                                                Backup Database
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm">
                                                <i class="icon-base ri ri-file-list-line me-2"></i>
                                                Generate Reports
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">Quick Actions</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('membership-types.create') }}" class="btn btn-outline-primary btn-sm">
                                                <i class="icon-base ri ri-add-line me-2"></i>
                                                Create Membership Type
                                            </a>
                                            <a href="{{ route('members.create') }}" class="btn btn-outline-success btn-sm">
                                                <i class="icon-base ri ri-user-add-line me-2"></i>
                                                Add New Member
                                            </a>
                                            <a href="{{ route('dining.index') }}" class="btn btn-outline-warning btn-sm">
                                                <i class="icon-base ri ri-restaurant-line me-2"></i>
                                                Record Visit
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 