@extends('layouts.app')

@section('title', 'Dining History - ' . (Auth::user()->hotel->name ?? 'Membership MS'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="icon-base ri ri-history-line me-2"></i>
                        Dining History & Analytics
                    </h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('dining.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="icon-base ri ri-restaurant-line me-1"></i>
                            Record Visit
                        </a>
                        <a href="{{ route('dining.history.export') }}?{{ http_build_query(request()->all()) }}" 
                           class="btn btn-success btn-sm">
                            <i class="icon-base ri ri-download-line me-1"></i>
                            Export CSV
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-3">
                            <span class="avatar-initial rounded-circle bg-label-primary">
                                <i class="icon-base ri ri-restaurant-line"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-1">Total Visits</h6>
                            <h4 class="mb-0">{{ number_format($analytics['total_visits']) }}</h4>
                            <small class="text-muted">
                                {{ $analytics['completed_visits'] }} completed, {{ $analytics['active_visits'] }} active
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-3">
                            <span class="avatar-initial rounded-circle bg-label-success">
                                <i class="icon-base ri ri-money-dollar-circle-line"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-1">Total Revenue</h6>
                            <h4 class="mb-0">TZS {{ number_format($analytics['total_revenue']) }}</h4>
                            <small class="text-muted">
                                Avg: TZS {{ number_format($analytics['avg_bill_amount']) }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-3">
                            <span class="avatar-initial rounded-circle bg-label-warning">
                                <i class="icon-base ri ri-percent-line"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-1">Total Discounts</h6>
                            <h4 class="mb-0">TZS {{ number_format($analytics['total_discounts']) }}</h4>
                            <small class="text-muted">
                                Avg: {{ number_format($analytics['avg_discount_rate'], 1) }}%
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-3">
                            <span class="avatar-initial rounded-circle bg-label-info">
                                <i class="icon-base ri ri-user-star-line"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-1">Top Member</h6>
                            @if($analytics['top_members']->count() > 0)
                                <h6 class="mb-0">{{ $analytics['top_members']->first()->member->full_name }}</h6>
                                <small class="text-muted">
                                    {{ $analytics['top_members']->first()->visit_count }} visits
                                </small>
                            @else
                                <h6 class="mb-0 text-muted">No visits yet</h6>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="icon-base ri ri-filter-3-line me-2"></i>
                        Filters & Search
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('dining.history') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ $search }}" placeholder="Member name, ID, phone...">
                        </div>

                        <div class="col-md-3">
                            <label for="member_id" class="form-label">Member</label>
                            <select class="form-select" id="member_id" name="member_id">
                                <option value="">All Members</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}" {{ $member_id == $member->id ? 'selected' : '' }}>
                                        {{ $member->full_name }} ({{ $member->membership_id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $date_from }}">
                        </div>

                        <div class="col-md-2">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $date_to }}">
                        </div>

                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All</option>
                                <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="min_amount" class="form-label">Min Amount</label>
                            <input type="number" class="form-control" id="min_amount" name="min_amount" 
                                   value="{{ $min_amount }}" placeholder="0">
                        </div>

                        <div class="col-md-3">
                            <label for="max_amount" class="form-label">Max Amount</label>
                            <input type="number" class="form-control" id="max_amount" name="max_amount" 
                                   value="{{ $max_amount }}" placeholder="Any">
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex gap-2 mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="icon-base ri ri-search-line me-1"></i>
                                    Apply Filters
                                </button>
                                <a href="{{ route('dining.history') }}" class="btn btn-outline-secondary">
                                    <i class="icon-base ri ri-refresh-line me-1"></i>
                                    Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Trends Chart -->
    @if($analytics['monthly_trends']->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="icon-base ri ri-bar-chart-line me-2"></i>
                        Monthly Trends (Last 6 Months)
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="monthlyTrendsChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Top Members -->
    @if($analytics['top_members']->count() > 0)
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="icon-base ri ri-trophy-line me-2"></i>
                        Top Members by Visits
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Visits</th>
                                    <th>Total Spent</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($analytics['top_members']->take(5) as $topMember)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    {{ substr($topMember->member->first_name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $topMember->member->full_name }}</h6>
                                                <small class="text-muted">{{ $topMember->member->membership_id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-primary">{{ $topMember->visit_count }}</span>
                                    </td>
                                    <td>TZS {{ number_format($topMember->total_spent) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="icon-base ri ri-money-dollar-circle-line me-2"></i>
                        Top Members by Spending
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Total Spent</th>
                                    <th>Visits</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($analytics['top_members']->sortByDesc('total_spent')->take(5) as $topSpender)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle bg-label-success">
                                                    {{ substr($topSpender->member->first_name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $topSpender->member->full_name }}</h6>
                                                <small class="text-muted">{{ $topSpender->member->membership_id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>TZS {{ number_format($topSpender->total_spent) }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-success">{{ $topSpender->visit_count }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Dining History Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="icon-base ri ri-table-line me-2"></i>
                        Dining History
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Visit</th>
                                    <th>Details</th>
                                    <th>Financial</th>
                                    <th>Status</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($visits as $visit)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    {{ substr($visit->member->first_name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $visit->member->full_name }}</h6>
                                                <small class="text-muted">{{ $visit->member->membership_id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <div class="fw-semibold">{{ $visit->created_at->format('M j, Y') }}</div>
                                            <div class="text-muted">{{ $visit->created_at->format('H:i') }}</div>
                                            <div>
                                                <span class="badge bg-label-info">{{ $visit->number_of_people }} people</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small">
                                            @if($visit->amount_spent)
                                                <div class="fw-semibold">TZS {{ number_format($visit->amount_spent) }}</div>
                                            @else
                                                <div class="text-muted">-</div>
                                            @endif
                                            
                                            @if($visit->discount_amount)
                                                <div class="text-success">-TZS {{ number_format($visit->discount_amount) }} ({{ $visit->discount_percentage }}%)</div>
                                            @endif
                                            
                                            @if($visit->final_amount)
                                                <div class="fw-bold">Final: TZS {{ number_format($visit->final_amount) }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small">
                                            @if($visit->is_checked_out)
                                                <span class="badge bg-label-success">Completed</span>
                                            @else
                                                <span class="badge bg-label-warning">Active</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            @if($visit->receipt_path)
                                                <a href="{{ $visit->receipt_url }}" target="_blank" 
                                                   class="btn btn-sm btn-outline-primary" title="View Receipt">
                                                    <i class="icon-base ri ri-file-text-line"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('dining.history.member', $visit->member) }}" 
                                               class="btn btn-sm btn-outline-info" title="View Member History">
                                                <i class="icon-base ri ri-user-line"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="icon-base ri ri-restaurant-line" style="font-size: 3rem;"></i>
                                            <p class="mt-2">No dining visits found</p>
                                            <a href="{{ route('dining.index') }}" class="btn btn-primary btn-sm">
                                                Record Your First Visit
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($visits->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $visits->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Monthly Trends Chart
@if($analytics['monthly_trends']->count() > 0)
const ctx = document.getElementById('monthlyTrendsChart').getContext('2d');
const monthlyData = @json($analytics['monthly_trends']);

new Chart(ctx, {
    type: 'line',
    data: {
        labels: monthlyData.map(item => {
            const date = new Date(item.month + '-01');
            return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
        }),
        datasets: [{
            label: 'Visits',
            data: monthlyData.map(item => item.visits),
            borderColor: '{{ Auth::user()->hotel->primary_color ?? "#000000" }}',
            backgroundColor: '{{ Auth::user()->hotel->primary_color ?? "#000000" }}20',
            tension: 0.4,
            yAxisID: 'y'
        }, {
            label: 'Revenue (TZS)',
            data: monthlyData.map(item => item.revenue / 1000), // Convert to thousands
            borderColor: '#28a745',
            backgroundColor: '#28a74520',
            tension: 0.4,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'Number of Visits'
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'Revenue (TZS thousands)'
                },
                grid: {
                    drawOnChartArea: false,
                },
            }
        }
    }
});
@endif
</script>
@endpush 