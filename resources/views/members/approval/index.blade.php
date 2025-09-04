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

<!-- Search and Filter Section -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <form method="GET" action="{{ route('members.approval.index') }}" class="row g-3">
          <div class="col-md-4">
            <label for="search" class="form-label">Search Members</label>
            <input type="text" class="form-control" id="search" name="search" 
                   placeholder="Search by name, email, phone, or membership ID" 
                   value="{{ $search ?? '' }}">
          </div>
          <div class="col-md-3">
            <label for="status" class="form-label">Filter by Status</label>
            <select class="form-select" id="status" name="status">
              <option value="all" {{ ($status ?? 'all') === 'all' ? 'selected' : '' }}>All Statuses</option>
              <option value="pending" {{ ($status ?? '') === 'pending' ? 'selected' : '' }}>Pending Approval</option>
              <option value="approved" {{ ($status ?? '') === 'approved' ? 'selected' : '' }}>Payment Verification</option>
              <option value="payment_verified" {{ ($status ?? '') === 'payment_verified' ? 'selected' : '' }}>Card Approval</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">&nbsp;</label>
            <div class="d-grid">
              <button type="submit" class="btn btn-primary">
                <i class="icon-base ri ri-search-line me-2"></i>Search
              </button>
            </div>
          </div>
          <div class="col-md-2">
            <label class="form-label">&nbsp;</label>
            <div class="d-grid">
              <a href="{{ route('members.approval.index') }}" class="btn btn-outline-secondary">
                <i class="icon-base ri ri-refresh-line me-2"></i>Clear
              </a>
            </div>
          </div>
        </form>
        
        @if($search || ($status && $status !== 'all'))
          <div class="mt-3">
            <div class="alert alert-info">
              <i class="icon-base ri ri-information-line me-2"></i>
              <strong>Active Filters:</strong>
              @if($search)
                <span class="badge bg-primary me-2">Search: "{{ $search }}"</span>
              @endif
              @if($status && $status !== 'all')
                <span class="badge bg-info me-2">Status: {{ ucfirst(str_replace('_', ' ', $status)) }}</span>
              @endif
              <a href="{{ route('members.approval.index') }}" class="btn btn-sm btn-outline-secondary ms-2">Clear All</a>
            </div>
          </div>
        @endif
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
          {{ $pendingMembers->appends(request()->query())->links() }}
        @else
          <div class="text-center py-4">
            @if($search || ($status && $status !== 'all'))
              <i class="icon-base ri ri-search-line text-muted" style="font-size: 3rem;"></i>
              <h5 class="mt-3">No Results Found</h5>
              <p class="text-muted">Try adjusting your search criteria or filters</p>
            @else
              <i class="icon-base ri ri-check-double-line text-success" style="font-size: 3rem;"></i>
              <h5 class="mt-3">No Pending Approvals</h5>
              <p class="text-muted">All members have been processed</p>
            @endif
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
          {{ $approvedMembers->appends(request()->query())->links() }}
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
          {{ $paymentVerifiedMembers->appends(request()->query())->links() }}
        @else
          <div class="text-center py-4">
            @if($search || ($status && $status !== 'all'))
              <i class="icon-base ri ri-search-line text-muted" style="font-size: 3rem;"></i>
              <h5 class="mt-3">No Results Found</h5>
              <p class="text-muted">Try adjusting your search criteria or filters</p>
            @else
              <i class="icon-base ri ri-credit-card-line text-muted" style="font-size: 3rem;"></i>
              <h5 class="mt-3">No Card Issuance Approval Required</h5>
              <p class="text-muted">All payment verified members have been processed</p>
            @endif
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Quick Search JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when status changes
    const statusSelect = document.getElementById('status');
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            this.form.submit();
        });
    }
    
    // Add enter key support for search
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.form.submit();
            }
        });
    }
    
    // Highlight search terms in results
    const searchTerm = '{{ $search ?? "" }}';
    if (searchTerm) {
        const tables = document.querySelectorAll('table tbody');
        tables.forEach(table => {
            const rows = table.querySelectorAll('tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm.toLowerCase())) {
                    row.style.backgroundColor = '#fff3cd';
                }
            });
        });
    }
});
</script>

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
