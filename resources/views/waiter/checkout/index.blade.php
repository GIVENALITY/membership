@extends('layouts.app')

@section('title', 'Waiter Checkout Dashboard')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">
          <i class="icon-base ri ri-restaurant-line me-2"></i>
          Waiter Checkout Dashboard
        </h4>
        <p class="text-muted mb-0">Manage your assigned members and process checkouts</p>
      </div>
    </div>
  </div>
</div>

<!-- Assign New Member -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">
          <i class="icon-base ri ri-user-add-line me-2"></i>
          Assign New Member
        </h5>
        <small class="text-muted">Search and assign a member to your service</small>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('waiter.checkout.assign-member') }}">
          @csrf
          <div class="row">
            <div class="col-md-4">
              <div class="input-group">
                <input type="text" class="form-control" id="memberSearch" placeholder="Search by ID, name, or phone...">
                <button class="btn btn-outline-secondary" type="button" onclick="searchMembers()">
                  <i class="icon-base ri ri-search-line"></i>
                </button>
              </div>
            </div>
            <div class="col-md-3">
              <input type="number" class="form-control" name="number_of_people" placeholder="Number of people" min="1" max="50" required>
            </div>
            <div class="col-md-3">
              <textarea class="form-control" name="waiter_notes" placeholder="Service notes..." rows="1"></textarea>
            </div>
            <div class="col-md-2">
              <button type="submit" class="btn btn-primary w-100">
                <i class="icon-base ri ri-user-add-line me-1"></i>Assign
              </button>
            </div>
          </div>
          
          <!-- Search Results -->
          <div id="searchResults" class="mt-3" style="display: none;">
            <div class="list-group" id="memberResults"></div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Active Visits -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">
          <i class="icon-base ri ri-time-line me-2"></i>
          Active Visits
        </h5>
        <small class="text-muted">Members currently under your service</small>
      </div>
      <div class="card-body">
        @if($activeVisits->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Member</th>
                  <th>Membership ID</th>
                  <th>People</th>
                  <th>Assigned Time</th>
                  <th>Notes</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($activeVisits as $visit)
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-3">
                          <div class="bg-label-primary rounded-circle d-flex align-items-center justify-content-center">
                            <i class="icon-base ri ri-user-line"></i>
                          </div>
                        </div>
                        <div>
                          <h6 class="mb-0">{{ $visit->member->full_name }}</h6>
                          <small class="text-muted">{{ $visit->member->phone }}</small>
                        </div>
                      </div>
                    </td>
                    <td><span class="badge bg-label-primary">{{ $visit->member->membership_id }}</span></td>
                    <td><span class="badge bg-label-info">{{ $visit->number_of_people }}</span></td>
                    <td>{{ $visit->created_at->diffForHumans() }}</td>
                    <td>{{ $visit->waiter_notes ?? '-' }}</td>
                    <td>
                      <div class="d-flex gap-2">
                        <a href="{{ route('waiter.checkout.show', $visit) }}" class="btn btn-sm btn-success">
                          <i class="icon-base ri ri-bill-line me-1"></i>Checkout
                        </a>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="text-center py-4">
            <i class="icon-base ri ri-time-line text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3">No Active Visits</h5>
            <p class="text-muted">Assign a member to start providing service</p>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Recent Checkouts -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">
          <i class="icon-base ri ri-check-double-line me-2"></i>
          Recent Checkouts
        </h5>
        <small class="text-muted">Your recently completed checkouts</small>
      </div>
      <div class="card-body">
        @if($completedCheckouts->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Member</th>
                  <th>Membership ID</th>
                  <th>Amount</th>
                  <th>Payment Method</th>
                  <th>Checkout Time</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($completedCheckouts as $visit)
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-3">
                          <div class="bg-label-success rounded-circle d-flex align-items-center justify-content-center">
                            <i class="icon-base ri ri-user-line"></i>
                          </div>
                        </div>
                        <div>
                          <h6 class="mb-0">{{ $visit->member->full_name }}</h6>
                          <small class="text-muted">{{ $visit->member->phone }}</small>
                        </div>
                      </div>
                    </td>
                    <td><span class="badge bg-label-primary">{{ $visit->member->membership_id }}</span></td>
                    <td>
                      <strong>TZS {{ number_format($visit->final_amount, 0) }}</strong>
                      @if($visit->discount_amount > 0)
                        <br><small class="text-success">-{{ number_format($visit->discount_amount, 0) }} discount</small>
                      @endif
                    </td>
                    <td>
                      <span class="badge bg-label-info">{{ ucfirst($visit->payment_method ?? 'N/A') }}</span>
                    </td>
                    <td>{{ $visit->checked_out_at->diffForHumans() }}</td>
                    <td>
                      <div class="d-flex gap-2">
                        @if($visit->receipt_path)
                          <a href="{{ route('waiter.checkout.download-receipt', $visit) }}" class="btn btn-sm btn-outline-primary">
                            <i class="icon-base ri ri-download-line me-1"></i>Receipt
                          </a>
                        @endif
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="text-center py-4">
            <i class="icon-base ri ri-check-double-line text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3">No Recent Checkouts</h5>
            <p class="text-muted">Complete your first checkout to see it here</p>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Summary Stats -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">
          <i class="icon-base ri ri-bar-chart-line me-2"></i>
          Today's Summary
        </h5>
      </div>
      <div class="card-body">
        <div class="row text-center">
          <div class="col-md-3">
            <div class="border rounded p-3">
              <h3 class="text-primary mb-1">{{ $activeVisits->count() }}</h3>
              <p class="mb-0 text-muted">Active Visits</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="border rounded p-3">
              <h3 class="text-success mb-1">{{ $completedCheckouts->count() }}</h3>
              <p class="mb-0 text-muted">Completed Today</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="border rounded p-3">
              <h3 class="text-info mb-1">{{ $activeVisits->sum('number_of_people') }}</h3>
              <p class="mb-0 text-muted">Total Guests</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="border rounded p-3">
              <h3 class="text-warning mb-1">{{ $completedCheckouts->sum('final_amount') > 0 ? 'TZS ' . number_format($completedCheckouts->sum('final_amount'), 0) : 'TZS 0' }}</h3>
              <p class="mb-0 text-muted">Total Revenue</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
let selectedMemberId = null;

function searchMembers() {
  const searchTerm = document.getElementById('memberSearch').value;
  if (searchTerm.trim() === '') {
    alert('Please enter a search term');
    return;
  }

  fetch('{{ route("waiter.checkout.search-members") }}?search=' + encodeURIComponent(searchTerm))
    .then(response => response.json())
    .then(data => {
      const resultsDiv = document.getElementById('memberResults');
      const searchResultsDiv = document.getElementById('searchResults');
      
      if (data.success && data.members.length > 0) {
        resultsDiv.innerHTML = data.members.map(member => `
          <div class="list-group-item list-group-item-action member-result" 
               onclick="selectMember(${member.id})" style="cursor: pointer;">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h6 class="mb-1">${member.name}</h6>
                <small class="text-muted">ID: ${member.membership_id} | Phone: ${member.phone || 'N/A'}</small>
                <br><small class="text-info">Type: ${member.membership_type} | Discount: ${member.current_discount_rate}%</small>
              </div>
              <i class="icon-base ri ri-arrow-right-line"></i>
            </div>
          </div>
        `).join('');
        searchResultsDiv.style.display = 'block';
      } else {
        resultsDiv.innerHTML = '<div class="text-center text-muted py-3">No members found</div>';
        searchResultsDiv.style.display = 'block';
      }
    })
    .catch(error => {
      console.error('Error searching members:', error);
    });
}

function selectMember(memberId) {
  selectedMemberId = memberId;
  
  // Add hidden input for member_id
  const form = document.querySelector('form');
  let memberIdInput = form.querySelector('input[name="member_id"]');
  
  if (!memberIdInput) {
    memberIdInput = document.createElement('input');
    memberIdInput.type = 'hidden';
    memberIdInput.name = 'member_id';
    form.appendChild(memberIdInput);
  }
  
  memberIdInput.value = memberId;
  
  // Hide search results
  document.getElementById('searchResults').style.display = 'none';
  
  // Update search input to show selected member
  const memberResult = document.querySelector(`[onclick="selectMember(${memberId})"]`);
  const memberName = memberResult.querySelector('h6').textContent;
  document.getElementById('memberSearch').value = memberName;
  
  // Enable form submission
  form.querySelector('button[type="submit"]').disabled = false;
}

// Initialize form
document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('form');
  form.querySelector('button[type="submit"]').disabled = true;
});
</script>

<style>
.member-result:hover {
  background-color: #f8f9fa;
  border-left: 3px solid #007bff;
}
</style>
@endsection
