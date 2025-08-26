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
                                        <th>Title</th>
                                        <th>Date & Time</th>
                                        <th>Location</th>
                                        <th>Capacity</th>
                                        <th>Status</th>
                                        <th>Registrations</th>
                                        <th>Actions</th>
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
                                                         style="width: 40px; height: 40px; object-fit: cover;">
                                                @endif
                                                <div>
                                                    <h6 class="mb-0">{{ $event->title }}</h6>
                                                    <small class="text-muted">{{ Str::limit($event->description, 50) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <div class="fw-semibold">{{ $event->start_date->format('M d, Y') }}</div>
                                                <small class="text-muted">
                                                    {{ $event->start_date->format('g:i A') }} - {{ $event->end_date->format('g:i A') }}
                                                </small>
                                            </div>
                                        </td>
                                        <td>{{ $event->location ?? 'TBD' }}</td>
                                        <td>
                                            @if($event->max_capacity)
                                                <span class="badge bg-info">{{ $event->getAvailableSpots() }}/{{ $event->max_capacity }}</span>
                                            @else
                                                <span class="badge bg-success">Unlimited</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="{{ $event->getStatusBadgeClass() }}">
                                                {{ $event->getStatusText() }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="fw-semibold">{{ $event->confirmedRegistrations()->count() }}</span>
                                                @if($event->pendingRegistrations()->count() > 0)
                                                    <span class="badge bg-warning ms-1">{{ $event->pendingRegistrations()->count() }} pending</span>
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
