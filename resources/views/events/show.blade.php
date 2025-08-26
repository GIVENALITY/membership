@extends('layouts.app')

@section('title', $event->title)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <!-- Event Header -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $event->title }}</h4>
                        <span class="{{ $event->getStatusBadgeClass() }}">{{ $event->getStatusText() }}</span>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('events.edit', $event) }}" class="btn btn-primary">
                            <i class="bx bx-edit"></i> Edit Event
                        </a>
                        <a href="{{ route('events.registrations', $event) }}" class="btn btn-info">
                            <i class="bx bx-list-ul"></i> View Registrations
                        </a>
                        <a href="{{ route('events.export-registrations', $event) }}" class="btn btn-success">
                            <i class="bx bx-download"></i> Export
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            @if($event->image)
                                <img src="{{ asset('storage/' . $event->image) }}" 
                                     alt="{{ $event->title }}" 
                                     class="img-fluid rounded mb-3" 
                                     style="max-height: 300px; object-fit: cover;">
                            @endif
                            
                            <div class="mb-3">
                                <h6>Description</h6>
                                <p class="text-muted">{{ $event->description }}</p>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <h6>Date & Time</h6>
                                        <p class="mb-1">
                                            <i class="bx bx-calendar text-primary me-2"></i>
                                            {{ $event->start_date->format('M d, Y') }}
                                        </p>
                                        <p class="mb-0">
                                            <i class="bx bx-time text-primary me-2"></i>
                                            {{ $event->start_date->format('g:i A') }} - {{ $event->end_date->format('g:i A') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <h6>Location</h6>
                                        <p class="mb-0">
                                            <i class="bx bx-map-pin text-primary me-2"></i>
                                            {{ $event->location ?? 'TBD' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <h6>Capacity</h6>
                                        <p class="mb-0">
                                            @if($event->max_capacity)
                                                <span class="badge bg-info">{{ $event->getAvailableSpots() }}/{{ $event->max_capacity }} spots available</span>
                                            @else
                                                <span class="badge bg-success">Unlimited capacity</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <h6>Price</h6>
                                        <p class="mb-0">
                                            <i class="bx bx-dollar text-primary me-2"></i>
                                            {{ $event->price > 0 ? '$' . number_format($event->price, 2) : 'Free' }} per person
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Statistics -->
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Registration Statistics</h6>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="mb-2">
                                                <h4 class="mb-0 text-primary">{{ $stats['total_registrations'] }}</h4>
                                                <small class="text-muted">Total</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-2">
                                                <h4 class="mb-0 text-success">{{ $stats['confirmed_registrations'] }}</h4>
                                                <small class="text-muted">Confirmed</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-2">
                                                <h4 class="mb-0 text-warning">{{ $stats['pending_registrations'] }}</h4>
                                                <small class="text-muted">Pending</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-2">
                                                <h4 class="mb-0 text-info">{{ $stats['total_guests'] }}</h4>
                                                <small class="text-muted">Total Guests</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="card mt-3">
                                <div class="card-body">
                                    <h6 class="card-title">Quick Actions</h6>
                                    @if($event->status === 'draft')
                                        <form action="{{ route('events.publish', $event) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm w-100 mb-2">
                                                <i class="bx bx-check-circle"></i> Publish Event
                                            </button>
                                        </form>
                                    @endif
                                    
                                    @if($event->status === 'published' && $event->isUpcoming())
                                        <form action="{{ route('events.cancel', $event) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm w-100 mb-2" 
                                                    onclick="return confirm('Are you sure you want to cancel this event?')">
                                                <i class="bx bx-x-circle"></i> Cancel Event
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('events.registrations', $event) }}" class="btn btn-info btn-sm w-100">
                                        <i class="bx bx-list-ul"></i> Manage Registrations
                                    </a>
                                </div>
                            </div>

                            <!-- Public Links -->
                            @if($event->is_public && $event->status === 'published')
                            <div class="card mt-3">
                                <div class="card-body">
                                    <h6 class="card-title">Public Links</h6>
                                    <div class="mb-2">
                                        <label class="form-label small">Public Event URL:</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm" 
                                                   value="{{ route('public.events.show', [$event->hotel->slug, $event]) }}" 
                                                   readonly id="publicEventUrl">
                                            <button class="btn btn-outline-primary btn-sm" type="button" 
                                                    onclick="copyToClipboard('publicEventUrl')">
                                                <i class="bx bx-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small">Registration URL:</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm" 
                                                   value="{{ route('public.events.register', [$event->hotel->slug, $event]) }}" 
                                                   readonly id="registrationUrl">
                                            <button class="btn btn-outline-primary btn-sm" type="button" 
                                                    onclick="copyToClipboard('registrationUrl')">
                                                <i class="bx bx-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="d-grid gap-1">
                                        <a href="{{ route('public.events.show', [$event->hotel->slug, $event]) }}" 
                                           target="_blank" class="btn btn-primary btn-sm">
                                            <i class="bx bx-external-link"></i> View Public Page
                                        </a>
                                        <a href="{{ route('public.events.register', [$event->hotel->slug, $event]) }}" 
                                           target="_blank" class="btn btn-success btn-sm">
                                            <i class="bx bx-user-plus"></i> Test Registration
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Registrations -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Registrations</h5>
                    <a href="{{ route('events.registrations', $event) }}" class="btn btn-primary btn-sm">
                        View All Registrations
                    </a>
                </div>
                <div class="card-body">
                    @if($registrations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Guests</th>
                                        <th>Status</th>
                                        <th>Registered</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($registrations as $registration)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                                        {{ strtoupper(substr($registration->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $registration->name }}</h6>
                                                    @if($registration->member)
                                                        <small class="text-muted">Member</small>
                                                    @else
                                                        <small class="text-muted">External</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $registration->email }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $registration->number_of_guests }}</span>
                                        </td>
                                        <td>
                                            <span class="{{ $registration->getStatusBadgeClass() }}">
                                                {{ $registration->getStatusText() }}
                                            </span>
                                        </td>
                                        <td>{{ $registration->registered_at->format('M d, Y g:i A') }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    @if($registration->isPending())
                                                        <li>
                                                            <form action="{{ route('events.confirm-registration', [$event, $registration]) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item text-success">
                                                                    <i class="bx bx-check me-1"></i> Confirm
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                    @if($registration->isConfirmed())
                                                        <li>
                                                            <form action="{{ route('events.mark-attended', [$event, $registration]) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item text-primary">
                                                                    <i class="bx bx-user-check me-1"></i> Mark Attended
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                    @if(in_array($registration->status, ['pending', 'confirmed']))
                                                        <li>
                                                            <form action="{{ route('events.cancel-registration', [$event, $registration]) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item text-danger" 
                                                                        onclick="return confirm('Are you sure you want to cancel this registration?')">
                                                                    <i class="bx bx-x me-1"></i> Cancel
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-3">
                            {{ $registrations->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bx bx-user-plus bx-lg text-muted mb-3"></i>
                            <h6 class="text-muted">No registrations yet</h6>
                            <p class="text-muted">Registrations will appear here once people start signing up.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    element.select();
    element.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand('copy');
        // Show success message
        const button = element.nextElementSibling;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="bx bx-check"></i>';
        button.classList.remove('btn-outline-primary');
        button.classList.add('btn-success');
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-primary');
        }, 2000);
    } catch (err) {
        console.error('Failed to copy: ', err);
    }
}
</script>
@endpush
@endsection
