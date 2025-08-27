@extends('layouts.app')

@section('title', 'Email Statistics')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">
                        <i class="icon-base ri ri-bar-chart-line me-2"></i>
                        Email Statistics
                    </h4>
                    <a href="{{ route('members.emails.index') }}" class="btn btn-secondary">
                        <i class="icon-base ri ri-arrow-left-line me-2"></i>
                        Back to Emails
                    </a>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $stats['total_sent'] }}</h4>
                                            <small>Total Sent</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="icon-base ri ri-mail-send-line fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $stats['total_opened'] }}</h4>
                                            <small>Total Opened</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="icon-base ri ri-eye-line fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $stats['total_clicked'] }}</h4>
                                            <small>Total Clicked</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="icon-base ri ri-mouse-line fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $stats['open_rate'] }}%</h4>
                                            <small>Open Rate</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="icon-base ri ri-percent-line fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chart Placeholder -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Email Performance Over Time</h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center py-5">
                                        <i class="icon-base ri ri-bar-chart-line fs-1 text-muted"></i>
                                        <p class="text-muted mt-3">Email tracking and analytics will be available in future updates.</p>
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
