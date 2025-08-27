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
                                            <div class="d-flex flex-column gap-1">
                                                <!-- Primary Actions -->
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('events.show', $event) }}" class="btn btn-sm btn-outline-primary" title="View">
                                                        <i class="bx bx-show"></i>
                                                    </a>
                                                    <a href="{{ route('events.edit', $event) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                                        <i class="bx bx-edit"></i>
                                                    </a>
                                                    <a href="{{ route('events.registrations', $event) }}" class="btn btn-sm btn-outline-info" title="Registrations">
                                                        <i class="bx bx-list-ul"></i>
                                                    </a>
                                                </div>
                                                
                                                <!-- Secondary Actions -->
                                                <div class="d-flex gap-1">
                                                    @if($event->status === 'draft')
                                                        <form action="{{ route('events.publish', $event) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success" title="Publish">
                                                                <i class="bx bx-check-circle"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    
                                                    @if($event->status === 'published')
                                                        @if($event->isRegistrationClosed())
                                                            <form action="{{ route('events.open-registration', $event) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-success" title="Open Registration">
                                                                    <i class="bx bx-lock-open"></i>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <form action="{{ route('events.close-registration', $event) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-warning" title="Close Registration"
                                                                        onclick="return confirm('Close registration?')">
                                                                    <i class="bx bx-lock"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @endif
                                                    
                                                    <form action="{{ route('events.destroy', $event) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete"
                                                                onclick="return confirm('Delete this event?')">
                                                            <i class="bx bx-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                                
                                                <!-- Public Links (if applicable) -->
                                                @if($event->is_public && $event->status === 'published')
                                                    <div class="d-flex gap-1">
                                                        <a href="{{ route('public.events.show', [$event->hotel->slug, $event]) }}" 
                                                           class="btn btn-sm btn-outline-dark" target="_blank" title="Public Page">
                                                            <i class="bx bx-external-link"></i>
                                                        </a>
                                                        <a href="{{ route('public.events.register', [$event->hotel->slug, $event]) }}" 
                                                           class="btn btn-sm btn-outline-success" target="_blank" title="Test Registration">
                                                            <i class="bx bx-user-plus"></i>
                                                        </a>
                                                    </div>
                                                @endif
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
