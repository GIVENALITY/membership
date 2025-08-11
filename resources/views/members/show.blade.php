@extends('layouts.app')

@section('title', 'Member Profile')

@section('content')
<div class="row">
  <div class="col-lg-4">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between">
        <h5 class="mb-0">Member Card</h5>
        @if($member->card_image_path)
          <a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ asset('storage/'.$member->card_image_path) }}">
            <i class="icon-base ri ri-download-line me-1"></i> Download
          </a>
        @endif
      </div>
      <div class="card-body text-center">
        @if($member->card_image_path)
          <img src="{{ asset('storage/'.$member->card_image_path) }}" alt="Membership Card" class="img-fluid rounded">
        @else
          <div class="text-muted">Card will appear here after generation.</div>
        @endif
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h6 class="mb-0">Status</h6></div>
      <div class="card-body">
        <p class="mb-2"><strong>Membership ID:</strong> <span class="badge bg-label-primary">{{ $member->membership_id }}</span></p>
        <p class="mb-2"><strong>Membership Type:</strong> {{ optional($member->membershipType)->name ?? 'N/A' }}</p>
        <p class="mb-2"><strong>Joined:</strong> {{ \Carbon\Carbon::parse($member->join_date)->toFormattedDateString() }}</p>
        <p class="mb-0"><strong>Expires:</strong> {{ $member->expires_at ? $member->expires_at->toFormattedDateString() : 'N/A' }}</p>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Member Details</h5>
        <div class="d-flex gap-2">
          <a href="{{ route('members.points-history', $member) }}" class="btn btn-sm btn-outline-info">
            <i class="icon-base ri ri-star-line me-1"></i> Points History
          </a>
          <a href="{{ route('members.edit', $member) }}" class="btn btn-sm btn-outline-secondary">
            <i class="icon-base ri ri-edit-line me-1"></i> Edit
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-6"><strong>Full Name</strong><div>{{ $member->full_name }}</div></div>
          <div class="col-md-6"><strong>Email</strong><div>{{ $member->email }}</div></div>
        </div>
        <div class="row mb-3">
          <div class="col-md-6"><strong>Phone</strong><div>{{ $member->phone }}</div></div>
          <div class="col-md-6"><strong>Status</strong>
            <div>
              @if($member->status==='active')
                <span class="badge bg-label-success">Active</span>
              @elseif($member->status==='inactive')
                <span class="badge bg-label-secondary">Inactive</span>
              @else
                <span class="badge bg-label-danger">Suspended</span>
              @endif
            </div>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-md-4"><strong>Total Visits</strong><div>{{ $member->total_visits }}</div></div>
          <div class="col-md-4"><strong>Total Spent</strong><div>TZS {{ number_format($member->total_spent,0) }}</div></div>
          <div class="col-md-4"><strong>Current Discount</strong><div>{{ rtrim(rtrim(number_format($member->current_discount_rate,2,'.',''), '0'),'.') }}%</div></div>
        </div>
        <div class="row mb-3">
          <div class="col-md-3"><strong>Points Earned</strong><div>{{ $member->total_points_earned }}</div></div>
          <div class="col-md-3"><strong>Points Used</strong><div>{{ $member->total_points_used }}</div></div>
          <div class="col-md-3"><strong>Current Balance</strong><div>{{ $member->current_points_balance }}</div></div>
          <div class="col-md-3"><strong>Qualifies for Discount</strong>
            <div>
              @if($member->qualifies_for_discount)
                <span class="badge bg-label-success">Yes (5+ points)</span>
              @else
                <span class="badge bg-label-warning">No ({{ 5 - $member->current_points_balance }} more needed)</span>
              @endif
            </div>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-md-4"><strong>Consecutive Visits</strong><div>{{ $member->consecutive_visits }}</div></div>
          <div class="col-md-4"><strong>Average Spending</strong><div>TZS {{ number_format($member->average_spending_per_visit,0) }}</div></div>
          <div class="col-md-4"><strong>Last Visit</strong><div>{{ $member->last_visit_date ? \Carbon\Carbon::parse($member->last_visit_date)->format('M d, Y') : 'Never' }}</div></div>
        </div>
        @if($member->isBirthdayVisit())
          <div class="alert alert-warning mb-0">
            <i class="icon-base ri ri-cake-line me-2"></i>
            <strong>Birthday Alert:</strong> This member's birthday is coming up! Special treatment will be applied on their next visit.
          </div>
        @endif

        @if($member->membershipType && $member->membershipType->getNextDiscountMilestone($member->total_visits))
          @php
            $nextMilestone = $member->membershipType->getNextDiscountMilestone($member->total_visits);
          @endphp
          <div class="alert alert-info mb-0 mt-3">
            <i class="icon-base ri ri-trending-up-line me-2"></i>
            <strong>Next Milestone:</strong> {{ $nextMilestone['remaining'] }} more visit(s) to get {{ $nextMilestone['discount'] }}% discount!
          </div>
        @endif
      </div>
    </div>

    <!-- Member Preferences & Details -->
    @if($member->allergies || $member->dietary_preferences || $member->special_requests || $member->additional_notes || $member->emergency_contact_name)
    <div class="card mb-4">
      <div class="card-header">
        <h6 class="mb-0">
          <i class="icon-base ri ri-heart-line me-2"></i>
          Member Preferences & Details
        </h6>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            @if($member->allergies)
              <div class="mb-3">
                <strong class="text-danger">⚠️ Allergies:</strong>
                <div class="text-danger">{{ $member->allergies }}</div>
              </div>
            @endif
            
            @if($member->dietary_preferences)
              <div class="mb-3">
                <strong>🍽️ Dietary Preferences:</strong>
                <div>{{ $member->dietary_preferences }}</div>
              </div>
            @endif
            
            @if($member->special_requests)
              <div class="mb-3">
                <strong>🎯 Special Requests:</strong>
                <div>{{ $member->special_requests }}</div>
              </div>
            @endif
          </div>
          
          <div class="col-md-6">
            @if($member->additional_notes)
              <div class="mb-3">
                <strong>📝 Additional Notes:</strong>
                <div>{{ $member->additional_notes }}</div>
              </div>
            @endif
            
            @if($member->emergency_contact_name)
              <div class="mb-3">
                <strong class="text-warning">🚨 Emergency Contact:</strong>
                <div>
                  <strong>{{ $member->emergency_contact_name }}</strong>
                  @if($member->emergency_contact_relationship)
                    <small class="text-muted">({{ $member->emergency_contact_relationship }})</small>
                  @endif
                  <br>
                  <a href="tel:{{ $member->emergency_contact_phone }}" class="text-decoration-none">
                    {{ $member->emergency_contact_phone }}
                  </a>
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
    @endif

    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Recent Visits</h5>
        <div class="d-flex gap-2">
          <a href="{{ route('dining.history.member', $member) }}" class="btn btn-sm btn-outline-info">
            <i class="icon-base ri ri-history-line me-1"></i> Full History
          </a>
          <a href="{{ route('dining.index') }}" class="btn btn-sm btn-outline-primary">
            <i class="icon-base ri ri-restaurant-line me-1"></i> Record Visit
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Date</th><th>Bill</th><th>Discount</th><th>Final</th><th>Receipt</th>
              </tr>
            </thead>
            <tbody>
              @forelse($member->diningVisits()->latest('created_at')->limit(10)->get() as $v)
                <tr>
                  <td>{{ \Carbon\Carbon::parse($v->created_at)->diffForHumans() }}</td>
                  <td>TZS {{ number_format($v->amount_spent ?? 0,0) }}</td>
                  <td>TZS {{ number_format($v->discount_amount ?? 0,0) }} ({{ $v->discount_percentage ?? 0 }}%)</td>
                  <td><strong>TZS {{ number_format($v->final_amount ?? 0,0) }}</strong></td>
                  <td>
                    @if($v->receipt_path)
                      <a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ $v->receipt_url }}">
                        <i class="icon-base ri ri-eye-line"></i>
                      </a>
                    @else
                      <span class="text-muted">N/A</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center text-muted">No visits yet</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection 