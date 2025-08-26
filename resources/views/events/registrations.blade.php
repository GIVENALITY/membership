@extends('layouts.app')

@section('title', 'Event Registrations - ' . $event->title)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Registrations for: {{ $event->title }}</h5>
                        <small class="text-muted">{{ $event->start_date->format('M d, Y g:i A') }}</small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('events.export-registrations', $event) }}" class="btn btn-success">
                            <i class="bx bx-download"></i> Export CSV
                        </a>
                        <a href="{{ route('events.show', $event) }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back"></i> Back to Event
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Registration Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h4 class="mb-0">{{ $registrations->total() }}</h4>
                                    <small>Total Registrations</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h4 class="mb-0">{{ $registrations->where('status', 'confirmed')->count() }}</h4>
                                    <small>Confirmed</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h4 class="mb-0">{{ $registrations->where('status', 'pending')->count() }}</h4>
                                    <small>Pending</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h4 class="mb-0">{{ $registrations->sum('number_of_guests') }}</h4>
                                    <small>Total Guests</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($registrations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Registration Code</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Guests</th>
                                        <th>Total Amount</th>
                                        <th>Status</th>
                                        <th>Registered</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($registrations as $registration)
                                    <tr>
                                        <td>
                                            <code class="text-primary">{{ $registration->registration_code }}</code>
                                        </td>
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
                                                        <small class="text-success">Member</small>
                                                    @else
                                                        <small class="text-muted">External</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $registration->email }}</td>
                                        <td>{{ $registration->phone ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $registration->number_of_guests }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold">
                                                {{ $registration->total_amount > 0 ? (Auth::user()->hotel->currency_symbol ?? '$') . number_format($registration->total_amount, 2) : 'Free' }}
                                            </span>
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
                                                    <li>
                                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#registrationModal{{ $registration->id }}">
                                                            <i class="bx bx-show me-1"></i> View Details
                                                        </a>
                                                    </li>
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
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $registrations->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bx bx-user-plus bx-lg text-muted mb-3"></i>
                            <h5 class="text-muted">No registrations found</h5>
                            <p class="text-muted">Registrations will appear here once people start signing up.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Registration Detail Modals -->
@foreach($registrations as $registration)
<div class="modal fade" id="registrationModal{{ $registration->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registration Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Personal Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td>{{ $registration->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $registration->email }}</td>
                            </tr>
                            <tr>
                                <td><strong>Phone:</strong></td>
                                <td>{{ $registration->phone ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Registration Code:</strong></td>
                                <td><code>{{ $registration->registration_code }}</code></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Event Details</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Number of Guests:</strong></td>
                                <td>{{ $registration->number_of_guests }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Amount:</strong></td>
                                                                            <td>{{ $registration->total_amount > 0 ? (Auth::user()->hotel->currency_symbol ?? '$') . number_format($registration->total_amount, 2) : 'Free' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <span class="{{ $registration->getStatusBadgeClass() }}">
                                        {{ $registration->getStatusText() }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Registered:</strong></td>
                                <td>{{ $registration->registered_at->format('M d, Y g:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                @if($registration->special_requests)
                <div class="mt-3">
                    <h6>Special Requests</h6>
                    <p class="text-muted">{{ $registration->special_requests }}</p>
                </div>
                @endif

                @if($registration->guest_details)
                <div class="mt-3">
                    <h6>Additional Guest Details</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($registration->guest_details as $guest)
                                <tr>
                                    <td>{{ $guest['name'] ?? 'N/A' }}</td>
                                    <td>{{ $guest['email'] ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
