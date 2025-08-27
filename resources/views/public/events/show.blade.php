<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->title }} - {{ $event->hotel->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4rem 0;
        }
        .event-image {
            height: 400px;
            object-fit: cover;
            border-radius: 10px;
        }
        .feature-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .status-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 10;
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-3">{{ $event->title }}</h1>
                    <p class="lead mb-4">{{ Str::limit($event->description, 200) }}</p>
                    <div class="d-flex align-items-center mb-3">
                        <i class="bx bx-calendar text-white me-2"></i>
                        <span class="fw-semibold">{{ $event->start_date->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex align-items-center mb-4">
                        <i class="bx bx-time text-white me-2"></i>
                        <span>{{ $event->getFormattedTimeRange() }}</span>
                    </div>
                    @if(!$event->isFull())
                        <a href="{{ route('public.events.register', [$event->hotel->slug, $event]) }}" class="btn btn-light btn-lg">
                            <i class="bx bx-user-plus"></i> Register Now
                        </a>
                    @else
                        <button class="btn btn-secondary btn-lg" disabled>
                            <i class="bx bx-x-circle"></i> Event Full
                        </button>
                    @endif
                </div>
                <div class="col-lg-6">
                    @if($event->image)
                        <img src="{{ asset('storage/' . $event->image) }}" 
                             class="event-image w-100" 
                             alt="{{ $event->title }}">
                    @else
                        <div class="event-image bg-light d-flex align-items-center justify-content-center">
                            <i class="bx bx-calendar-event bx-lg text-muted"></i>
                        </div>
                    @endif
                    
                    @if($event->isFull())
                        <div class="status-badge">
                            <span class="badge bg-danger fs-6">Event Full</span>
                        </div>
                    @else
                        <div class="status-badge">
                            <span class="badge bg-success fs-6">Available</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Event Details -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card feature-card mb-4">
                        <div class="card-body p-4">
                            <h3 class="card-title mb-4">About This Event</h3>
                            <p class="card-text">{{ $event->description }}</p>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card feature-card h-100">
                                <div class="card-body p-4 text-center">
                                    <i class="bx bx-map-pin bx-lg text-primary mb-3"></i>
                                    <h5 class="card-title">Location</h5>
                                    <p class="card-text">{{ $event->location ?? 'To be announced' }}</p>
                                </div>
                            </div>
                        </div>
                                                            <div class="col-md-6">
                                        <div class="card feature-card h-100">
                                            <div class="card-body p-4 text-center">
                                                <i class="bx bx-dollar bx-lg text-primary mb-3"></i>
                                                <h5 class="card-title">Price</h5>
                                                <p class="card-text fw-bold">
                                                    {{ $event->price > 0 ? $event->hotel->currency_symbol . number_format($event->price, 2) : 'Free' }} per person
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                        <div class="col-md-6">
                            <div class="card feature-card h-100">
                                <div class="card-body p-4 text-center">
                                    <i class="bx bx-group bx-lg text-primary mb-3"></i>
                                    <h5 class="card-title">Capacity</h5>
                                    <p class="card-text">
                                        @if($event->max_capacity)
                                            {{ $availableSpots }}/{{ $event->max_capacity }} spots available
                                        @else
                                            Unlimited capacity
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card feature-card sticky-top" style="top: 20px;">
                        <div class="card-body p-4">
                            <h4 class="card-title mb-4">Event Summary</h4>
                            
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bx bx-calendar text-primary me-2"></i>
                                    <span class="fw-semibold">{{ $event->start_date->format('M d, Y') }}</span>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bx bx-time text-primary me-2"></i>
                                    <span>{{ $event->getFormattedTimeRange() }}</span>
                                </div>
                            </div>

                            @if($event->location)
                            <div class="mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bx bx-map-pin text-primary me-2"></i>
                                    <span>{{ $event->location }}</span>
                                </div>
                            </div>
                            @endif

                            <div class="mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bx bx-dollar text-primary me-2"></i>
                                    <span class="fw-semibold">
                                        {{ $event->price > 0 ? $event->hotel->currency_symbol . number_format($event->price, 2) : 'Free' }} per person
                                    </span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex align-items-center">
                                    <i class="bx bx-group text-primary me-2"></i>
                                    <span>
                                        @if($event->max_capacity)
                                            {{ $availableSpots }} spots left
                                        @else
                                            Unlimited spots available
                                        @endif
                                    </span>
                                </div>
                            </div>

                            @if($event->isRegistrationClosed())
                                <button class="btn btn-danger btn-lg w-100 mb-2" disabled>
                                    <i class="bx bx-lock"></i> Registration Closed
                                </button>
                            @elseif(!$event->isFull())
                                <a href="{{ route('public.events.register', [$event->hotel->slug, $event]) }}" 
                                   class="btn btn-primary btn-lg w-100 mb-2">
                                    <i class="bx bx-user-plus"></i> Register Now
                                </a>
                            @else
                                <button class="btn btn-secondary btn-lg w-100 mb-2" disabled>
                                    <i class="bx bx-x-circle"></i> Event Full
                                </button>
                            @endif

                            <div class="text-center">
                                <small class="text-muted">
                                    <i class="bx bx-shield-check me-1"></i>
                                    Secure registration process
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h6>{{ $event->hotel->name }}</h6>
                    <p class="mb-0">Event registration system</p>
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
