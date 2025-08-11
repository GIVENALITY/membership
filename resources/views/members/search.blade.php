@extends('layouts.app')

@section('title', 'Search Members - ' . (Auth::user()->hotel->name ?? 'Membership MS'))

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">
          <i class="icon-base ri ri-search-line me-2"></i>
          Search Members
        </h4>
        <p class="text-muted mb-0">Search for members by name, email, phone, or membership ID</p>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-8">
            <div class="input-group">
              <input type="text" class="form-control" id="memberSearch" 
                     placeholder="Search by name, email, phone, or membership ID..." 
                     onkeyup="if(event.key === 'Enter') searchMember()">
              <button class="btn btn-primary" type="button" onclick="searchMember()">
                <i class="icon-base ri ri-search-line"></i>
                Search
              </button>
            </div>
          </div>
          <div class="col-md-4">
            <a href="{{ route('members.create') }}" class="btn btn-success">
              <i class="icon-base ri ri-user-add-line me-2"></i>
              Add New Member
            </a>
          </div>
        </div>

        <!-- Search Results -->
        <div id="searchResults" class="mt-4" style="display: none;">
          <h6 class="mb-3">Search Results</h6>
          <div id="resultsList"></div>
        </div>

        <!-- Loading Indicator -->
        <div id="loadingIndicator" class="text-center mt-4" style="display: none;">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="mt-2 text-muted">Searching members...</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Member Details Modal -->
<div class="modal fade" id="memberDetailsModal" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="icon-base ri ri-user-line me-2"></i>
          Member Details
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <!-- Member Info -->
          <div class="col-md-4">
            <div class="card">
              <div class="card-body text-center">
                <div class="avatar avatar-xl mb-3">
                  <span class="avatar-initial rounded-circle bg-label-primary" id="memberAvatar">
                    J
                  </span>
                </div>
                <h5 class="card-title" id="memberName">John Doe</h5>
                <p class="text-muted" id="memberEmail">john@example.com</p>
                <span class="badge bg-label-primary" id="memberId">MS001</span>
                <div class="mt-3">
                  <a href="#" class="btn btn-success btn-sm" id="markPresentBtn">
                    <i class="icon-base ri ri-user-check-line me-1"></i>
                    Mark Present
                  </a>
                  <a href="#" class="btn btn-warning btn-sm" id="calculateDiscountBtn">
                    <i class="icon-base ri ri-percent-line me-1"></i>
                    Calculate Discount
                  </a>
                </div>
              </div>
            </div>
            
            <!-- Member Stats -->
            <div class="card mt-3">
              <div class="card-header">
                <h6 class="card-title mb-0">Member Statistics</h6>
              </div>
              <div class="card-body">
                <div class="row text-center">
                  <div class="col-6">
                    <h4 class="text-primary" id="totalVisits">0</h4>
                    <small class="text-muted">Total Visits</small>
                  </div>
                  <div class="col-6">
                    <h4 class="text-success" id="totalSpent">TZS 0</h4>
                    <small class="text-muted">Total Spent</small>
                  </div>
                </div>
                <hr>
                <div class="row text-center">
                  <div class="col-6">
                    <h4 class="text-info" id="discountRate">0%</h4>
                    <small class="text-muted">Current Discount</small>
                  </div>
                  <div class="col-6">
                    <h4 class="text-warning" id="lastVisit">Never</h4>
                    <small class="text-muted">Last Visit</small>
                  </div>
                </div>
              </div>
            </div>

            <!-- Member Details -->
            <div class="card mt-3">
              <div class="card-header">
                <h6 class="card-title mb-0">Member Information</h6>
              </div>
              <div class="card-body">
                <p><strong>Phone:</strong> <span id="memberPhone">-</span></p>
                <p><strong>Address:</strong> <span id="memberAddress">-</span></p>
                <p><strong>Birth Date:</strong> <span id="memberBirthDate">-</span></p>
                <p><strong>Join Date:</strong> <span id="memberJoinDate">-</span></p>
                <p><strong>Status:</strong> <span id="memberStatus">-</span></p>
                <p><strong>Membership Type:</strong> <span id="memberType">-</span></p>
              </div>
            </div>
          </div>
          
          <!-- Spending History -->
          <div class="col-md-8">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">Recent Visits</h6>
                <div class="d-flex gap-2">
                  <a href="#" class="btn btn-primary btn-sm" id="viewFullHistoryBtn">
                    <i class="icon-base ri ri-history-line me-1"></i>
                    Full History
                  </a>
                  <a href="#" class="btn btn-success btn-sm" id="recordVisitBtn">
                    <i class="icon-base ri ri-restaurant-line me-1"></i>
                    Record Visit
                  </a>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>Date</th>
                        <th>People</th>
                        <th>Amount</th>
                        <th>Discount</th>
                        <th>Final</th>
                        <th>Status</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody id="spendingHistory">
                      <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                          <i class="icon-base ri ri-restaurant-line" style="font-size: 2rem;"></i>
                          <p class="mt-2">No visits recorded yet</p>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
let currentMember = null;

