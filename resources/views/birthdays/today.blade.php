@extends('layouts.app')

@section('title', "Today's Birthdays")

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">
          <i class="icon-base ri ri-cake-line me-2 text-warning"></i>
          Today's Birthdays
        </h4>
        <div class="d-flex gap-2">
          <a href="{{ route('birthdays.this-week') }}" class="btn btn-outline-primary btn-sm">
            <i class="icon-base ri ri-calendar-line me-1"></i>
            This Week's Birthdays
          </a>
        </div>
      </div>
      <div class="card-body">
        @if($todayBirthdays->count() > 0)
          <div class="row">
            @foreach($todayBirthdays as $member)
              <div class="col-md-6 col-lg-4 mb-4">
                <div class="card border-warning h-100">
                  <div class="card-body text-center">
                    <div class="mb-3">
                      <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="icon-base ri ri-cake-line text-white" style="font-size: 2rem;"></i>
                      </div>
                    </div>
                    <h5 class="card-title mb-2">{{ $member->full_name }}</h5>
                    <p class="text-muted mb-2">
                      <i class="icon-base ri ri-mail-line me-1"></i>
                      {{ $member->email }}
                    </p>
                    <p class="text-muted mb-2">
                      <i class="icon-base ri ri-phone-line me-1"></i>
                      {{ $member->phone }}
                    </p>
                    <p class="text-muted mb-3">
                      <i class="icon-base ri ri-vip-crown-line me-1"></i>
                      {{ optional($member->membershipType)->name ?? 'No Type' }}
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                      <a href="{{ route('members.show', $member) }}" class="btn btn-sm btn-outline-primary">
                        <i class="icon-base ri ri-eye-line me-1"></i>
                        View Profile
                      </a>
                      <a href="{{ route('dining.index') }}?member_id={{ $member->id }}" class="btn btn-sm btn-success">
                        <i class="icon-base ri ri-restaurant-line me-1"></i>
                        Record Visit
                      </a>
                    </div>
                  </div>
                  <div class="card-footer bg-warning bg-opacity-10 text-center">
                    <small class="text-warning fw-bold">
                      <i class="icon-base ri ri-gift-line me-1"></i>
                      Birthday Today! 🎉
                    </small>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="text-center py-5">
            <div class="mb-3">
              <i class="icon-base ri ri-cake-line text-muted" style="font-size: 4rem;"></i>
            </div>
            <h5 class="text-muted">No Birthdays Today</h5>
            <p class="text-muted">There are no member birthdays today. Check out this week's birthdays instead!</p>
            <a href="{{ route('birthdays.this-week') }}" class="btn btn-primary">
              <i class="icon-base ri ri-calendar-line me-1"></i>
              View This Week's Birthdays
            </a>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
