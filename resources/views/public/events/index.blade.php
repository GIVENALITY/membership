<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - {{ $hotelSlug ? 'Hotel Events' : 'All Events' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <style>
        .event-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .event-image {
            height: 200px;
            object-fit: cover;
        }
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4rem 0;
        }
        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">
                        @if($hotelSlug)
                            Upcoming Events
                        @else
                            Discover Amazing Events
                        @endif
                    </h1>
                    <p class="lead mb-4">
                        @if($hotelSlug)
                            Join us for exciting events and experiences
                        @else
                            Browse and register for events from our partner hotels
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Events Section -->
    <section class="py-5">
        <div class="container">
            @if($events->count() > 0)
                <div class="row g-4">
                    @foreach($events as $event)
                    <div class="col-md-6 col-lg-4">
                        <div class="card event-card h-100">
                            @if($event->image)
                                <img src="{{ asset('storage/' . $event->image) }}" 
                                     class="card-img-top event-image" 
                                     alt="{{ $event->title }}">
                            @else
                                <div class="card-img-top event-image bg-light d-flex align-items-center justify-content-center">
                                    <i class="bx bx-calendar-event bx-lg text-muted"></i>
                                </div>
                            @endif
                            
                            <div class="status-badge">
                                @if($event->isFull())
                                    <span class="badge bg-danger">Full</span>
                                @else
                                    <span class="badge bg-success">Available</span>
                                @endif
                            </div>

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $event->title }}</h5>
                                <p class="card-text text-muted">
                                    {{ Str::limit($event->description, 100) }}
                                </p>
                                
                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bx bx-calendar text-primary me-2"></i>
                                        <span class="fw-semibold">{{ $event->start_date->format('M d, Y') }}</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bx bx-time text-primary me-2"></i>
                                        <span>{{ $event->start_date->format('g:i A') }} - {{ $event->end_date->format('g:i A') }}</span>
                                    </div>
                                    @if($event->location)
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bx bx-map-pin text-primary me-2"></i>
                                        <span>{{ $event->location }}</span>
                                    </div>
                                    @endif
                                    <div class="d-flex align-items-center">
                                        <i class="bx bx-dollar text-primary me-2"></i>
                                        <span class="fw-semibold">
                                            {{ $event->price > 0 ? '$' . number_format($event->price, 2) : 'Free' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-auto">
                                    @if($event->isFull())
                                        <button class="btn btn-secondary w-100" disabled>
                                            Event Full
                                        </button>
                                    @else
                                        <a href="{{ route('public.events.show', [$event->hotel->slug, $event]) }}" 
                                           class="btn btn-primary w-100">
                                            View Details
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="card-footer bg-transparent">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="bx bx-group me-1"></i>
                                        {{ $event->confirmedRegistrations()->count() }} registered
                                    </small>
                                    @if($event->max_capacity)
                                    <small class="text-muted">
                                        {{ $event->getAvailableSpots() }} spots left
                                    </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-5">
                    {{ $events->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bx bx-calendar-event bx-lg text-muted mb-3"></i>
                    <h4 class="text-muted">No events available</h4>
                    <p class="text-muted">Check back later for upcoming events.</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h6>Event Registration System</h6>
                    <p class="mb-0">Easy event registration and management</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">&copy; {{ date('Y') }} All rights reserved</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
