@extends('layouts.app')

@section('title', 'Cashier Dashboard')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Cashier Dashboard</h4>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-8">
            <div class="input-group">
              <input type="text" class="form-control" id="memberLookup" placeholder="Enter membership ID or phone number...">
              <button class="btn btn-primary" type="button" onclick="lookupMember()">
                <i class="icon-base ri ri-search-line"></i>
                Lookup Member
              </button>
            </div>
          </div>
          <div class="col-md-4">
            <div class="alert alert-info mb-0">
              <i class="icon-base ri ri-information-line me-2"></i>
              <strong>Today's Present Members:</strong> <span id="presentCount">0</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Member Eligibility Check -->
<div class="row mt-4" id="memberEligibility" style="display: none;">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Member Information</h5>
      </div>
      <div class="card-body">
        <div class="d-flex align-items-center mb-3">
          <div class="avatar avatar-lg me-3">
            <img src="{{ asset('assets/img/avatars/1.png') }}" alt="Member Avatar" class="rounded-circle" />
          </div>
          <div>
            <h6 class="mb-0" id="memberName">John Doe</h6>
            <small class="text-muted" id="memberId">MS001</small>
          </div>
        </div>
        
        <div class="row">
          <div class="col-6">
            <small class="text-muted">Total Visits</small>
            <h6 class="text-primary" id="totalVisits">12</h6>
          </div>
          <div class="col-6">
            <small class="text-muted">Current Discount</small>
            <h6 class="text-success" id="discountRate">10%</h6>
          </div>
        </div>
        
        <div class="alert alert-success mt-3">
          <i class="icon-base ri ri-check-line me-2"></i>
          <strong>Member is present and eligible for discount!</strong>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Bill Calculation</h5>
      </div>
      <div class="card-body">
        <form id="billForm">
          <div class="mb-3">
            <label for="billAmount" class="form-label">Bill Amount</label>
            <div class="input-group">
              <span class="input-group-text">TZS</span>
              <input type="number" class="form-control" id="billAmount" step="100" placeholder="0" onchange="calculateBill()">
            </div>
          </div>
          
          <div class="mb-3">
            <label for="discountAmount" class="form-label">Discount Applied</label>
            <div class="input-group">
              <span class="input-group-text">TZS</span>
              <input type="number" class="form-control" id="discountAmount" step="100" placeholder="0" readonly>
            </div>
          </div>
          
          <div class="mb-3">
            <label for="finalAmount" class="form-label">Final Amount</label>
            <div class="input-group">
              <span class="input-group-text">TZS</span>
              <input type="number" class="form-control" id="finalAmount" step="100" placeholder="0" readonly>
            </div>
          </div>
          
          <div class="alert alert-info">
            <i class="icon-base ri ri-information-line me-2"></i>
            <small><strong>Note:</strong> Discount will be automatically calculated based on member's membership type, points, and visit history. The preview above shows an estimated rate.</small>
          </div>
                           <div class="mb-3">
                                                   @if(auth()->user()->hotel->getSetting('receipt_required', false))
                                <label for="cashierReceipt" class="form-label">Upload Receipt <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="cashierReceipt" name="receipt" accept="image/*,.pdf" required>
                                <small class="text-muted">Upload receipt image or PDF (required)</small>
                                @else
                                <label for="cashierReceipt" class="form-label">Upload Receipt (Optional)</label>
                                <input type="file" class="form-control" id="cashierReceipt" name="receipt" accept="image/*,.pdf">
                                <small class="text-muted">Upload receipt image or PDF (optional)</small>
                                @endif
                 </div>
                 <div class="d-flex justify-content-between">
                   <button type="button" class="btn btn-outline-secondary" onclick="clearBill()">
                     <i class="icon-base ri ri-refresh-line me-1"></i>
                     Clear
                   </button>
                   <button type="button" class="btn btn-success" onclick="processPayment()">
                     <i class="icon-base ri ri-bank-card-line me-1"></i>
                     Process Payment
                   </button>
                 </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Present Members List -->
