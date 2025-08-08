@extends('layouts.app')

@section('title', 'Discounts')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Discount Calculator</h4>
      </div>
      <div class="card-body">
        <form>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="memberId" class="form-label">Member ID</label>
              <input type="text" class="form-control" id="memberId" placeholder="Enter membership ID (e.g., MS001)">
            </div>
            <div class="col-md-6 mb-3">
              <label for="billAmount" class="form-label">Bill Amount</label>
              <div class="input-group">
                <span class="input-group-text">TZS</span>
                <input type="number" class="form-control" id="billAmount" step="100" placeholder="0">
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-4 mb-3">
              <label for="visitCount" class="form-label">Total Visits</label>
              <input type="number" class="form-control" id="visitCount" placeholder="0" readonly>
            </div>
            <div class="col-md-4 mb-3">
              <label for="discountRate" class="form-label">Discount Rate</label>
              <div class="input-group">
                <input type="number" class="form-control" id="discountRate" step="0.1" placeholder="0.0" readonly>
                <span class="input-group-text">%</span>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <label for="discountAmount" class="form-label">Discount Amount</label>
              <div class="input-group">
                <span class="input-group-text">TZS</span>
                <input type="number" class="form-control" id="discountAmount" step="100" placeholder="0" readonly>
              </div>
            </div>
          </div>
          
                      <div class="row">
              <div class="col-md-6 mb-3">
                <label for="finalAmount" class="form-label">Final Amount</label>
                <div class="input-group">
                  <span class="input-group-text">TZS</span>
                  <input type="number" class="form-control" id="finalAmount" step="100" placeholder="0" readonly>
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="savings" class="form-label">Total Savings</label>
                <div class="input-group">
                  <span class="input-group-text">TZS</span>
                  <input type="number" class="form-control" id="savings" step="100" placeholder="0" readonly>
                </div>
              </div>
            </div>
          
          <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-outline-secondary" onclick="calculateDiscount()">
              <i class="icon-base ri ri-calculator-line me-2"></i>
              Calculate Discount
            </button>
            <button type="button" class="btn btn-success">
              <i class="icon-base ri ri-check-line me-2"></i>
              Apply Discount
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="row mt-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Discount Rules</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Visits</th>
                <th>Discount Rate</th>
                <th>Description</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1-5 visits</td>
                <td><span class="badge bg-label-primary">5%</span></td>
                <td>Basic member discount</td>
              </tr>
              <tr>
                <td>6-10 visits</td>
                <td><span class="badge bg-label-success">10%</span></td>
                <td>Regular member discount</td>
              </tr>
              <tr>
                <td>11-20 visits</td>
                <td><span class="badge bg-label-warning">15%</span></td>
                <td>Frequent diner discount</td>
              </tr>
              <tr>
                <td>21+ visits</td>
                <td><span class="badge bg-label-danger">20%</span></td>
                <td>VIP member discount</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function calculateDiscount() {
  // This would be replaced with actual calculation logic
  const memberId = document.getElementById('memberId').value;
  const billAmount = parseFloat(document.getElementById('billAmount').value) || 0;
  
  if (memberId && billAmount > 0) {
    // Simulate discount calculation
    const visitCount = Math.floor(Math.random() * 25) + 1;
    let discountRate = 5;
    
    if (visitCount > 20) discountRate = 20;
    else if (visitCount > 10) discountRate = 15;
    else if (visitCount > 5) discountRate = 10;
    
    const discountAmount = (billAmount * discountRate) / 100;
    const finalAmount = billAmount - discountAmount;
    
    document.getElementById('visitCount').value = visitCount;
    document.getElementById('discountRate').value = discountRate;
    document.getElementById('discountAmount').value = Math.round(discountAmount);
    document.getElementById('finalAmount').value = Math.round(finalAmount);
    document.getElementById('savings').value = Math.round(discountAmount);
  }
}
</script>
@endsection 