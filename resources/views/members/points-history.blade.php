@extends('layouts.app')

@section('title', 'Points History - ' . $member->full_name)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">
            <i class="icon-base ri ri-star-line me-2"></i>
            Points History - {{ $member->full_name }}
          </h5>
          <div class="d-flex gap-2">
            <a href="{{ route('members.show', $member) }}" class="btn btn-sm btn-outline-secondary">
              <i class="icon-base ri ri-arrow-left-line me-1"></i> Back to Member
            </a>
          </div>
        </div>
        <div class="card-body">
          <!-- Points Summary -->
          <div class="row mb-4">
            <div class="col-md-3">
              <div class="card bg-primary text-white">
                <div class="card-body text-center">
                  <h4>{{ $member->total_points_earned }}</h4>
                  <small>Total Points Earned</small>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card bg-success text-white">
                <div class="card-body text-center">
                  <h4>{{ $member->current_points_balance }}</h4>
                  <small>Current Balance</small>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card bg-info text-white">
                <div class="card-body text-center">
                  <h4>{{ $member->consecutive_visits }}</h4>
                  <small>Consecutive Visits</small>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card {{ $member->qualifies_for_discount ? 'bg-success' : 'bg-warning' }} text-white">
                <div class="card-body text-center">
                  <h4>{{ $member->qualifies_for_discount ? 'Yes' : 'No' }}</h4>
                  <small>Qualifies for Discount</small>
                </div>
              </div>
            </div>
          </div>

          <!-- Points Rules -->
          <div class="alert alert-info mb-4">
            <h6><i class="icon-base ri ri-information-line me-2"></i>Points System Rules:</h6>
            <ul class="mb-0">
              <li><strong>1 point per person</strong> per visit (minimum 50k spending per person)</li>
              <li><strong>Maximum 4 people</strong> for points calculation (200k card limit)</li>
              <li><strong>5 points needed</strong> to qualify for enhanced discounts</li>
              <li><strong>20% discount</strong> on 5th consecutive visit if spending above average 50k per person</li>
              <li><strong>Birthday visits</strong> get special treatment and recognition</li>
            </ul>
          </div>

          <!-- Points History Table -->
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Visit Details</th>
                  <th>Spending</th>
                  <th>People</th>
                  <th>Per Person</th>
                  <th>Points Earned</th>
                  <th>Special Notes</th>
                </tr>
              </thead>
              <tbody>
                @forelse($member->points()->orderBy('created_at', 'desc')->get() as $point)
                  <tr>
                    <td>{{ \Carbon\Carbon::parse($point->created_at)->format('M d, Y H:i') }}</td>
                    <td>
                      @if($point->diningVisit)
                        <a href="{{ route('dining.show', $point->diningVisit) }}" class="text-decoration-none">
                          Visit #{{ $point->diningVisit->id }}
                        </a>
                      @else
                        <span class="text-muted">Visit not linked</span>
                      @endif
                    </td>
                    <td>TZS {{ number_format($point->spending_amount, 0) }}</td>
                    <td>{{ $point->number_of_people }}</td>
                    <td>TZS {{ number_format($point->per_person_spending, 0) }}</td>
                    <td>
                      <span class="badge bg-label-primary">{{ $point->points_earned }} pts</span>
                    </td>
                    <td>
                      @if($point->is_birthday_visit)
                        <span class="badge bg-label-warning">🎂 Birthday Visit</span>
                      @endif
                      @if($point->qualifies_for_discount)
                        <span class="badge bg-label-success">Qualified for Discount</span>
                      @endif
                      @if($point->notes)
                        <small class="text-muted d-block">{{ $point->notes }}</small>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="text-center text-muted">
                      <i class="icon-base ri ri-star-line me-2"></i>
                      No points history yet. Points are earned when members dine with minimum 50k spending per person.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection 