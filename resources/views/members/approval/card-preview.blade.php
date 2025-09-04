@extends('layouts.app')

@section('title', 'Card Preview - ' . $member->full_name)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">
                        <i class="icon-base ri ri-credit-card-line me-2"></i>
                        Card Preview: {{ $member->full_name }}
                    </h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('members.approval.show', $member) }}" class="btn btn-outline-secondary">
                            <i class="icon-base ri ri-arrow-left-line me-2"></i>Back to Member
                        </a>
                        <a href="{{ route('members.download-card', $member) }}" class="btn btn-primary">
                            <i class="icon-base ri ri-download-line me-2"></i>Download Card
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Member Information -->
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="icon-base ri ri-user-line me-2"></i>
                                        Member Details
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Name</label>
                                        <p class="mb-0">{{ $member->full_name }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Membership ID</label>
                                        <p class="mb-0">
                                            <span class="badge bg-primary">{{ $member->membership_id }}</span>
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Membership Type</label>
                                        <p class="mb-0">{{ optional($member->membershipType)->name ?? 'N/A' }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Status</label>
                                        <p class="mb-0">{!! $member->getApprovalStatusBadgeAttribute() !!}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Expires</label>
                                        <p class="mb-0">{{ $member->expires_at ? $member->expires_at->format('M d, Y') : 'No expiration' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Virtual Card -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="icon-base ri ri-credit-card-line me-2"></i>
                                        Virtual Card
                                    </h6>
                                </div>
                                <div class="card-body text-center">
                                    @if($cardUrl)
                                        <img src="{{ $cardUrl }}" alt="Member Card" class="img-fluid rounded shadow-sm" style="max-width: 100%;">
                                        <div class="mt-3">
                                            <a href="{{ $cardUrl }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                                <i class="icon-base ri ri-external-link-line me-2"></i>View Full Size
                                            </a>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="icon-base ri ri-error-warning-line fs-1 text-muted"></i>
                                            <p class="text-muted mt-2">No card image available</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- QR Code -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="icon-base ri ri-qr-code-line me-2"></i>
                                        QR Code
                                    </h6>
                                </div>
                                <div class="card-body text-center">
                                    @if($qrUrl)
                                        <img src="{{ $qrUrl }}" alt="QR Code" class="img-fluid rounded shadow-sm" style="max-width: 150px;">
                                        <div class="mt-3">
                                            <a href="{{ $qrUrl }}" target="_blank" class="btn btn-outline-info btn-sm">
                                                <i class="icon-base ri ri-external-link-line me-2"></i>View Full Size
                                            </a>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                Scan to verify membership
                                            </small>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="icon-base ri ri-qr-code-line fs-1 text-muted"></i>
                                            <p class="text-muted mt-2">No QR code available</p>
                                            <form action="{{ route('members.generate-qr-code', $member) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="icon-base ri ri-add-line me-2"></i>Generate QR Code
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Management Actions -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="icon-base ri ri-settings-line me-2"></i>
                                        Card Management
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex gap-2 flex-wrap">
                                        <form action="{{ route('members.regenerate-card', $member) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to regenerate this card? This will create a new card and QR code.')">
                                                <i class="icon-base ri ri-refresh-line me-2"></i>Regenerate Card & QR
                                            </button>
                                        </form>
                                        
                                        @if($qrUrl)
                                            <form action="{{ route('members.generate-qr-code', $member) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-warning" onclick="return confirm('Are you sure you want to regenerate the QR code?')">
                                                    <i class="icon-base ri ri-refresh-line me-2"></i>Regenerate QR Only
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('members.approval.show', $member) }}" class="btn btn-outline-secondary">
                                            <i class="icon-base ri ri-arrow-left-line me-2"></i>Back to Member
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
@endsection
