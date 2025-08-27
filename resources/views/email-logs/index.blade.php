@extends('layouts.app')

@section('title', 'Email Logs')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">
                        <i class="icon-base ri ri-mail-line me-2"></i>
                        Email Logs
                    </h4>
                    <div>
                        <a href="{{ route('email-logs.statistics') }}" class="btn btn-info me-2">
                            <i class="icon-base ri ri-bar-chart-line me-2"></i>
                            Statistics
                        </a>
                        <a href="{{ route('email-logs.export') }}" class="btn btn-success">
                            <i class="icon-base ri ri-download-line me-2"></i>
                            Export CSV
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $stats['total'] }}</h5>
                                    <small>Total Emails</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $stats['sent'] }}</h5>
                                    <small>Sent</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $stats['delivered'] }}</h5>
                                    <small>Delivered</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $stats['opened'] }}</h5>
                                    <small>Opened</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $stats['failed'] }}</h5>
                                    <small>Failed</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-secondary text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $stats['bounced'] }}</h5>
                                    <small>Bounced</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Filters</h6>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('email-logs.index') }}">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="">All Statuses</option>
                                            <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                                            <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                            <option value="opened" {{ request('status') === 'opened' ? 'selected' : '' }}>Opened</option>
                                            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                                            <option value="bounced" {{ request('status') === 'bounced' ? 'selected' : '' }}>Bounced</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Email Type</label>
                                        <select name="type" class="form-select">
                                            <option value="">All Types</option>
                                            @foreach($emailTypes as $type)
                                                <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>
                                                    {{ ucfirst(str_replace('_', ' ', $type)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Date From</label>
                                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Date To</label>
                                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Search</label>
                                        <input type="text" name="search" class="form-control" 
                                               placeholder="Search by email, name, or subject" 
                                               value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-6 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary me-2">
                                            <i class="icon-base ri ri-search-line me-2"></i>
                                            Filter
                                        </button>
                                        <a href="{{ route('email-logs.index') }}" class="btn btn-secondary">
                                            <i class="icon-base ri ri-refresh-line me-2"></i>
                                            Clear
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Email Logs Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Subject</th>
                                    <th>Recipient</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                {{ $log->getEmailTypeLabel() }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 200px;" title="{{ $log->subject }}">
                                                {{ $log->subject }}
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $log->recipient_name ?: 'N/A' }}</strong><br>
                                                <small class="text-muted">{{ $log->recipient_email }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="{{ $log->getStatusBadgeClass() }}">
                                                {{ ucfirst($log->status) }}
                                            </span>
                                            @if($log->error_message)
                                                <br><small class="text-danger">{{ Str::limit($log->error_message, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('email-logs.show', $log) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="View Details">
                                                    <i class="icon-base ri ri-eye-line"></i>
                                                </a>
                                                @if($log->canRetry())
                                                    <form method="POST" action="{{ route('email-logs.retry', $log) }}" 
                                                          style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-warning" 
                                                                title="Retry Email"
                                                                onclick="return confirm('Are you sure you want to retry this email?')">
                                                            <i class="icon-base ri ri-refresh-line"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="icon-base ri ri-mail-line" style="font-size: 3rem;"></i>
                                                <p class="mt-2">No email logs found</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($logs->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $logs->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
