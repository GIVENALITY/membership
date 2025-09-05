@extends('layouts.app')

@section('title', 'Review Member - ' . $member->full_name)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h4 class="card-title mb-0">
              <i class="icon-base ri ri-user-check-line me-2"></i>
              Review Member: {{ $member->full_name }}
            </h4>
            <p class="text-muted mb-0">Review member details and approve/reject membership</p>
          </div>
          <div class="d-flex gap-2">
            <a href="{{ route('members.approval.index') }}" class="btn btn-outline-secondary">
              <i class="icon-base ri ri-arrow-left-line me-2"></i>Back to Approval List
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- Member Information -->
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">
            <i class="icon-base ri ri-user-line me-2"></i>
            Member Information
          </h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label fw-bold">Full Name</label>
                <p class="form-control-plaintext">{{ $member->full_name }}</p>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Membership ID</label>
                <p class="form-control-plaintext">
                  <span class="badge bg-label-primary">{{ $member->membership_id }}</span>
                </p>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Email</label>
                <p class="form-control-plaintext">{{ $member->email }}</p>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Phone</label>
                <p class="form-control-plaintext">{{ $member->phone }}</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label fw-bold">Membership Type</label>
                <p class="form-control-plaintext">{{ optional($member->membershipType)->name ?? 'N/A' }}</p>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Date of Birth</label>
                <p class="form-control-plaintext">{{ $member->birth_date ? $member->birth_date->format('M d, Y') : 'N/A' }}</p>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Join Date</label>
                <p class="form-control-plaintext">{{ $member->join_date ? $member->join_date->format('M d, Y') : 'N/A' }}</p>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Address</label>
                <p class="form-control-plaintext">{{ $member->address ?? 'N/A' }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Payment Proof Section -->
      <div class="card mt-3">
        <div class="card-header">
          <h5 class="card-title mb-0">
            <i class="icon-base ri ri-file-text-line me-2"></i>
            Payment Proof
            @if($member->payment_proof_path)
              <span class="badge bg-label-success ms-2">Uploaded</span>
            @else
              <span class="badge bg-label-warning ms-2">Pending</span>
            @endif
          </h5>
        </div>
        <div class="card-body">
          @if($member->payment_proof_path)
            <!-- Payment proof exists -->
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label fw-bold">Payment Proof File</label>
                  <div class="d-flex gap-2">
                    <a href="{{ route('members.approval.download-payment-proof', $member) }}" 
                       class="btn btn-outline-primary btn-sm">
                      <i class="icon-base ri ri-download-line me-2"></i>Download
                    </a>
                    @if(in_array(strtolower(pathinfo($member->payment_proof_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']))
                      <button type="button" class="btn btn-outline-info btn-sm" 
                              onclick="viewPaymentProof('{{ asset('storage/' . $member->payment_proof_path) }}')">
                        <i class="icon-base ri ri-eye-line me-2"></i>View
                      </button>
                    @endif
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label fw-bold">Payment Notes</label>
                  <p class="form-control-plaintext">{{ $member->payment_notes ?? 'No notes provided' }}</p>
                </div>
              </div>
              <div class="col-md-6">
                @if(in_array(strtolower(pathinfo($member->payment_proof_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']))
                  <div class="text-center">
                    <img src="{{ asset('storage/' . $member->payment_proof_path) }}" 
                         alt="Payment Proof" 
                         class="img-fluid rounded" 
                         style="max-height: 200px;">
                  </div>
                @endif
              </div>
            </div>
          @else
            <!-- No payment proof uploaded -->
            <div class="alert alert-warning">
              <div class="d-flex align-items-center">
                <i class="icon-base ri ri-alert-line me-2"></i>
                <div>
                  <h6 class="alert-heading mb-1">Payment Proof Required</h6>
                  <p class="mb-0">This member has not uploaded payment proof yet. Please request payment proof before approving membership.</p>
                </div>
              </div>
            </div>
            
            <!-- Upload Payment Proof Form -->
            <form method="POST" action="{{ route('members.approval.upload-payment-proof', $member) }}" enctype="multipart/form-data">
              @csrf
              <div class="row">
                <div class="col-md-8">
                  <div class="mb-3">
                    <label for="payment_proof" class="form-label fw-bold">Upload Payment Proof</label>
                    <input type="file" class="form-control" id="payment_proof" name="payment_proof" 
                           accept=".jpg,.jpeg,.png,.gif,.pdf" required>
                    <div class="form-text">Accepted formats: JPG, PNG, GIF, PDF (Max: 2MB)</div>
                  </div>
                  <div class="mb-3">
                    <label for="payment_notes" class="form-label fw-bold">Payment Notes</label>
                    <textarea class="form-control" id="payment_notes" name="payment_notes" rows="3" 
                              placeholder="Add any notes about the payment..."></textarea>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                      <i class="icon-base ri ri-upload-line me-2"></i>Upload Payment Proof
                    </button>
                  </div>
                </div>
              </div>
            </form>
          @endif
        </div>
      </div>

      <!-- Approval Actions -->
      @if($member->approval_status === 'pending')
        <div class="card mt-3">
          <div class="card-header">
            <h5 class="card-title mb-0">
              <i class="icon-base ri ri-check-line me-2"></i>
              Approval Actions
            </h5>
          </div>
          <div class="card-body">
            <form action="{{ route('members.approval.approve', $member) }}" method="POST" class="mb-3">
              @csrf
              <div class="mb-3">
                <label for="approval_notes" class="form-label">Approval Notes (Optional)</label>
                <textarea class="form-control" id="approval_notes" name="approval_notes" 
                          rows="3" placeholder="Add any notes about this approval..."></textarea>
              </div>
              <button type="submit" class="btn btn-success">
                <i class="icon-base ri ri-check-line me-2"></i>Approve Member
              </button>
            </form>

            <hr>

            <form action="{{ route('members.approval.reject', $member) }}" method="POST">
              @csrf
              <div class="mb-3">
                <label for="rejection_notes" class="form-label">Rejection Notes <span class="text-danger">*</span></label>
                <textarea class="form-control" id="rejection_notes" name="approval_notes" 
                          rows="3" placeholder="Please provide a reason for rejection..." required></textarea>
              </div>
              <button type="submit" class="btn btn-danger">
                <i class="icon-base ri ri-close-line me-2"></i>Reject Member
              </button>
            </form>
          </div>
        </div>
      @endif

      <!-- Payment Verification Actions -->
      @if($member->approval_status === 'approved' && $member->payment_status === 'pending')
        <div class="card mt-3">
          <div class="card-header">
            <h5 class="card-title mb-0">
              <i class="icon-base ri ri-money-dollar-circle-line me-2"></i>
              Payment Verification
            </h5>
          </div>
          <div class="card-body">
            <form action="{{ route('members.approval.verify-payment', $member) }}" method="POST">
              @csrf
              <div class="mb-3">
                <label for="payment_status" class="form-label">Payment Status <span class="text-danger">*</span></label>
                <select class="form-select" id="payment_status" name="payment_status" required>
                  <option value="">Select status...</option>
                  <option value="verified">Payment Verified</option>
                  <option value="failed">Payment Failed</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="payment_notes" class="form-label">Verification Notes (Optional)</label>
                <textarea class="form-control" id="payment_notes" name="payment_notes" 
                          rows="3" placeholder="Add notes about payment verification..."></textarea>
              </div>
              <button type="submit" class="btn btn-primary">
                <i class="icon-base ri ri-check-line me-2"></i>Verify Payment
              </button>
            </form>
          </div>
        </div>
      @endif

      <!-- Card Issuance Approval -->
      @if($member->payment_status === 'verified' && $member->card_issuance_status === 'pending')
        <div class="card mt-3">
          <div class="card-header">
            <h5 class="card-title mb-0">
              <i class="icon-base ri ri-credit-card-line me-2"></i>
              Card Issuance Approval
            </h5>
          </div>
          <div class="card-body">
            <form action="{{ route('members.approval.approve-card-issuance', $member) }}" method="POST">
              @csrf
              <div class="mb-3">
                <label for="card_issuance_notes" class="form-label">Card Issuance Notes (Optional)</label>
                <textarea class="form-control" id="card_issuance_notes" name="card_issuance_notes" 
                          rows="3" placeholder="Add notes about card issuance..."></textarea>
              </div>
              <button type="submit" class="btn btn-success">
                <i class="icon-base ri ri-credit-card-line me-2"></i>Approve Card Issuance
              </button>
            </form>
          </div>
        </div>
      @endif

      <!-- Card Management -->
      @if($member->card_issuance_status === 'approved')
        <div class="card mt-3">
          <div class="card-header">
            <h5 class="card-title mb-0">
              <i class="icon-base ri ri-credit-card-line me-2"></i>
              Card Management
            </h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <h6>Virtual Card</h6>
                @if($member->card_image_path)
                  <div class="d-flex gap-2 mb-3">
                    <a href="{{ route('members.card-preview', $member) }}" class="btn btn-outline-primary btn-sm" target="_blank">
                      <i class="icon-base ri ri-eye-line me-2"></i>Preview Card
                    </a>
                    <a href="{{ route('members.download-card', $member) }}" class="btn btn-outline-success btn-sm">
                      <i class="icon-base ri ri-download-line me-2"></i>Download Card
                    </a>
                    <form action="{{ route('members.regenerate-card', $member) }}" method="POST" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-outline-warning btn-sm" onclick="return confirm('Are you sure you want to regenerate this card? This will create a new card and QR code.')">
                        <i class="icon-base ri ri-refresh-line me-2"></i>Regenerate
                      </button>
                    </form>
                  </div>
                  <div class="alert alert-success">
                    <i class="icon-base ri ri-check-line me-2"></i>
                    <strong>Card Status:</strong> Generated
                  </div>
                @else
                  <div class="d-flex gap-2 mb-3">
                    <a href="{{ route('members.cards.generate', $member) }}" class="btn btn-primary btn-sm">
                      <i class="icon-base ri ri-add-line me-2"></i>Generate Card
                    </a>
                  </div>
                  <div class="alert alert-warning">
                    <i class="icon-base ri ri-error-warning-line me-2"></i>
                    <strong>Card Status:</strong> Not Generated
                  </div>
                @endif
              </div>
              
              <div class="col-md-6">
                <h6>QR Code</h6>
                @if($member->hasQRCode())
                  <div class="d-flex gap-2 mb-3">
                    <img src="{{ $member->getQRCodeUrlAttribute() }}" alt="QR Code" class="img-fluid" style="max-width: 100px;">
                    <div class="d-flex flex-column gap-1">
                      <form action="{{ route('members.generate-qr-code', $member) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-warning btn-sm" onclick="return confirm('Are you sure you want to regenerate the QR code?')">
                          <i class="icon-base ri ri-refresh-line me-2"></i>Regenerate QR
                        </button>
                      </form>
                      <a href="{{ $member->getQRCodeUrlAttribute() }}" class="btn btn-outline-info btn-sm" target="_blank">
                        <i class="icon-base ri ri-external-link-line me-2"></i>View Full Size
                      </a>
                    </div>
                  </div>
                  <div class="alert alert-success">
                    <i class="icon-base ri ri-check-line me-2"></i>
                    <strong>QR Status:</strong> Generated
                  </div>
                @else
                  <div class="d-flex gap-2 mb-3">
                    <form action="{{ route('members.generate-qr-code', $member) }}" method="POST" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-primary btn-sm">
                        <i class="icon-base ri ri-qr-code-line me-2"></i>Generate QR Code
                      </button>
                    </form>
                  </div>
                  <div class="alert alert-warning">
                    <i class="icon-base ri ri-error-warning-line me-2"></i>
                    <strong>QR Status:</strong> Not Generated
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>
      @endif
    </div>

    <!-- Status & Timeline -->
    <div class="col-lg-4">
      <!-- Current Status -->
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">
            <i class="icon-base ri ri-information-line me-2"></i>
            Current Status
          </h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label fw-bold">Approval Status</label>
            <div class="mb-2">{!! $member->getApprovalStatusBadgeAttribute() !!}</div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Payment Status</label>
            <div class="mb-2">{!! $member->getPaymentStatusBadgeAttribute() !!}</div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Card Status</label>
            <div class="mb-2">{!! $member->getCardIssuanceStatusBadgeAttribute() !!}</div>
          </div>
        </div>
      </div>

      <!-- Workflow Timeline -->
      <div class="card mt-3">
        <div class="card-header">
          <h5 class="card-title mb-0">
            <i class="icon-base ri ri-time-line me-2"></i>
            Workflow Timeline
          </h5>
        </div>
        <div class="card-body">
          <div class="timeline">
            <div class="timeline-item">
              <div class="timeline-marker bg-success">
                <i class="icon-base ri ri-check-line"></i>
              </div>
              <div class="timeline-content">
                <h6 class="mb-1">Member Created</h6>
                <p class="text-muted mb-0">{{ $member->created_at->format('M d, Y g:i A') }}</p>
              </div>
            </div>

            @if($member->approved_at)
              <div class="timeline-item">
                <div class="timeline-marker bg-primary">
                  <i class="icon-base ri ri-user-check-line"></i>
                </div>
                <div class="timeline-content">
                  <h6 class="mb-1">Member Approved</h6>
                  <p class="text-muted mb-0">{{ $member->approved_at->format('M d, Y g:i A') }}</p>
                  @if($member->approvedBy)
                    <small class="text-muted">by {{ $member->approvedBy->name }}</small>
                  @endif
                </div>
              </div>
            @endif

            @if($member->payment_verified_at)
              <div class="timeline-item">
                <div class="timeline-marker bg-info">
                  <i class="icon-base ri ri-money-dollar-circle-line"></i>
                </div>
                <div class="timeline-content">
                  <h6 class="mb-1">Payment Verified</h6>
                  <p class="text-muted mb-0">{{ $member->payment_verified_at->format('M d, Y g:i A') }}</p>
                  @if($member->paymentVerifiedBy)
                    <small class="text-muted">by {{ $member->paymentVerifiedBy->name }}</small>
                  @endif
                </div>
              </div>
            @endif

            @if($member->card_approved_at)
              <div class="timeline-item">
                <div class="timeline-marker bg-success">
                  <i class="icon-base ri ri-credit-card-line"></i>
                </div>
                <div class="timeline-content">
                  <h6 class="mb-1">Card Approved</h6>
                  <p class="text-muted mb-0">{{ $member->card_approved_at->format('M d, Y g:i A') }}</p>
                  @if($member->cardApprovedBy)
                    <small class="text-muted">by {{ $member->cardApprovedBy->name }}</small>
                  @endif
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Payment Proof Modal -->
<div class="modal fade" id="paymentProofModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Payment Proof</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <img id="paymentProofImage" src="" alt="Payment Proof" class="img-fluid">
      </div>
    </div>
  </div>
</div>

<!-- Timeline CSS -->
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
  width: 30px;
  height: 30px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 14px;
}

.timeline-content {
  padding-left: 10px;
}

.timeline-content h6 {
  margin-bottom: 5px;
  font-size: 14px;
}
</style>

<!-- JavaScript -->
<script>
function viewPaymentProof(imageUrl) {
  document.getElementById('paymentProofImage').src = imageUrl;
  new bootstrap.Modal(document.getElementById('paymentProofModal')).show();
}
</script>
@endsection
