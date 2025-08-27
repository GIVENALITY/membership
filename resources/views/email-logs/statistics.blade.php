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
                    <a href="{{ route('email-logs.index') }}" class="btn btn-secondary">
                        <i class="icon-base ri ri-arrow-left-line me-2"></i>
                        Back to Logs
                    </a>
                </div>
                <div class="card-body">
                    <!-- Key Metrics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3 class="card-title">{{ number_format($stats['total_sent']) }}</h3>
                                    <p class="card-text">Total Sent (30 days)</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3 class="card-title">{{ number_format($stats['successful']) }}</h3>
                                    <p class="card-text">Successful</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h3 class="card-title">{{ number_format($stats['failed']) }}</h3>
                                    <p class="card-text">Failed</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3 class="card-title">{{ number_format($stats['opened']) }}</h3>
                                    <p class="card-text">Opened</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Metrics -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Delivery Rate</h6>
                                </div>
                                <div class="card-body text-center">
                                    <div class="display-4 text-success">{{ $stats['delivery_rate'] }}%</div>
                                    <p class="text-muted">Emails successfully delivered</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Open Rate</h6>
                                </div>
                                <div class="card-body text-center">
                                    <div class="display-4 text-info">{{ $stats['open_rate'] }}%</div>
                                    <p class="text-muted">Emails opened by recipients</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Daily Chart -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Daily Email Activity (Last 30 Days)</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="emailChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('emailChart').getContext('2d');
    
    const data = {
        labels: {!! json_encode($dailyStats->pluck('date')) !!},
        datasets: [
            {
                label: 'Total Sent',
                data: {!! json_encode($dailyStats->pluck('total')) !!},
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            },
            {
                label: 'Successful',
                data: {!! json_encode($dailyStats->pluck('successful')) !!},
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.2)',
                tension: 0.1
            },
            {
                label: 'Failed',
                data: {!! json_encode($dailyStats->pluck('failed')) !!},
                borderColor: 'rgb(239, 68, 68)',
                backgroundColor: 'rgba(239, 68, 68, 0.2)',
                tension: 0.1
            }
        ]
    };

    const config = {
        type: 'line',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: false
                }
            }
        }
    };

    new Chart(ctx, config);
});
</script>
@endpush
@endsection
