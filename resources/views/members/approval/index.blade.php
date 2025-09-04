@extends('layouts.app')

@section('title', 'Member Approval Workflow')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">
          <i class="icon-base ri ri-user-check-line me-2"></i>
          Member Approval Workflow
        </h4>
        <p class="text-muted mb-0">Manage member approvals, payment verification, and card issuance</p>
      </div>
    </div>
  </div>
</div>

<!-- Pending Approvals -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">
          <i class="icon-base ri ri-time-line me-2"></i>
          Pending Approvals
        </h5>
        <small class="text-muted">Members awaiting initial approval</small>
      </div>
      <div class="card-body">
        @if($pendingMembers->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Member</th>
                  <th>Membership ID</th>
                  <th>Membership Type</th>
                  <th>Contact</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($pendingMembers as $member)
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-3">
                          <div class="bg-label-primary rounded-circle d-flex align-items-center justify-content-center">
                            <i class="icon-base ri ri-user-line"></i>
                          </div>
                        </div>
                        <div>
                          <h6 class="mb-0">{{ $member->full_name }}</h6>
                          <small class="text-muted">{{ $member->email }}</small>
                        </div>
                      </div>
                    </td>
                    <td><span class="badge bg-label-primary">{{ $member->membership_id }}</span></td>
                    <td>{{ optional($member->membershipType)->name ?? 'N/A' }}</td>
                    <td>{{ $member->phone }}</td>
                    <td>{{ $member->created_at->diffForHumans() }}</td>
                    <td>
                      <div class="d-flex gap-2">
                        <a href="{{ route('members.approval.show', $member) }}" class="btn btn-sm btn-outline-primary">
                          <i class="icon-base ri ri-eye-line me-1"></i>Review
                        </a>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          {{ $pendingMembers->links() }}
        @else
          <div class="text-center py-4">
            <i class="icon-base ri ri-check-double-line text-success" style="font-size: 3rem;"></i>
            <h5 class="mt-3">No Pending Approvals</h5>
            <p class="text-muted">All members have been processed</p>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Approved Members Awaiting Payment Verification -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">
          <i class="icon-base ri ri-bill-line me-2"></i>
          Payment Verification Required
        </h5>
        <small class="text-muted">Approved members awaiting payment proof verification</small>
      </div>
      <div class="card-body">
        @if($approvedMembers->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Member</th>
                  <th>Membership ID</th>
                  <th>Approved By</th>
                  <th>Approved Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($approvedMembers as $member)
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-3">
                          <div class="bg-label-success rounded-circle d-flex align-items-center justify-content-center">
                            <i class="icon-base ri ri-user-line"></i>
                          </div>
                        </div>
                        <div>
                          <h6 class="mb-0">{{ $member->full_name }}</h6>
                          <small class="text-muted">{{ $member->email }}</small>
                        </div>
                      </div>
                    </td>
                    <td><span class="badge bg-label-primary">{{ $member->membership_id }}</span></td>
                    <td>{{ optional($member->approvedBy)->name ?? 'N/A' }}</td>
                    <td>{{ $member->approved_at->diffForHumans() }}</td>
                    <td>
                      <div class="d-flex gap-2">
                        <a href="{{ route('members.approval.show', $member) }}" class="btn btn-sm btn-outline-warning">
                          <i class="icon-base ri ri-bill-line me-1"></i>Verify Payment
                        </a>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          {{ $approvedMembers->links() }}
        @else
          <div class="text-center py-4">
            <i class="icon-base ri ri-bill-line text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3">No Payment Verification Required</h5>
            <p class="text-muted">All approved members have been processed</p>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Payment Verified Members Awaiting Card Issuance Approval -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">
          <i class="icon-base ri ri-credit-card-line me-2"></i>
          Card Issuance Approval Required
        </h5>
        <small class="text-muted">Payment verified members awaiting card issuance approval</small>
      </div>
      <div class="card-body">
        @if($paymentVerifiedMembers->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Member</th>
                  <th>Membership ID</th>
                  <th>Payment Verified By</th>
                  <th>Verified Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($paymentVerifiedMembers as $member)
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-3">
                          <div class="bg-label-info rounded-circle d-flex align-items-center justify-content-center">
                            <i class="icon-base ri ri-user-line"></i>
                          </div>
                        </div>
                        <div>
                          <h6 class="mb-0">{{ $member->full_name }}</h6>
                          <small class="text-muted">{{ $member->email }}</small>
                        </div>
                      </div>
                    </td>
                    <td><span class="badge bg-label-primary">{{ $member->membership_id }}</span></td>
                    <td>{{ optional($member->paymentVerifiedBy)->name ?? 'N/A' }}</td>
                    <td>{{ $member->payment_verified_at->diffForHumans() }}</td>
                    <td>
                      <div class="d-flex gap-2">
                        <a href="{{ route('members.approval.show', $member) }}" class="btn btn-sm btn-outline-success">
                          <i class="icon-base ri ri-credit-card-line me-1"></i>Approve Card
                        </a>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          {{ $paymentVerifiedMembers->links() }}
        @else
          <div class="text-center py-4">
            <i class="icon-base ri ri-credit-card-line text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3">No Card Issuance Approval Required</h5>
            <p class="text-muted">All payment verified members have been processed</p>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Workflow Summary -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">
          <i class="icon-base ri ri-information-line me-2"></i>
          Workflow Summary
        </h5>
      </div>
      <div class="card-body">
        <div class="row text-center">
          <div class="col-md-3">
            <div class="border rounded p-3">
              <h3 class="text-warning mb-1">{{ $pendingMembers->total() }}</h3>
              <p class="mb-0 text-muted">Pending Approval</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="border rounded p-3">
              <h3 class="text-info mb-1">{{ $approvedMembers->total() }}</h3>
              <p class="mb-0 text-muted">Payment Verification</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="border rounded p-3">
              <h3 class="text-success mb-1">{{ $paymentVerifiedMembers->total() }}</h3>
              <p class="mb-0 text-muted">Card Approval</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="border rounded p-3">
              <h3 class="text-primary mb-1">{{ $pendingMembers->total() + $approvedMembers->total() + $paymentVerifiedMembers->total() }}</h3>
              <p class="mb-0 text-muted">Total Pending</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
