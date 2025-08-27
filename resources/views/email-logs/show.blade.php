@extends('layouts.app')

@section('title', 'Email Log Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">
                        <i class="icon-base ri ri-mail-line me-2"></i>
                        Email Log Details
                    </h4>
                    <div>
                        <a href="{{ route('email-logs.index') }}" class="btn btn-secondary me-2">
                            <i class="icon-base ri ri-arrow-left-line me-2"></i>
                            Back to Logs
                        </a>
                        @if($emailLog->canRetry())
                            <form method="POST" action="{{ route('email-logs.retry', $emailLog) }}" 
                                  style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-warning" 
                                        onclick="return confirm('Are you sure you want to retry this email?')">
                                    <i class="icon-base ri ri-refresh-line me-2"></i>
                                    Retry Email
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Email Details -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Email Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <strong>Subject:</strong>
                                        </div>
                                        <div class="col-md-9">
                                            {{ $emailLog->subject }}
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <strong>Email Type:</strong>
                                        </div>
                                        <div class="col-md-9">
                                            <span class="badge bg-light text-dark">
                                                {{ $emailLog->getEmailTypeLabel() }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <strong>Status:</strong>
                                        </div>
                                        <div class="col-md-9">
                                            <span class="{{ $emailLog->getStatusBadgeClass() }}">
                                                {{ ucfirst($emailLog->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <strong>Recipient:</strong>
                                        </div>
                                        <div class="col-md-9">
                                            <div>
                                                <strong>{{ $emailLog->recipient_name ?: 'N/A' }}</strong><br>
                                                <span class="text-muted">{{ $emailLog->recipient_email }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if($emailLog->member)
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <strong>Member:</strong>
                                        </div>
                                        <div class="col-md-9">
                                            <a href="{{ route('members.show', $emailLog->member) }}" 
                                               class="text-decoration-none">
                                                {{ $emailLog->member->full_name }} 
                                                ({{ $emailLog->member->membership_id }})
                                            </a>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    @if($emailLog->error_message)
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <strong>Error Message:</strong>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="alert alert-danger">
                                                {{ $emailLog->error_message }}
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Email Content Preview -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Email Content</h6>
                                </div>
                                <div class="card-body">
                                    <div class="border rounded p-3 bg-light">
                                        {!! $emailLog->content !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Timeline and Metadata -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Timeline</h6>
                                </div>
                                <div class="card-body">
                                    <div class="timeline">
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-primary"></div>
                                            <div class="timeline-content">
                                                <h6 class="timeline-title">Created</h6>
                                                <p class="timeline-text">{{ $emailLog->created_at->format('M d, Y H:i:s') }}</p>
                                            </div>
                                        </div>
                                        
                                        @if($emailLog->sent_at)
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-success"></div>
                                            <div class="timeline-content">
                                                <h6 class="timeline-title">Sent</h6>
                                                <p class="timeline-text">{{ $emailLog->sent_at->format('M d, Y H:i:s') }}</p>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        @if($emailLog->delivered_at)
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-info"></div>
                                            <div class="timeline-content">
                                                <h6 class="timeline-title">Delivered</h6>
                                                <p class="timeline-text">{{ $emailLog->delivered_at->format('M d, Y H:i:s') }}</p>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        @if($emailLog->opened_at)
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-warning"></div>
                                            <div class="timeline-content">
                                                <h6 class="timeline-title">Opened</h6>
                                                <p class="timeline-text">{{ $emailLog->opened_at->format('M d, Y H:i:s') }}</p>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        @if($emailLog->bounced_at)
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-danger"></div>
                                            <div class="timeline-content">
                                                <h6 class="timeline-title">Bounced</h6>
                                                <p class="timeline-text">{{ $emailLog->bounced_at->format('M d, Y H:i:s') }}</p>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Metadata -->
                            @if($emailLog->metadata)
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Additional Information</h6>
                                </div>
                                <div class="card-body">
                                    <dl class="row">
                                        @if($emailLog->message_id)
                                        <dt class="col-sm-4">Message ID:</dt>
                                        <dd class="col-sm-8">
                                            <code>{{ $emailLog->message_id }}</code>
                                        </dd>
                                        @endif
                                        
                                        @if(isset($emailLog->metadata['retry_of']))
                                        <dt class="col-sm-4">Retry of:</dt>
                                        <dd class="col-sm-8">
                                            <a href="{{ route('email-logs.show', $emailLog->metadata['retry_of']) }}">
                                                Email #{{ $emailLog->metadata['retry_of'] }}
                                            </a>
                                        </dd>
                                        @endif
                                        
                                        @foreach($emailLog->metadata as $key => $value)
                                            @if($key !== 'retry_of')
                                            <dt class="col-sm-4">{{ ucfirst(str_replace('_', ' ', $key)) }}:</dt>
                                            <dd class="col-sm-8">{{ is_string($value) ? $value : json_encode($value) }}</dd>
                                            @endif
                                        @endforeach
                                    </dl>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-content {
    padding-left: 10px;
}

.timeline-title {
    margin: 0;
    font-size: 0.875rem;
    font-weight: 600;
}

.timeline-text {
    margin: 0;
    font-size: 0.75rem;
    color: #6c757d;
}
</style>
@endsection
