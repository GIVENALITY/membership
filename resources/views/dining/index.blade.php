@extends('layouts.app')

@section('title', 'Dining History')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Dining History</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#recordVisitModal">
          <i class="icon-base ri ri-restaurant-line me-2"></i>
          Record Visit
        </button>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Member</th>
                <th>Membership ID</th>
                <th>Visit Date</th>
                <th>Bill Amount</th>
                <th>Discount Applied</th>
                <th>Final Amount</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-3">
                      <img src="{{ asset('assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle" />
                    </div>
                    <div>
                      <h6 class="mb-0">John Doe</h6>
                      <small class="text-muted">MS001</small>
                    </div>
                  </div>
                </td>
                <td><span class="badge bg-label-primary">MS001</span></td>
                <td>Today, 2:30 PM</td>
                <td>$45.00</td>
                <td><span class="text-success">$4.50 (10%)</span></td>
                <td><strong>$40.50</strong></td>
                <td>
                  <button class="btn btn-sm btn-outline-primary">
                    <i class="icon-base ri ri-eye-line"></i>
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
                      <small class="text-muted">MS002</small>
                    </div>
                  </div>
                </td>
                <td><span class="badge bg-label-primary">MS002</span></td>
                <td>Yesterday, 7:15 PM</td>
                <td>$32.00</td>
                <td><span class="text-success">$3.20 (10%)</span></td>
                <td><strong>$28.80</strong></td>
                <td>
                  <button class="btn btn-sm btn-outline-primary">
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

<!-- Record Visit Modal -->
<div class="modal fade" id="recordVisitModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Record New Visit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="mb-3">
            <label for="memberSelect" class="form-label">Select Member</label>
            <select class="form-select" id="memberSelect" required>
              <option value="">Choose member...</option>
              <option value="MS001">MS001 - John Doe</option>
              <option value="MS002">MS002 - Jane Smith</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="billAmount" class="form-label">Bill Amount</label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input type="number" class="form-control" id="billAmount" step="0.01" placeholder="0.00" required>
            </div>
          </div>
          <div class="mb-3">
            <label for="discountAmount" class="form-label">Discount Applied</label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input type="number" class="form-control" id="discountAmount" step="0.01" placeholder="0.00" readonly>
            </div>
            <small class="text-muted">Auto-calculated based on member's discount rate</small>
          </div>
          <div class="mb-3">
            <label for="finalAmount" class="form-label">Final Amount</label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input type="number" class="form-control" id="finalAmount" step="0.01" placeholder="0.00" readonly>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary">Record Visit</button>
      </div>
    </div>
  </div>
</div>
@endsection 