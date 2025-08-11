@extends('layouts.app')

@section('title', 'Discount Rules Settings - ' . (Auth::user()->hotel->name ?? 'Membership MS'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="icon-base ri ri-percent-line me-2"></i>
                        Discount Rules Settings
                    </h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Redirect to Discount Rules Management -->
                    <div class="text-center py-5">
                        <i class="icon-base ri ri-percent-line icon-4x text-primary mb-3"></i>
                        <h5>Discount Rules Management</h5>
                        <p class="text-muted">The comprehensive discount rules management system is available in the dedicated Discount Rules page.</p>
                        <a href="{{ route('discounts.index') }}" class="btn btn-primary">
                            <i class="icon-base ri ri-arrow-right-line me-2"></i>
                            Go to Discount Rules Management
                        </a>
                    </div>

                    <!-- Quick Overview -->
                    <div class="mb-4">
                        <h5 class="text-info mb-3">
                            <i class="icon-base ri ri-information-line me-2"></i>
                            What You Can Manage
                        </h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">Membership Types</h6>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                            <li><i class="icon-base ri ri-check-line text-primary me-2"></i>Base discount rates</li>
                                            <li><i class="icon-base ri ri-check-line text-primary me-2"></i>Discount progression rules</li>
                                            <li><i class="icon-base ri ri-check-line text-primary me-2"></i>Points requirements</li>
                                            <li><i class="icon-base ri ri-check-line text-primary me-2"></i>Special bonuses</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0">Points System</h6>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                            <li><i class="icon-base ri ri-check-line text-success me-2"></i>Points earning rules</li>
                                            <li><i class="icon-base ri ri-check-line text-success me-2"></i>Points reset policies</li>
                                            <li><i class="icon-base ri ri-check-line text-success me-2"></i>Qualification thresholds</li>
                                            <li><i class="icon-base ri ri-check-line text-success me-2"></i>Points history tracking</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="mb-0">Special Discounts</h6>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                            <li><i class="icon-base ri ri-check-line text-warning me-2"></i>Birthday discounts</li>
                                            <li><i class="icon-base ri ri-check-line text-warning me-2"></i>Consecutive visit bonuses</li>
                                            <li><i class="icon-base ri ri-check-line text-warning me-2"></i>Special occasion rates</li>
                                            <li><i class="icon-base ri ri-check-line text-warning me-2"></i>Loyalty rewards</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="mb-4">
                        <h5 class="text-dark mb-3">
                            <i class="icon-base ri ri-tools-line me-2"></i>
                            Quick Actions
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">Discount Management</h6>
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
                                        <h6 class="mb-0">Analytics & Reports</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('dining.history') }}" class="btn btn-outline-secondary btn-sm">
                                                <i class="icon-base ri ri-bar-chart-line me-2"></i>
                                                View Discount Analytics
                                            </a>
                                            <button type="button" class="btn btn-outline-warning btn-sm">
                                                <i class="icon-base ri ri-file-list-line me-2"></i>
                                                Generate Discount Report
                                            </button>
                                            <button type="button" class="btn btn-outline-info btn-sm">
                                                <i class="icon-base ri ri-download-line me-2"></i>
                                                Export Discount Data
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Discount System Information -->
                    <div class="alert alert-info">
                        <h6><i class="icon-base ri ri-information-line me-2"></i>Discount System Information</h6>
                        <p class="mb-0">
                            The discount system uses a multi-layered approach combining membership types, points, and special occasions. 
                            Discounts are calculated automatically based on visit count, spending, and member status. 
                            All rules can be customized per membership type to create different tiers of loyalty programs.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 