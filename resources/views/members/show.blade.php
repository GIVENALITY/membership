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
        <a href="{{ route('members.edit', $member) }}" class="btn btn-sm btn-outline-secondary">
          <i class="icon-base ri ri-edit-line me-1"></i> Edit
        </a>
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
              @forelse($member->diningVisits()->latest('visited_at')->limit(10)->get() as $v)
                <tr>
                  <td>{{ \Carbon\Carbon::parse($v->visited_at)->diffForHumans() }}</td>
                  <td>TZS {{ number_format($v->bill_amount,0) }}</td>
                  <td>TZS {{ number_format($v->discount_amount,0) }} ({{ rtrim(rtrim(number_format($v->discount_rate,2,'.',''), '0'),'.') }}%)</td>
                  <td><strong>TZS {{ number_format($v->final_amount,0) }}</strong></td>
                  <td>
                    @if($v->receipt_path)
                      <a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ asset('storage/'.$v->receipt_path) }}">
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