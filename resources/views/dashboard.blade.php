@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
  <div class="col-lg-12 mb-4 order-0">
    <div class="card">
      <div class="d-flex align-items-end row">
        <div class="col-sm-7">
          <div class="card-body">
            <h5 class="card-title text-primary">Welcome to Membership MS! 🍽️</h5>
            <p class="mb-4">Manage your restaurant's premium loyalty membership program. Track dining visits, calculate discounts, and grow your member base.</p>

            <a href="{{ route('members.create') }}" class="btn btn-sm btn-outline-primary">Add New Member</a>
          </div>
        </div>
        <div class="col-sm-5 text-center text-sm-left">
          <div class="card-body pb-0 px-0 px-md-4">
            <img
              src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}"
              height="140"
              alt="Restaurant Management"
              data-app-dark-img="illustrations/man-with-laptop-dark.png"
              data-app-light-img="illustrations/man-with-laptop-light.png" />
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-12 col-md-4 order-1">
    <div class="row">
      <div class="col-lg-3 col-md-6 col-6 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between">
              <div class="avatar flex-shrink-0">
                <i class="icon-base ri ri-user-star-line icon-lg text-primary"></i>
              </div>
            </div>
            <span class="fw-semibold d-block mb-1">Total Members</span>
            <h3 class="card-title mb-2">1,234</h3>
            <small class="text-success fw-semibold">
              <i class="icon-base ri ri-arrow-up-line"></i>
              +12.5%
            </small>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-6 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between">
              <div class="avatar flex-shrink-0">
                <i class="icon-base ri ri-restaurant-line icon-lg text-warning"></i>
              </div>
            </div>
            <span class="fw-semibold d-block mb-1">Today's Visits</span>
            <h3 class="card-title mb-2">89</h3>
            <small class="text-success fw-semibold">
              <i class="icon-base ri ri-arrow-up-line"></i>
              +8.2%
            </small>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-6 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between">
              <div class="avatar flex-shrink-0">
                <i class="icon-base ri ri-percent-line icon-lg text-success"></i>
              </div>
            </div>
            <span class="fw-semibold d-block mb-1">Discounts Given</span>
            <h3 class="card-title mb-2">TZS 2,456,000</h3>
            <small class="text-info fw-semibold">
              <i class="icon-base ri ri-information-line"></i>
              This Month
            </small>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-6 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between">
              <div class="avatar flex-shrink-0">
                <i class="icon-base ri ri-bar-chart-line icon-lg text-info"></i>
              </div>
            </div>
            <span class="fw-semibold d-block mb-1">Avg. Visits/Member</span>
            <h3 class="card-title mb-2">4.2</h3>
            <small class="text-success fw-semibold">
              <i class="icon-base ri ri-arrow-up-line"></i>
              +15.3%
            </small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Recent Activity -->
<div class="row">
  <div class="col-lg-8 mb-4">
    <div class="card">
      <div class="card-header d-flex justify-content-between">
        <h5 class="card-title mb-0">Recent Member Activity</h5>
        <a href="{{ route('members.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-borderless">
            <thead>
              <tr>
                <th>Member</th>
                <th>Membership ID</th>
                <th>Last Visit</th>
                <th>Total Visits</th>
                <th>Status</th>
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
                      <small class="text-muted">john@example.com</small>
                    </div>
                  </div>
                </td>
                <td><span class="badge bg-label-primary">MS001</span></td>
                <td>2 hours ago</td>
                <td>12 visits</td>
                <td><span class="badge bg-label-success">Active</span></td>
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
                <td>1 day ago</td>
                <td>8 visits</td>
                <td><span class="badge bg-label-success">Active</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-lg-4 mb-4">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Quick Actions</h5>
      </div>
      <div class="card-body">
        <div class="d-grid gap-2">
                           <a href="{{ route('members.search') }}" class="btn btn-primary">
                   <i class="icon-base ri ri-search-line me-2"></i>
                   Search Members
                 </a>
                 <a href="{{ route('cashier.index') }}" class="btn btn-outline-warning">
                   <i class="icon-base ri ri-bank-card-line me-2"></i>
                   Cashier Dashboard
                 </a>
                 <a href="{{ route('members.create') }}" class="btn btn-outline-primary">
                   <i class="icon-base ri ri-user-add-line me-2"></i>
                   Add New Member
                 </a>
                 <a href="{{ route('dining.index') }}" class="btn btn-outline-success">
                   <i class="icon-base ri ri-restaurant-line me-2"></i>
                   Record Visit
                 </a>
                 <a href="{{ route('notifications.index') }}" class="btn btn-outline-info">
                   <i class="icon-base ri ri-mail-line me-2"></i>
                   Email Notifications
                 </a>
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
          Birthday Alerts
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
@endsection

@push('page-js')
<script src="{{ asset('assets/js/dashboards-analytics.js') }}"></script>
@endpush 