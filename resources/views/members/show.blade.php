@extends('layouts.app')

@section('title', 'Member Profile')

@section('content')
<div class="row">
  <div class="col-lg-4">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between">
        <h5 class="mb-0">Member Card</h5>
        @if($member->card_image_path)
          <div class="d-flex gap-2">
            <a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ asset('storage/'.$member->card_image_path) }}">
              <i class="icon-base ri ri-download-line me-1"></i> Download
            </a>
            <form method="POST" action="{{ route('members.regenerate-card', $member) }}" style="display: inline;">
              @csrf
              <button type="submit" class="btn btn-sm btn-outline-warning" onclick="return confirm('{{ __('app.confirm_regenerate_card') }}')">
                <i class="icon-base ri ri-refresh-line me-1"></i> Regenerate
              </button>
            </form>
          </div>
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

    <!-- QR Code Section -->
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between">
        <h6 class="mb-0">{{ __('app.qr_code') }}</h6>
        @if($member->hasQRCode())
          <div class="d-flex gap-2">
            <a class="btn btn-sm btn-outline-info" href="{{ $member->getQRCodeUrlAttribute() }}" target="_blank">
              <i class="icon-base ri ri-external-link-line me-1"></i> {{ __('app.view_full_size') }}
            </a>
            <form method="POST" action="{{ route('members.generate-qr-code', $member) }}" style="display: inline;">
              @csrf
              <button type="submit" class="btn btn-sm btn-outline-warning" onclick="return confirm('{{ __('app.confirm_regenerate_qr') }}')">
                <i class="icon-base ri ri-refresh-line me-1"></i> {{ __('app.regenerate_qr') }}
              </button>
            </form>
          </div>
        @endif
      </div>
      <div class="card-body text-center">
        @if($member->hasQRCode())
          <img src="{{ $member->getQRCodeUrlAttribute() }}" alt="QR Code" class="img-fluid rounded" style="max-width: 150px;">
          <div class="mt-2">
            <small class="text-muted">{{ __('app.scan_to_verify') }}</small>
          </div>
        @else
          <div class="text-muted mb-2">{{ __('app.no_qr_available') }}</div>
          @if($member->hasCard())
            <form method="POST" action="{{ route('members.generate-qr-code', $member) }}" style="display: inline;">
              @csrf
              <button type="submit" class="btn btn-sm btn-primary">
                <i class="icon-base ri ri-qr-code-line me-1"></i> {{ __('app.generate_qr_code') }}
              </button>
            </form>
          @else
            <small class="text-muted">{{ __('app.generate_card_first') }}</small>
          @endif
        @endif

        <!-- Debug Info -->
        <div class="mt-3">
          <small class="text-muted">
            <strong>Debug:</strong> 
            Card: {{ $member->hasCard() ? 'Yes' : 'No' }}, 
            QR: {{ $member->hasQRCode() ? 'Yes' : 'No' }}, 
            QR Path: {{ $member->qr_code_path ?? 'None' }}
          </small>
        </div>
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
          @if($member->hasCard())
            <a href="{{ route('members.card-preview', $member) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                              <i class="icon-base ri ri-eye-line me-1"></i> {{ __('app.preview_card') }}
            </a>
            <form method="POST" action="{{ route('members.regenerate-card', $member) }}" style="display: inline;">
              @csrf
              <button type="submit" class="btn btn-sm btn-outline-warning" onclick="return confirm('{{ __('app.confirm_regenerate_card') }}')">
                <i class="icon-base ri ri-refresh-line me-1"></i> {{ __('app.regenerate_all') }}
              </button>
            </form>
          @else
            <form method="POST" action="{{ route('members.cards.generate', $member) }}" style="display: inline;">
              @csrf
              <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('Generate virtual card for this member?')">
                <i class="icon-base ri ri-image-add-line me-1"></i> Generate Card
              </button>
            </form>
          @endif
          <a href="{{ route('members.edit', $member) }}" class="btn btn-sm btn-outline-secondary">
            <i class="icon-base ri ri-edit-line me-1"></i> Edit
          </a>
        </div>
      </div>
      <div class="card-body">
        <!-- Personal Information -->
        <div class="row mb-4">
          <div class="col-12">
            <h6 class="text-muted mb-3 border-bottom pb-2">
              <i class="icon-base ri ri-user-line me-2"></i>Personal Information
            </h6>
          </div>
          <div class="col-md-6 mb-3">
            <div class="d-flex align-items-center">
              <div class="bg-label-primary rounded-circle p-2 me-3">
                <i class="icon-base ri ri-user-line text-primary"></i>
              </div>
              <div>
                <small class="text-muted">Full Name</small>
                <div class="fw-semibold">{{ $member->full_name }}</div>
              </div>
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <div class="d-flex align-items-center">
              <div class="bg-label-info rounded-circle p-2 me-3">
                <i class="icon-base ri ri-mail-line text-info"></i>
              </div>
              <div>
                <small class="text-muted">Email</small>
                <div class="fw-semibold">{{ $member->email }}</div>
              </div>
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <div class="d-flex align-items-center">
              <div class="bg-label-success rounded-circle p-2 me-3">
                <i class="icon-base ri ri-phone-line text-success"></i>
              </div>
              <div>
                <small class="text-muted">Phone</small>
                <div class="fw-semibold">{{ $member->phone }}</div>
              </div>
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <div class="d-flex align-items-center">
              <div class="bg-label-warning rounded-circle p-2 me-3">
                <i class="icon-base ri ri-calendar-line text-warning"></i>
              </div>
              <div>
                <small class="text-muted">Birth Date</small>
                <div class="fw-semibold">{{ $member->birth_date ? $member->birth_date->format('M d, Y') : 'Not provided' }}</div>
              </div>
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <div class="d-flex align-items-center">
              <div class="bg-label-secondary rounded-circle p-2 me-3">
                <i class="icon-base ri ri-map-pin-line text-secondary"></i>
              </div>
              <div>
                <small class="text-muted">Address</small>
                <div class="fw-semibold">{{ $member->address ?: 'Not provided' }}</div>
              </div>
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <div class="d-flex align-items-center">
              <div class="bg-label-success rounded-circle p-2 me-3">
                <i class="icon-base ri ri-check-line text-success"></i>
              </div>
              <div>
                <small class="text-muted">Status</small>
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
          </div>
        </div>

        <!-- Activity & Statistics -->
        <div class="row mb-4">
          <div class="col-12">
            <h6 class="text-muted mb-3 border-bottom pb-2">
              <i class="icon-base ri ri-bar-chart-line me-2"></i>Activity & Statistics
            </h6>
          </div>
          <div class="col-md-3 mb-3">
            <div class="text-center p-3 bg-light rounded">
              <div class="h4 mb-1 text-primary">{{ $member->total_visits }}</div>
              <small class="text-muted">Total Visits</small>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <div class="text-center p-3 bg-light rounded">
              <div class="h4 mb-1 text-success">TZS {{ number_format($member->total_spent,0) }}</div>
              <small class="text-muted">Total Spent</small>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <div class="text-center p-3 bg-light rounded">
              <div class="h4 mb-1 text-warning">{{ rtrim(rtrim(number_format($member->current_discount_rate,2,'.',''), '0'),'.') }}%</div>
              <small class="text-muted">Current Discount</small>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <div class="text-center p-3 bg-light rounded">
              <div class="h4 mb-1 text-info">{{ $member->consecutive_visits }}</div>
              <small class="text-muted">Consecutive Visits</small>
            </div>
          </div>
        </div>

        <!-- Points Information -->
        <div class="row mb-4">
          <div class="col-12">
            <h6 class="text-muted mb-3 border-bottom pb-2">
              <i class="icon-base ri ri-star-line me-2"></i>Points & Rewards
            </h6>
          </div>
          <div class="col-md-3 mb-3">
            <div class="text-center p-3 bg-light rounded">
              <div class="h4 mb-1 text-success">{{ $member->total_points_earned }}</div>
              <small class="text-muted">Points Earned</small>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <div class="text-center p-3 bg-light rounded">
              <div class="h4 mb-1 text-warning">{{ $member->total_points_used }}</div>
              <small class="text-muted">Points Used</small>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <div class="text-center p-3 bg-light rounded">
              <div class="h4 mb-1 text-primary">{{ $member->current_points_balance }}</div>
              <small class="text-muted">Current Balance</small>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <div class="text-center p-3 bg-light rounded">
              <div class="h4 mb-1">
                @if($member->qualifies_for_discount)
                  <span class="badge bg-label-success">Yes</span>
                @else
                  <span class="badge bg-label-warning">No</span>
                @endif
              </div>
              <small class="text-muted">Qualifies for Discount</small>
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="row mb-3">
          <div class="col-12">
            <h6 class="text-muted mb-3 border-bottom pb-2">
              <i class="icon-base ri ri-time-line me-2"></i>Recent Activity
            </h6>
          </div>
          <div class="col-md-4 mb-3">
            <div class="d-flex align-items-center">
              <div class="bg-label-info rounded-circle p-2 me-3">
                <i class="icon-base ri ri-calendar-event-line text-info"></i>
              </div>
              <div>
                <small class="text-muted">Last Visit</small>
                <div class="fw-semibold">{{ $member->last_visit_date ? \Carbon\Carbon::parse($member->last_visit_date)->format('M d, Y') : 'Never' }}</div>
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="d-flex align-items-center">
              <div class="bg-label-success rounded-circle p-2 me-3">
                <i class="icon-base ri ri-money-dollar-circle-line text-success"></i>
              </div>
              <div>
                <small class="text-muted">Average Spending</small>
                <div class="fw-semibold">TZS {{ number_format($member->average_spending_per_visit,0) }}</div>
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="d-flex align-items-center">
              <div class="bg-label-warning rounded-circle p-2 me-3">
                <i class="icon-base ri ri-fire-line text-warning"></i>
              </div>
              <div>
                <small class="text-muted">Consecutive Visits</small>
                <div class="fw-semibold">{{ $member->consecutive_visits }}</div>
              </div>
            </div>
          </div>
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
            @else
              <div class="mb-3">
                <strong class="text-muted">⚠️ Allergies:</strong>
                <div class="text-muted">None reported</div>
              </div>
            @endif
            
            @if($member->dietary_preferences)
              <div class="mb-3">
                <strong>🍽️ Dietary Preferences:</strong>
                <div>{{ $member->dietary_preferences }}</div>
              </div>
            @else
              <div class="mb-3">
                <strong class="text-muted">🍽️ Dietary Preferences:</strong>
                <div class="text-muted">None specified</div>
              </div>
            @endif
            
            @if($member->special_requests)
              <div class="mb-3">
                <strong>🎯 Special Requests:</strong>
                <div>{{ $member->special_requests }}</div>
              </div>
            @else
              <div class="mb-3">
                <strong class="text-muted">🎯 Special Requests:</strong>
                <div class="text-muted">None specified</div>
              </div>
            @endif
          </div>
          
          <div class="col-md-6">
            @if($member->additional_notes)
              <div class="mb-3">
                <strong>📝 Additional Notes:</strong>
                <div>{{ $member->additional_notes }}</div>
              </div>
            @else
              <div class="mb-3">
                <strong class="text-muted">📝 Additional Notes:</strong>
                <div class="text-muted">None</div>
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
            @else
              <div class="mb-3">
                <strong class="text-muted">🚨 Emergency Contact:</strong>
                <div class="text-muted">Not provided</div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>

    <!-- Physical Card Information -->
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
          <i class="icon-base ri ri-card-line me-2"></i>
          Physical Card Information
        </h6>
        <div class="d-flex gap-2">
          <a href="{{ route('members.physical-cards.issue-form', $member) }}" class="btn btn-sm btn-outline-primary">
            <i class="icon-base ri ri-card-line me-1"></i> Issue Card
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <strong>Card Status:</strong>
              <div>
                <span class="badge {{ $member->getPhysicalCardStatusBadgeClass() }}">
                  {{ $member->getPhysicalCardStatusText() }}
                </span>
              </div>
            </div>
            
            @if($member->physical_card_issued_date)
              <div class="mb-3">
                <strong>Issued Date:</strong>
                <div>{{ $member->physical_card_issued_date->format('M d, Y') }}</div>
              </div>
            @endif
            
            @if($member->physical_card_issued_by)
              <div class="mb-3">
                <strong>Issued By:</strong>
                <div>{{ $member->physical_card_issued_by }}</div>
              </div>
            @endif
          </div>
          
          <div class="col-md-6">
            @if($member->physical_card_delivered_date)
              <div class="mb-3">
                <strong>Delivered Date:</strong>
                <div>{{ $member->physical_card_delivered_date->format('M d, Y') }}</div>
              </div>
            @endif
            
            @if($member->physical_card_delivered_by)
              <div class="mb-3">
                <strong>Delivered By:</strong>
                <div>{{ $member->physical_card_delivered_by }}</div>
              </div>
            @endif
            
            @if($member->physical_card_notes)
              <div class="mb-3">
                <strong>Notes:</strong>
                <div>{{ $member->physical_card_notes }}</div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>

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