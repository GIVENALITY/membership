<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register for {{ $event->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
        }
        .event-image {
            height: 300px;
            object-fit: cover;
            border-radius: 10px;
        }
        .form-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 2rem;
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="display-5 fw-bold mb-3">Register for Event</h1>
                    <p class="lead mb-0">Complete your registration below</p>
                </div>
                <div class="col-md-6 text-center">
                    @if($event->image)
                        <img src="{{ asset('storage/' . $event->image) }}" 
                             class="event-image w-100" 
                             alt="{{ $event->title }}">
                    @else
                        <div class="event-image bg-light d-flex align-items-center justify-content-center">
                            <i class="bx bx-calendar-event bx-lg text-muted"></i>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Registration Form -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="form-section">
                        <h3 class="mb-4">Registration Form</h3>
                        
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('public.events.process-registration', [$event->hotel->slug, $event]) }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Full Name *</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address *</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" value="{{ old('email') }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" name="phone" value="{{ old('phone') }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="number_of_guests" class="form-label">Number of Guests *</label>
                                        <select class="form-select @error('number_of_guests') is-invalid @enderror" 
                                                id="number_of_guests" name="number_of_guests" required>
                                            @for($i = 1; $i <= min(10, $event->getAvailableSpots() === -1 ? 10 : $event->getAvailableSpots()); $i++)
                                                <option value="{{ $i }}" {{ old('number_of_guests') == $i ? 'selected' : '' }}>
                                                    {{ $i }} {{ $i == 1 ? 'Guest' : 'Guests' }}
                                                </option>
                                            @endfor
                                        </select>
                                        @error('number_of_guests')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="special_requests" class="form-label">Special Requests</label>
                                <textarea class="form-control @error('special_requests') is-invalid @enderror" 
                                          id="special_requests" name="special_requests" rows="3" 
                                          placeholder="Any special dietary requirements, accessibility needs, or other requests...">{{ old('special_requests') }}</textarea>
                                @error('special_requests')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('public.events.show', [$event->hotel->slug, $event]) }}" 
                                   class="btn btn-secondary">
                                    <i class="bx bx-arrow-back"></i> Back to Event
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-check"></i> Complete Registration
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $event->title }}</h5>
                            <p class="card-text text-muted">{{ $event->description }}</p>
                            
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
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bx bx-dollar text-primary me-2"></i>
                                                                            <span class="fw-semibold">
                                            {{ $event->price > 0 ? $event->hotel->currency_symbol . number_format($event->price, 2) : 'Free' }} per person
                                        </span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="bx bx-group text-primary me-2"></i>
                                    <span>{{ $event->getAvailableSpots() === -1 ? 'Unlimited' : $event->getAvailableSpots() }} spots available</span>
                                </div>
                            </div>

                            <hr>

                            <div class="text-center">
                                <h6>Registration Summary</h6>
                                <div id="registration-summary">
                                    <p class="text-muted">Select number of guests to see total</p>
                                </div>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const guestsSelect = document.getElementById('number_of_guests');
            const summaryDiv = document.getElementById('registration-summary');
            const eventPrice = {{ $event->price }};
            const currencySymbol = '{{ $event->hotel->currency_symbol ?? "$" }}';

            function updateSummary() {
                const guests = parseInt(guestsSelect.value);
                const total = eventPrice * guests;
                
                const priceDisplay = eventPrice > 0 ? `${currencySymbol}${eventPrice.toFixed(2)}` : 'Free';
                const totalDisplay = total > 0 ? `${currencySymbol}${total.toFixed(2)}` : 'Free';
                
                summaryDiv.innerHTML = `
                    <div class="mb-2">
                        <span class="fw-semibold">${guests} ${guests === 1 ? 'Guest' : 'Guests'}</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">Price per person:</span>
                        <span class="fw-semibold">${priceDisplay}</span>
                    </div>
                    <div class="border-top pt-2">
                        <span class="fw-bold">Total: ${totalDisplay}</span>
                    </div>
                `;
            }

            guestsSelect.addEventListener('change', updateSummary);
            updateSummary();
        });
    </script>
</body>
</html>