<div class="row mt-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Present Members Today</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Member</th>
                <th>Membership ID</th>
                <th>Check-in Time</th>
                <th>Discount Rate</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="presentMembersList">
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-3">
                      <img src="{{ asset('assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle" />
                    </div>
                    <div>
                      <h6 class="mb-0">John Doe</h6>
                      <small class="text-muted">john@example.com</small>
                    </div>
                  </div>
                </td>
                <td><span class="badge bg-label-primary">MS001</span></td>
                <td>2:30 PM</td>
                <td><span class="badge bg-label-success">10%</span></td>
                <td><span class="badge bg-label-success">Present</span></td>
                <td>
                  <button class="btn btn-sm btn-outline-primary" onclick="selectMember('MS001')">
                    <i class="icon-base ri ri-check-line"></i>
                    Select
                  </button>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-3">
                      <img src="{{ asset('assets/img/avatars/2.png') }}" alt="Avatar" class="rounded-circle" />
                    </div>
                    <div>
                      <h6 class="mb-0">Jane Smith</h6>
                      <small class="text-muted">jane@example.com</small>
                    </div>
                  </div>
                </td>
                <td><span class="badge bg-label-primary">MS002</span></td>
                <td>1:15 PM</td>
                <td><span class="badge bg-label-warning">15%</span></td>
                <td><span class="badge bg-label-success">Present</span></td>
                <td>
                  <button class="btn btn-sm btn-outline-primary" onclick="selectMember('MS002')">
                    <i class="icon-base ri ri-check-line"></i>
                    Select
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

<!-- Birthday Alerts -->
<div class="row mt-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">
          <i class="icon-base ri ri-cake-line text-warning me-2"></i>
          Upcoming Birthdays
        </h5>
      </div>
      <div class="card-body">
        <div class="alert alert-warning">
          <i class="icon-base ri ri-cake-line me-2"></i>
          <strong>Birthday Alert:</strong> John Doe (MS001) has a birthday in 3 days! Consider offering a special birthday discount.
        </div>
        <div class="alert alert-info">
          <i class="icon-base ri ri-cake-line me-2"></i>
          <strong>Birthday Alert:</strong> Jane Smith (MS002) has a birthday in 7 days!
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function lookupMember() {
  const memberId = document.getElementById('memberLookup').value;
  if (memberId.trim() === '') {
    alert('Please enter a membership ID or phone number');
    return;
  }
  
  // Simulate member lookup
  document.getElementById('memberEligibility').style.display = 'block';
  document.getElementById('presentCount').textContent = '2';
}

function calculateBill() {
  const billAmount = parseFloat(document.getElementById('billAmount').value) || 0;
  
  // Note: Actual discount will be calculated by server based on member's membership type and points
  // This is just a preview - the server will apply the correct discount rate
  const discountRate = 10; // This is just a preview rate
  
  const discountAmount = (billAmount * discountRate) / 100;
  const finalAmount = billAmount - discountAmount;
  
  document.getElementById('discountAmount').value = Math.round(discountAmount);
  document.getElementById('finalAmount').value = Math.round(finalAmount);
}

function clearBill() {
  document.getElementById('billForm').reset();
  document.getElementById('memberEligibility').style.display = 'none';
}

       async function processPayment() {
         const finalAmount = document.getElementById('finalAmount').value;
         if (!finalAmount || finalAmount <= 0) {
           alert('Please calculate the bill first');
           return;
         }

         const formData = new FormData();
         // Ideally use selected member id; here we fall back to typed lookup value
         formData.append('member_id', document.getElementById('memberLookup').value);
         formData.append('number_of_people', 1); // Default to 1 person
         formData.append('amount_spent', document.getElementById('billAmount').value || 0);
         formData.append('discount_amount', document.getElementById('discountAmount').value || 0);
         formData.append('final_amount', finalAmount);
         formData.append('is_checked_out', true);
         formData.append('checkout_notes', 'Processed via cashier');
         const receipt = document.getElementById('cashierReceipt').files[0];
         if (receipt) formData.append('receipt', receipt);

         try {
           const response = await fetch(`{{ route('dining.process-payment') }}`, {
             method: 'POST',
             headers: { 'X-CSRF-TOKEN': `{{ csrf_token() }}` },
             body: formData,
           });
           
           const result = await response.json();
           
           if (result.success) {
             alert(result.message);
             clearBill();
           } else {
             throw new Error(result.message || 'Failed to process payment');
           }
         } catch (e) {
           alert('Error: ' + e.message);
         }
       }

function selectMember(memberId) {
  document.getElementById('memberLookup').value = memberId;
  lookupMember();
}
</script>
@endsection 