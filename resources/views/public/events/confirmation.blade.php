<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Confirmed - {{ $event->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 4rem 0;
        }
        .confirmation-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .success-icon {
            font-size: 4rem;
            color: #28a745;
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <i class="bx bx-check-circle success-icon mb-3"></i>
                    <h1 class="display-4 fw-bold mb-3">Registration Confirmed!</h1>
                    <p class="lead mb-0">Thank you for registering for {{ $event->title }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Confirmation Details -->
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card confirmation-card">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <h3 class="card-title text-success">Registration Successful</h3>
                                <p class="text-muted">Your registration has been received and is being processed.</p>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3">Registration Details</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Registration Code:</strong></td>
                                            <td><code class="text-primary">{{ $registration->registration_code }}</code></td>
                                        </tr>
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
                                            <td>{{ $registration->phone ?? 'Not provided' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Number of Guests:</strong></td>
                                            <td>{{ $registration->number_of_guests }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Amount:</strong></td>
                                            <td class="fw-bold">
                                                {{ $registration->total_amount > 0 ? $event->hotel->currency_symbol . number_format($registration->total_amount, 2) : 'Free' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="{{ $registration->getStatusBadgeClass() }}">
                                                    {{ $registration->getStatusText() }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3">Event Information</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Event:</strong></td>
                                            <td>{{ $event->title }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date:</strong></td>
                                            <td>{{ $event->start_date->format('M d, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Time:</strong></td>
                                            <td>{{ $event->start_date->format('g:i A') }} - {{ $event->end_date->format('g:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Location:</strong></td>
                                            <td>{{ $event->location ?? 'To be announced' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Venue:</strong></td>
                                            <td>{{ $event->hotel->name }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($registration->special_requests)
                            <div class="mt-4">
                                <h6 class="fw-bold">Special Requests</h6>
                                <p class="text-muted">{{ $registration->special_requests }}</p>
                            </div>
                            @endif

                            <div class="alert alert-info mt-4">
                                <h6 class="alert-heading">
                                    <i class="bx bx-info-circle me-2"></i>What happens next?
                                </h6>
                                <ul class="mb-0">
                                    <li>You will receive a confirmation email shortly</li>
                                    <li>Keep your registration code safe for future reference</li>
                                    <li>We'll send you event reminders as the date approaches</li>
                                    <li>Contact us if you need to make any changes</li>
                                </ul>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                                <a href="{{ route('public.events.show', [$event->hotel->slug, $event]) }}" 
                                   class="btn btn-outline-primary">
                                    <i class="bx bx-arrow-back"></i> Back to Event
                                </a>
                                <a href="{{ route('public.events.search', $event->hotel->slug) }}" 
                                   class="btn btn-outline-secondary">
                                    <i class="bx bx-search"></i> Search Registration
                                </a>
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