function searchMember() {
  const searchTerm = document.getElementById('memberSearch').value.trim();
  if (searchTerm === '') {
    alert('Please enter a search term');
    return;
  }
  
  // Show loading indicator
  document.getElementById('loadingIndicator').style.display = 'block';
  document.getElementById('searchResults').style.display = 'none';
  
  // Make AJAX call to search API
  fetch(`{{ route('members.search') }}?query=${encodeURIComponent(searchTerm)}`)
    .then(response => response.json())
    .then(data => {
      document.getElementById('loadingIndicator').style.display = 'none';
      
      if (data.length === 0) {
        document.getElementById('searchResults').style.display = 'block';
        document.getElementById('resultsList').innerHTML = `
          <div class="alert alert-info">
            <i class="icon-base ri ri-information-line me-2"></i>
            No members found matching "${searchTerm}"
          </div>
        `;
        return;
      }
      
      // Display search results
      displaySearchResults(data);
    })
    .catch(error => {
      document.getElementById('loadingIndicator').style.display = 'none';
      console.error('Error:', error);
      alert('Error searching members. Please try again.');
    });
}

function displaySearchResults(members) {
  document.getElementById('searchResults').style.display = 'block';
  
  let html = '<div class="row">';
  members.forEach(member => {
    html += `
      <div class="col-md-6 col-lg-4 mb-3">
        <div class="card member-card" onclick="showMemberDetails(${member.id})" style="cursor: pointer;">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="avatar avatar-sm me-3">
                <span class="avatar-initial rounded-circle bg-label-primary">
                  ${member.first_name.charAt(0)}
                </span>
              </div>
              <div>
                <h6 class="mb-0">${member.first_name} ${member.last_name}</h6>
                <small class="text-muted">${member.membership_id}</small>
                <br>
                <small class="text-muted">${member.phone || 'No phone'}</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
  });
  html += '</div>';
  
  document.getElementById('resultsList').innerHTML = html;
}

function showMemberDetails(memberId) {
  // Find the member from the search results
  const searchTerm = document.getElementById('memberSearch').value.trim();
  
  fetch(`{{ route('members.search') }}?query=${encodeURIComponent(searchTerm)}`)
    .then(response => response.json())
    .then(members => {
      const member = members.find(m => m.id === memberId);
      if (member) {
        currentMember = member;
        populateMemberModal(member);
        const modal = new bootstrap.Modal(document.getElementById('memberDetailsModal'));
        modal.show();
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error loading member details. Please try again.');
    });
}

function populateMemberModal(member) {
  // Basic info
  document.getElementById('memberAvatar').textContent = member.first_name.charAt(0);
  document.getElementById('memberName').textContent = `${member.first_name} ${member.last_name}`;
  document.getElementById('memberEmail').textContent = member.email;
  document.getElementById('memberId').textContent = member.membership_id;
  
  // Stats
  document.getElementById('totalVisits').textContent = member.total_visits || 0;
  document.getElementById('totalSpent').textContent = `TZS ${(member.total_spent || 0).toLocaleString()}`;
  document.getElementById('discountRate').textContent = `${member.current_discount_rate || 0}%`;
  document.getElementById('lastVisit').textContent = member.last_visit_date || 'Never';
  
  // Details
  document.getElementById('memberPhone').textContent = member.phone || '-';
  document.getElementById('memberAddress').textContent = member.address || '-';
  document.getElementById('memberBirthDate').textContent = member.birth_date || '-';
  document.getElementById('memberJoinDate').textContent = member.join_date || '-';
  document.getElementById('memberStatus').innerHTML = getStatusBadge(member.status);
  document.getElementById('memberType').textContent = member.membership_type?.name || '-';
  
  // Update links
  document.getElementById('markPresentBtn').href = `{{ route('members.show', '') }}/${member.id}`;
  document.getElementById('calculateDiscountBtn').href = `{{ route('members.show', '') }}/${member.id}`;
  document.getElementById('viewFullHistoryBtn').href = `{{ route('dining.history.member', '') }}/${member.id}`;
  document.getElementById('recordVisitBtn').href = `{{ route('dining.index') }}`;
  
  // Load recent visits
  loadRecentVisits(member.id);
}

function getStatusBadge(status) {
  const badges = {
    'active': '<span class="badge bg-label-success">Active</span>',
    'inactive': '<span class="badge bg-label-secondary">Inactive</span>',
    'suspended': '<span class="badge bg-label-danger">Suspended</span>'
  };
  return badges[status] || '<span class="badge bg-label-secondary">Unknown</span>';
}

function loadRecentVisits(memberId) {
  // This would load recent visits for the member
  // For now, we'll show a placeholder
  document.getElementById('spendingHistory').innerHTML = `
    <tr>
      <td colspan="7" class="text-center text-muted py-4">
        <i class="icon-base ri ri-restaurant-line" style="font-size: 2rem;"></i>
        <p class="mt-2">Loading recent visits...</p>
      </td>
    </tr>
  `;
}

// Add hover effect to member cards
document.addEventListener('DOMContentLoaded', function() {
  document.addEventListener('mouseover', function(e) {
    if (e.target.closest('.member-card')) {
      e.target.closest('.member-card').style.transform = 'translateY(-2px)';
      e.target.closest('.member-card').style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
    }
  });
  
  document.addEventListener('mouseout', function(e) {
    if (e.target.closest('.member-card')) {
      e.target.closest('.member-card').style.transform = 'translateY(0)';
      e.target.closest('.member-card').style.boxShadow = 'none';
    }
  });
});
</script>
@endsection 