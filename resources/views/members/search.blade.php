@extends('layouts.app')

@section('title', 'Search Members')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Search Members</h4>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-8">
            <div class="input-group">
              <input type="text" class="form-control" id="memberSearch" placeholder="Search by name, email, phone, or membership ID...">
              <button class="btn btn-primary" type="button" onclick="searchMember()">
                <i class="icon-base ri ri-search-line"></i>
                Search
              </button>
            </div>
          </div>
          <div class="col-md-4">
            <button class="btn btn-success" onclick="markMemberPresent()">
              <i class="icon-base ri ri-user-check-line me-2"></i>
              Mark Present
            </button>
          </div>
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
        <h5 class="modal-title">Member Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <!-- Member Info -->
          <div class="col-md-4">
            <div class="card">
              <div class="card-body text-center">
                <div class="avatar avatar-xl mb-3">
                  <img src="{{ asset('assets/img/avatars/1.png') }}" alt="Member Avatar" class="rounded-circle" />
                </div>
                <h5 class="card-title" id="memberName">John Doe</h5>
                <p class="text-muted" id="memberEmail">john@example.com</p>
                <span class="badge bg-label-primary" id="memberId">MS001</span>
                <div class="mt-3">
                  <button class="btn btn-success btn-sm" onclick="markPresent()">
                    <i class="icon-base ri ri-user-check-line me-1"></i>
                    Mark Present
                  </button>
                  <button class="btn btn-warning btn-sm" onclick="calculateDiscount()">
                    <i class="icon-base ri ri-percent-line me-1"></i>
                    Calculate Discount
                  </button>
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
                    <h4 class="text-primary" id="totalVisits">12</h4>
                    <small class="text-muted">Total Visits</small>
                  </div>
                  <div class="col-6">
                    <h4 class="text-success" id="totalSpent">TZS 450,000</h4>
                    <small class="text-muted">Total Spent</small>
                  </div>
                </div>
                <hr>
                <div class="row text-center">
                  <div class="col-6">
                    <h4 class="text-info" id="discountRate">10%</h4>
                    <small class="text-muted">Current Discount</small>
                  </div>
                  <div class="col-6">
                    <h4 class="text-warning" id="lastVisit">2 days ago</h4>
                    <small class="text-muted">Last Visit</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Spending History -->
          <div class="col-md-8">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">Spending History</h6>
                <button class="btn btn-primary btn-sm" onclick="recordNewVisit()">
                  <i class="icon-base ri ri-restaurant-line me-1"></i>
                  Record Visit
                </button>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>Date</th>
                        <th>Bill Amount</th>
                        <th>Discount</th>
                        <th>Final Amount</th>
                        <th>Receipt</th>
                      </tr>
                    </thead>
                    <tbody id="spendingHistory">
                      <tr>
                        <td>Today, 2:30 PM</td>
                        <td>TZS 45,000</td>
                        <td><span class="text-success">TZS 4,500 (10%)</span></td>
                        <td><strong>TZS 40,500</strong></td>
                        <td>
                          <button class="btn btn-sm btn-outline-primary" onclick="viewReceipt('receipt1.jpg')">
                            <i class="icon-base ri ri-eye-line"></i>
                          </button>
                        </td>
                      </tr>
                      <tr>
                        <td>Yesterday, 7:15 PM</td>
                        <td>TZS 32,000</td>
                        <td><span class="text-success">TZS 3,200 (10%)</span></td>
                        <td><strong>TZS 28,800</strong></td>
                        <td>
                          <button class="btn btn-sm btn-outline-primary" onclick="viewReceipt('receipt2.jpg')">
                            <i class="icon-base ri ri-eye-line"></i>
                          </button>
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

<!-- Record Visit Modal -->
<div class="modal fade" id="recordVisitModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Record New Visit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="visitForm">
          <div class="mb-3">
            <label for="visitBillAmount" class="form-label">Bill Amount</label>
            <div class="input-group">
              <span class="input-group-text">TZS</span>
              <input type="number" class="form-control" id="visitBillAmount" step="100" placeholder="0" required>
            </div>
          </div>
          <div class="mb-3">
            <label for="visitDiscount" class="form-label">Discount Applied</label>
            <div class="input-group">
              <span class="input-group-text">TZS</span>
              <input type="number" class="form-control" id="visitDiscount" step="100" placeholder="0" readonly>
            </div>
          </div>
          <div class="mb-3">
            <label for="visitFinalAmount" class="form-label">Final Amount</label>
            <div class="input-group">
              <span class="input-group-text">TZS</span>
              <input type="number" class="form-control" id="visitFinalAmount" step="100" placeholder="0" readonly>
            </div>
          </div>
          <div class="mb-3">
            <label for="receiptUpload" class="form-label">Upload Receipt</label>
            <input type="file" class="form-control" id="receiptUpload" accept="image/*,.pdf">
            <small class="text-muted">Upload receipt image or PDF</small>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="saveVisit()">Save Visit</button>
      </div>
    </div>
  </div>
</div>

<!-- Receipt Viewer Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Receipt</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <img id="receiptImage" src="" alt="Receipt" class="img-fluid" style="max-height: 500px;">
      </div>
    </div>
  </div>
</div>

<script>
function searchMember() {
  const searchTerm = document.getElementById('memberSearch').value;
  if (searchTerm.trim() === '') {
    alert('Please enter a search term');
    return;
  }
  
  // Simulate search - in real app, this would be an AJAX call
  const memberDetailsModal = new bootstrap.Modal(document.getElementById('memberDetailsModal'));
  memberDetailsModal.show();
}

function markMemberPresent() {
  const searchTerm = document.getElementById('memberSearch').value;
  if (searchTerm.trim() === '') {
    alert('Please search for a member first');
    return;
  }
  
  // Show success message
  alert('Member marked as present! They are now eligible for discounts.');
}

function markPresent() {
  alert('Member marked as present! They are now eligible for discounts.');
}

function calculateDiscount() {
  // This would calculate discount based on member's visit count
  alert('Discount calculated: 10% off (based on 12 visits)');
}

function recordNewVisit() {
  const recordVisitModal = new bootstrap.Modal(document.getElementById('recordVisitModal'));
  recordVisitModal.show();
}

function saveVisit() {
  const billAmount = document.getElementById('visitBillAmount').value;
  const discount = document.getElementById('visitDiscount').value;
  const finalAmount = document.getElementById('visitFinalAmount').value;
  const receipt = document.getElementById('receiptUpload').files[0];
  
  if (!billAmount || !discount || !finalAmount) {
    alert('Please fill in all required fields');
    return;
  }
  
  // Simulate saving visit
  alert('Visit recorded successfully! Receipt uploaded.');
  
  // Close modal
  const recordVisitModal = bootstrap.Modal.getInstance(document.getElementById('recordVisitModal'));
  recordVisitModal.hide();
}

function viewReceipt(receiptFile) {
  document.getElementById('receiptImage').src = `{{ asset('assets/img/receipts/') }}/${receiptFile}`;
  const receiptModal = new bootstrap.Modal(document.getElementById('receiptModal'));
  receiptModal.show();
}
</script>
@endsection 