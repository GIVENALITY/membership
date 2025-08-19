@extends('layouts.app')

@section('title', 'Dashboard - ' . (Auth::user()->hotel->name ?? 'Membership MS'))

@section('content')
<div class="row">
  <div class="col-lg-12 mb-4 order-0">
    <div class="card">
      <div class="d-flex align-items-end row">
        <div class="col-sm-7">
          <div class="card-body">
            <h5 class="card-title text-primary">Welcome to {{ Auth::user()->hotel->name ?? 'Membership MS' }}! 🍽️</h5>
            <p class="mb-4">Manage your restaurant's premium loyalty membership program. Track dining visits, calculate discounts, and grow your member base.</p>

                <div class="d-flex gap-2">
      <a href="{{ route('members.create') }}" class="btn btn-sm btn-outline-primary">Add New Member</a>
      <a href="{{ route('dining.history') }}" class="btn btn-sm btn-outline-primary">
        <i class="ri ri-history-line me-1"></i>
        Dining History
      </a>
      <a href="{{ route('birthdays.today') }}" class="btn btn-sm btn-outline-warning">
        <i class="ri ri-cake-line me-1"></i>
        Today's Birthdays
      </a>
      <a href="{{ route('onboarding.index') }}" class="btn btn-sm btn-outline-primary">
        <i class="ri ri-rocket-line me-1"></i>
        System Guide
      </a>
    </div>
          </div>
        </div>
        <div class="col-sm-5 text-center text-sm-left">
          <div class="card-body pb-0 px-0 px-md-4">
            @if(Auth::check() && Auth::user()->hotel && Auth::user()->hotel->logo_path)
              <img
                src="{{ Auth::user()->hotel->logo_url }}"
                height="140"
                alt="{{ Auth::user()->hotel->name }}"
                style="max-width: 100%; object-fit: contain; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.1));" />
            @else
              <div class="d-flex align-items-center justify-content-center" style="height: 140px;">
                <div class="text-center">
                  <div class="mb-2">
                    <i class="icon-base ri ri-restaurant-line" style="font-size: 3rem; color: {{ Auth::user()->hotel->primary_color ?? '#007bff' }};"></i>
                  </div>
                  <h5 class="mb-0" style="color: {{ Auth::user()->hotel->primary_color ?? '#007bff' }};">
                                          {{ Auth::user()->hotel->name ?? 'Restaurant MS' }}
                  </h5>
                  <small class="text-muted">Restaurant Management System</small>
                </div>
              </div>
            @endif
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
            <h3 class="card-title mb-2">{{ number_format($totalMembers) }}</h3>
            <small class="text-{{ $memberGrowthPercentage >= 0 ? 'success' : 'danger' }} fw-semibold">
              <i class="icon-base ri ri-arrow-{{ $memberGrowthPercentage >= 0 ? 'up' : 'down' }}-line"></i>
              {{ $memberGrowthPercentage >= 0 ? '+' : '' }}{{ $memberGrowthPercentage }}%
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
            <h3 class="card-title mb-2">{{ number_format($todaysVisits) }}</h3>
            <small class="text-{{ $visitGrowthPercentage >= 0 ? 'success' : 'danger' }} fw-semibold">
              <i class="icon-base ri ri-arrow-{{ $visitGrowthPercentage >= 0 ? 'up' : 'down' }}-line"></i>
              {{ $visitGrowthPercentage >= 0 ? '+' : '' }}{{ $visitGrowthPercentage }}%
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
            <h3 class="card-title mb-2">TZS {{ number_format($discountsThisMonth) }}</h3>
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
            <h3 class="card-title mb-2">{{ $avgVisitsPerMember }}</h3>
            <small class="text-info fw-semibold">
              <i class="icon-base ri ri-information-line"></i>
              Overall Average
            </small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- This Month Statistics -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">
          <i class="icon-base ri ri-bar-chart-line me-2"></i>
          This Month
        </h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-3 mb-3">
            <div class="d-flex align-items-center">
              <div class="bg-label-primary rounded-circle p-2 me-3">
                <i class="icon-base ri ri-restaurant-line text-primary"></i>
              </div>
              <div>
                <small class="text-muted">Visits</small>
                <div class="fw-semibold">{{ $monthlyStats[5]['visits'] ?? 0 }}</div>
                <div class="progress mt-1" style="height: 4px;">
                  <div class="progress-bar" style="width: {{ min(100, ($monthlyStats[5]['visits'] ?? 0) / max(1, max(array_column($monthlyStats, 'visits')))) }}%"></div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <div class="d-flex align-items-center">
              <div class="bg-label-success rounded-circle p-2 me-3">
                <i class="icon-base ri ri-money-dollar-circle-line text-success"></i>
              </div>
              <div>
                <small class="text-muted">Revenue</small>
                <div class="fw-semibold">TZS {{ number_format($monthlyStats[5]['revenue'] ?? 0) }}</div>
                <div class="progress mt-1" style="height: 4px;">
                  <div class="progress-bar bg-success" style="width: {{ min(100, ($monthlyStats[5]['revenue'] ?? 0) / max(1, max(array_column($monthlyStats, 'revenue')))) }}%"></div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <div class="d-flex align-items-center">
              <div class="bg-label-info rounded-circle p-2 me-3">
                <i class="icon-base ri ri-user-add-line text-info"></i>
              </div>
              <div>
                <small class="text-muted">New Members</small>
                <div class="fw-semibold">{{ $monthlyStats[5]['new_members'] ?? 0 }}</div>
                <div class="progress mt-1" style="height: 4px;">
                  <div class="progress-bar bg-info" style="width: {{ min(100, ($monthlyStats[5]['new_members'] ?? 0) / max(1, max(array_column($monthlyStats, 'new_members')))) }}%"></div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <div class="d-flex align-items-center">
              <div class="bg-label-warning rounded-circle p-2 me-3">
                <i class="icon-base ri ri-cake-line text-warning"></i>
              </div>
              <div>
                <small class="text-muted">Birthdays</small>
                <div class="fw-semibold">{{ $thisMonthBirthdays }}</div>
                <div class="progress mt-1" style="height: 4px;">
                  <div class="progress-bar bg-warning" style="width: 100%"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Visit Frequency Analysis -->
        <div class="row mt-4">
          <div class="col-12">
            <h6 class="text-muted mb-3">Visit Frequency This Month</h6>
            <div class="row">
              <div class="col-md-3 mb-2">
                <div class="text-center p-2 bg-light rounded">
                  <div class="h5 mb-1 text-primary">{{ $visitFrequency['once'] }}</div>
                  <small class="text-muted">Visited Once</small>
                </div>
              </div>
              <div class="col-md-3 mb-2">
                <div class="text-center p-2 bg-light rounded">
                  <div class="h5 mb-1 text-success">{{ $visitFrequency['twice'] }}</div>
                  <small class="text-muted">Visited Twice</small>
                </div>
              </div>
              <div class="col-md-3 mb-2">
                <div class="text-center p-2 bg-light rounded">
                  <div class="h5 mb-1 text-warning">{{ $visitFrequency['three_times'] }}</div>
                  <small class="text-muted">Visited 3 Times</small>
                </div>
              </div>
              <div class="col-md-3 mb-2">
                <div class="text-center p-2 bg-light rounded">
                  <div class="h5 mb-1 text-info">{{ $visitFrequency['four_plus'] }}</div>
                  <small class="text-muted">4+ Visits</small>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="text-center mt-3">
          <a href="#" class="btn btn-outline-primary btn-sm">
            <i class="icon-base ri ri-bar-chart-line me-1"></i>
            View Detailed Reports
          </a>
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
              @forelse($recentActivity as $visit)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar avatar-sm me-3">
                        <div class="bg-label-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                          <i class="icon-base ri ri-user-line text-primary"></i>
                        </div>
                      </div>
                      <div>
                        <h6 class="mb-0">{{ $visit->member->full_name }}</h6>
                        <small class="text-muted">{{ $visit->member->email }}</small>
                      </div>
                    </div>
                  </td>
                  <td><span class="badge bg-label-primary">{{ $visit->member->membership_id }}</span></td>
                  <td>{{ $visit->created_at->diffForHumans() }}</td>
                  <td>{{ $visit->member->total_visits }} visits</td>
                  <td><span class="badge bg-label-success">Active</span></td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center text-muted py-4">
                    <i class="icon-base ri ri-restaurant-line me-2"></i>
                    No recent activity
                  </td>
                </tr>
              @endforelse
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
                 <a href="{{ route('cashier.index') }}" class="btn btn-outline-primary">
                   <i class="icon-base ri ri-bank-card-line me-2"></i>
                   Cashier Dashboard
                 </a>
                 <a href="{{ route('members.create') }}" class="btn btn-outline-primary">
                   <i class="icon-base ri ri-user-add-line me-2"></i>
                   Add New Member
                 </a>
                 <a href="{{ route('dining.index') }}" class="btn btn-outline-primary">
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
        @if($todayBirthdays > 0)
          <div class="alert alert-warning">
            <i class="icon-base ri ri-cake-line me-2"></i>
            <strong>Birthday Today!</strong> {{ $todayBirthdays }} member{{ $todayBirthdays > 1 ? 's have' : ' has' }} a birthday today! 🎉
            <a href="{{ route('birthdays.today') }}" class="btn btn-sm btn-warning ms-2">View Details</a>
          </div>
        @endif
        
        @if($thisWeekBirthdays > 0)
          <div class="alert alert-info">
            <i class="icon-base ri ri-cake-line me-2"></i>
            <strong>This Week:</strong> {{ $thisWeekBirthdays }} member{{ $thisWeekBirthdays > 1 ? 's have' : ' has' }} birthdays this week!
            <a href="{{ route('birthdays.this-week') }}" class="btn btn-sm btn-info ms-2">View Details</a>
          </div>
        @endif
        
        @if($todayBirthdays == 0 && $thisWeekBirthdays == 0)
          <div class="text-center text-muted py-3">
            <i class="icon-base ri ri-cake-line me-2"></i>
            No upcoming birthdays this week
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@push('page-js')
<script src="{{ asset('assets/js/dashboards-analytics.js') }}"></script>
@endpush 