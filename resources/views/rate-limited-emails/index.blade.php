@extends('layouts.app')

@section('title', 'Rate-Limited Email Sending')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="icon-base ri ri-mail-send-line me-2"></i>
                        Rate-Limited Email Sending
                    </h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('email-logs.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="icon-base ri ri-list-check me-1"></i>
                            Email Logs
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Current Batch Status -->
                    @if($currentBatch)
                    <div class="alert alert-info">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>📧 Active Email Batch</strong><br>
                                <small>Started: {{ $currentBatch['started_at']->format('M d, Y H:i:s') }}</small><br>
                                <small>Subject: {{ $currentBatch['subject'] }}</small><br>
                                <small>Batch Size: {{ $currentBatch['batch_size'] }} emails/hour</small>
                                @if($currentBatch['dry_run'])
                                    <br><span class="badge bg-warning">DRY RUN MODE</span>
                                @endif
                            </div>
                            <form action="{{ route('rate-limited-emails.stop') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Stop the current batch?')">
                                    <i class="icon-base ri ri-stop-circle-line me-1"></i>
                                    Stop Batch
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif

                    <!-- Email Sending Form -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="icon-base ri ri-send-plane-line me-2"></i>
                                Send Rate-Limited Emails
                            </h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('rate-limited-emails.start') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Email Subject *</label>
                                            <input type="text" name="subject" class="form-control" required 
                                                   placeholder="Enter email subject">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Batch Size (emails/hour)</label>
                                            <select name="batch_size" class="form-select">
                                                <option value="60" selected>60 emails/hour (Recommended)</option>
                                                <option value="50">50 emails/hour (Conservative)</option>
                                                <option value="70">70 emails/hour (Aggressive)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email Content *</label>
                                    <textarea name="content" class="form-control" rows="6" required 
                                              placeholder="Enter your email content here..."></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Resume From (Optional)</label>
                                            <select name="resume_from" class="form-select">
                                                <option value="">Start from beginning</option>
                                                @foreach($recentLogs as $log)
                                                    <option value="{{ $log->id }}">
                                                        {{ $log->recipient_email }} ({{ $log->created_at->format('M d, H:i') }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">Select to resume from a specific email</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Specific Members (Optional)</label>
                                            <select name="member_ids[]" class="form-select" multiple>
                                                @foreach(\App\Models\Member::where('hotel_id', auth()->user()->hotel_id)->orderBy('first_name')->get() as $member)
                                                    <option value="{{ $member->id }}">
                                                        {{ $member->full_name }} ({{ $member->email }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">Leave empty to send to all members</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="dry_run" class="form-check-input" id="dryRun">
                                        <label class="form-check-label" for="dryRun">
                                            <strong>Dry Run Mode</strong> - Test the process without sending actual emails
                                        </label>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="icon-base ri ri-send-plane-line me-1"></i>
                                        Start Email Batch
                                    </button>
                                    <button type="submit" class="btn btn-warning" onclick="document.getElementById('dryRun').checked = true;">
                                        <i class="icon-base ri ri-eye-line me-1"></i>
                                        Test Run
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Progress Tracking -->
                    <div class="card mb-4" id="progressCard" style="display: none;">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="icon-base ri ri-dashboard-line me-2"></i>
                                Batch Progress
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h4 id="sentCount">0</h4>
                                        <small class="text-success">Sent</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h4 id="failedCount">0</h4>
                                        <small class="text-danger">Failed</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h4 id="totalMembers">0</h4>
                                        <small class="text-muted">Total</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h4 id="batchSize">60</h4>
                                        <small class="text-info">Per Hour</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="progress mt-3">
                                <div class="progress-bar" id="progressBar" role="progressbar" style="width: 0%"></div>
                            </div>
                            
                            <div class="mt-3">
                                <h6>Recent Activity:</h6>
                                <div id="recentActivity" class="small">
                                    <div class="text-muted">No activity yet...</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Failed Emails Retry -->
                    @if($failedEmails->isNotEmpty())
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="icon-base ri ri-error-warning-line me-2"></i>
                                Failed Emails ({{ $failedEmails->count() }})
                            </h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('rate-limited-emails.retry') }}" method="POST">
                                @csrf
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th width="50">
                                                    <input type="checkbox" id="selectAllFailed" class="form-check-input">
                                                </th>
                                                <th>Recipient</th>
                                                <th>Subject</th>
                                                <th>Error</th>
                                                <th>Failed At</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($failedEmails as $log)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="email_log_ids[]" value="{{ $log->id }}" 
                                                           class="form-check-input failed-email-checkbox">
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $log->recipient_name ?: 'N/A' }}</strong><br>
                                                        <small class="text-muted">{{ $log->recipient_email }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-truncate" style="max-width: 200px;" title="{{ $log->subject }}">
                                                        {{ $log->subject }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <small class="text-danger">{{ Str::limit($log->error_message, 50) }}</small>
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $log->created_at->format('M d, H:i') }}</small>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <small class="text-muted">
                                        <span id="selectedCount">0</span> emails selected for retry
                                    </small>
                                    <button type="submit" class="btn btn-warning btn-sm" id="retryBtn" disabled>
                                        <i class="icon-base ri ri-refresh-line me-1"></i>
                                        Retry Selected
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Progress tracking
    let progressInterval;
    
    @if($currentBatch)
        startProgressTracking();
    @endif
    
    // Failed emails selection
    const selectAllFailed = document.getElementById('selectAllFailed');
    const failedEmailCheckboxes = document.querySelectorAll('.failed-email-checkbox');
    const selectedCount = document.getElementById('selectedCount');
    const retryBtn = document.getElementById('retryBtn');
    
    if (selectAllFailed) {
        selectAllFailed.addEventListener('change', function() {
            failedEmailCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });
        
        failedEmailCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });
    }
    
    function updateSelectedCount() {
        const checked = document.querySelectorAll('.failed-email-checkbox:checked').length;
        selectedCount.textContent = checked;
        retryBtn.disabled = checked === 0;
    }
    
    function startProgressTracking() {
        document.getElementById('progressCard').style.display = 'block';
        
        progressInterval = setInterval(function() {
            fetch('{{ route("rate-limited-emails.progress") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'completed') {
                        clearInterval(progressInterval);
                        document.getElementById('progressCard').innerHTML = 
                            '<div class="alert alert-success">✅ Email batch completed!</div>';
                        return;
                    }
                    
                    // Update progress
                    document.getElementById('sentCount').textContent = data.sent_count;
                    document.getElementById('failedCount').textContent = data.failed_count;
                    document.getElementById('totalMembers').textContent = data.total_members;
                    document.getElementById('batchSize').textContent = data.batch_size;
                    
                    // Update progress bar
                    const progress = data.total_members > 0 ? 
                        ((data.sent_count + data.failed_count) / data.total_members) * 100 : 0;
                    document.getElementById('progressBar').style.width = progress + '%';
                    document.getElementById('progressBar').textContent = Math.round(progress) + '%';
                    
                    // Update recent activity
                    const activityHtml = data.recent_logs.map(log => 
                        `<div class="mb-1">
                            <span class="badge ${log.status === 'sent' ? 'bg-success' : 'bg-danger'}">${log.status}</span>
                            ${log.email} - ${log.sent_at || 'N/A'}
                            ${log.error ? `<br><small class="text-danger">${log.error}</small>` : ''}
                        </div>`
                    ).join('');
                    
                    document.getElementById('recentActivity').innerHTML = 
                        activityHtml || '<div class="text-muted">No recent activity...</div>';
                })
                .catch(error => {
                    console.error('Error fetching progress:', error);
                });
        }, 5000); // Update every 5 seconds
    }
});
</script>
@endpush
