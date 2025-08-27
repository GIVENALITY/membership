@extends('layouts.app')

@section('title', 'Events')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Events</h5>
                    <a href="{{ route('events.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus"></i> Create Event
                    </a>
                </div>
                <div class="card-body">
                    @if($events->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Event</th>
                                        <th>Details</th>
                                        <th>Status & Capacity</th>
                                        <th>Registrations</th>
                                        <th width="120">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($events as $event)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($event->image)
                                                    <img src="{{ asset('storage/' . $event->image) }}" 
                                                         alt="{{ $event->title }}" 
                                                         class="rounded me-2" 
                                                         style="width: 32px; height: 32px; object-fit: cover;">
                                                @endif
                                                <div>
                                                    <h6 class="mb-0">{{ $event->title }}</h6>
                                                    <small class="text-muted">{{ Str::limit($event->description, 40) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <div class="fw-semibold">{{ $event->start_date->format('M d, Y') }}</div>
                                                <div class="text-muted">{{ $event->start_date->format('g:i A') }} - {{ $event->end_date->format('g:i A') }}</div>
                                                <div class="text-muted">{{ $event->location ?? 'TBD' }}</div>
                                                <div class="fw-semibold">
                                                    {{ $event->price > 0 ? (Auth::user()->hotel->currency_symbol ?? '$') . number_format($event->price, 2) : 'Free' }}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <div class="mb-1">
                                                    <span class="{{ $event->getStatusBadgeClass() }}">
                                                        {{ $event->getStatusText() }}
                                                    </span>
                                                </div>
                                                <div class="mb-1">
                                                    @if($event->isRegistrationClosed())
                                                        <span class="badge bg-danger">Registration Closed</span>
                                                    @else
                                                        <span class="badge bg-success">Registration Open</span>
                                                    @endif
                                                </div>
                                                <div>
                                                    @if($event->max_capacity)
                                                        <span class="badge bg-info">{{ $event->getAvailableSpots() }}/{{ $event->max_capacity }}</span>
                                                    @else
                                                        <span class="badge bg-success">Unlimited</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <div class="fw-semibold fs-5">{{ $event->confirmedRegistrations()->count() }}</div>
                                                <small class="text-muted">confirmed</small>
                                                @if($event->pendingRegistrations()->count() > 0)
                                                    <div class="mt-1">
                                                        <span class="badge bg-warning">{{ $event->pendingRegistrations()->count() }} pending</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="{{ route('events.show', $event) }}">
                                                        <i class="bx bx-show me-1"></i> View
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="{{ route('events.edit', $event) }}">
                                                        <i class="bx bx-edit me-1"></i> Edit
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="{{ route('events.registrations', $event) }}">
                                                        <i class="bx bx-list-ul me-1"></i> Registrations
                                                    </a></li>
                                                    @if($event->is_public && $event->status === 'published')
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item" href="{{ route('public.events.show', [$event->hotel->slug, $event]) }}" target="_blank">
                                                        <i class="bx bx-external-link me-1"></i> View Public Page
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="{{ route('public.events.register', [$event->hotel->slug, $event]) }}" target="_blank">
                                                        <i class="bx bx-user-plus me-1"></i> Test Registration
                                                    </a></li>
                                                    @endif
                                                    @if($event->status === 'draft')
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="{{ route('events.publish', $event) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item text-success">
                                                                    <i class="bx bx-check-circle me-1"></i> Publish
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                    @if($event->status === 'published' && $event->isUpcoming())
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="{{ route('events.cancel', $event) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item text-danger" 
                                                                        onclick="return confirm('Are you sure you want to cancel this event?')">
                                                                    <i class="bx bx-x-circle me-1"></i> Cancel Event
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                    @if($event->status === 'published')
                                                        <li><hr class="dropdown-divider"></li>
                                                        @if($event->isRegistrationClosed())
                                                            <li>
                                                                <form action="{{ route('events.open-registration', $event) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="dropdown-item text-success">
                                                                        <i class="bx bx-lock-open me-1"></i> Open Registration
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @else
                                                            <li>
                                                                <form action="{{ route('events.close-registration', $event) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="dropdown-item text-warning" 
                                                                            onclick="return confirm('Are you sure you want to close registration for this event?')">
                                                                        <i class="bx bx-lock me-1"></i> Close Registration
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                    @endif
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('events.destroy', $event) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger" 
                                                                    onclick="return confirm('Are you sure you want to delete this event?')">
                                                                <i class="bx bx-trash me-1"></i> Delete
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $events->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bx bx-calendar-event bx-lg text-muted mb-3"></i>
                            <h5 class="text-muted">No events found</h5>
                            <p class="text-muted">Create your first event to get started.</p>
                            <a href="{{ route('events.create') }}" class="btn btn-primary">
                                <i class="bx bx-plus"></i> Create Event
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
