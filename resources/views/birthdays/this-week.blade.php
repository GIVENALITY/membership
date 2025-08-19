@extends('layouts.app')

@section('title', "This Week's Birthdays")

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">
          <i class="icon-base ri ri-calendar-line me-2 text-info"></i>
          This Week's Birthdays
        </h4>
        <div class="d-flex gap-2">
          <a href="{{ route('birthdays.today') }}" class="btn btn-outline-warning btn-sm">
            <i class="icon-base ri ri-cake-line me-1"></i>
            Today's Birthdays
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="mb-4">
          <p class="text-muted mb-0">
            <i class="icon-base ri ri-calendar-event-line me-1"></i>
            Week of {{ $startOfWeek->format('M d, Y') }} - {{ $endOfWeek->format('M d, Y') }}
          </p>
        </div>

        @if(count($weekBirthdays) > 0)
          @foreach($weekBirthdays as $date => $members)
            <div class="mb-4">
              <h5 class="border-bottom pb-2 mb-3">
                <i class="icon-base ri ri-calendar-line me-2 text-primary"></i>
                {{ $date }}
                @if($date === Carbon\Carbon::today()->format('l, M d'))
                  <span class="badge bg-warning ms-2">Today!</span>
                @endif
              </h5>
              
              <div class="row">
                @foreach($members as $member)
                  <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100 {{ $date === Carbon\Carbon::today()->format('l, M d') ? 'border-warning' : 'border-info' }}">
                      <div class="card-body text-center">
                        <div class="mb-3">
                          <div class="{{ $date === Carbon\Carbon::today()->format('l, M d') ? 'bg-warning' : 'bg-info' }} rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="icon-base ri ri-cake-line text-white" style="font-size: 1.5rem;"></i>
                          </div>
                        </div>
                        <h6 class="card-title mb-2">{{ $member->full_name }}</h6>
                        <p class="text-muted mb-2 small">
                          <i class="icon-base ri ri-mail-line me-1"></i>
                          {{ $member->email }}
                        </p>
                        <p class="text-muted mb-2 small">
                          <i class="icon-base ri ri-phone-line me-1"></i>
                          {{ $member->phone }}
                        </p>
                        <p class="text-muted mb-3 small">
                          <i class="icon-base ri ri-vip-crown-line me-1"></i>
                          {{ optional($member->membershipType)->name ?? 'No Type' }}
                        </p>
                        <div class="d-flex justify-content-center gap-1">
                          <a href="{{ route('members.show', $member) }}" class="btn btn-sm btn-outline-primary">
                            <i class="icon-base ri ri-eye-line"></i>
                          </a>
                          <a href="{{ route('dining.index') }}?member_id={{ $member->id }}" class="btn btn-sm btn-success">
                            <i class="icon-base ri ri-restaurant-line"></i>
                          </a>
                        </div>
                      </div>
                      @if($date === Carbon\Carbon::today()->format('l, M d'))
                        <div class="card-footer bg-warning bg-opacity-10 text-center">
                          <small class="text-warning fw-bold">
                            <i class="icon-base ri ri-gift-line me-1"></i>
                            Birthday Today! 🎉
                          </small>
                        </div>
                      @endif
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          @endforeach
        @else
          <div class="text-center py-5">
            <div class="mb-3">
              <i class="icon-base ri ri-calendar-line text-muted" style="font-size: 4rem;"></i>
            </div>
            <h5 class="text-muted">No Birthdays This Week</h5>
            <p class="text-muted">There are no member birthdays this week. Check out today's birthdays instead!</p>
            <a href="{{ route('birthdays.today') }}" class="btn btn-warning">
              <i class="icon-base ri ri-cake-line me-1"></i>
              View Today's Birthdays
            </a>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
