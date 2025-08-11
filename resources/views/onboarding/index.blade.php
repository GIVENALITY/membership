<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ Auth::user()->hotel->name ?? 'Membership MS' }} - Onboarding</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    <style>
        :root {
            --hotel-primary-color: {{ Auth::user()->hotel->primary_color ?? '#000000' }};
            --hotel-secondary-color: {{ Auth::user()->hotel->secondary_color ?? '#6c757d' }};
        }
        
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .onboarding-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .welcome-header {
            text-align: center;
            margin-bottom: 3rem;
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .hotel-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-bottom: 1rem;
        }
        
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-left: 5px solid var(--hotel-primary-color);
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }
        
        .feature-icon {
            font-size: 3rem;
            color: var(--hotel-primary-color);
            margin-bottom: 1rem;
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 3rem;
        }
        
        .step {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--hotel-primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin: 0 1rem;
            position: relative;
        }
        
        .step.active {
            background: var(--hotel-primary-color);
            transform: scale(1.1);
        }
        
        .step.completed {
            background: #28a745;
        }
        
        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            right: -60px;
            top: 50%;
            transform: translateY(-50%);
            width: 50px;
            height: 3px;
            background: #dee2e6;
        }
        
        .step.completed:not(:last-child)::after {
            background: #28a745;
        }
        
        .btn-primary {
            background-color: var(--hotel-primary-color) !important;
            border-color: var(--hotel-primary-color) !important;
        }
        
        .btn-primary:hover {
            background-color: {{ Auth::user()->hotel->primary_color ?? '#000000' }}dd !important;
            border-color: {{ Auth::user()->hotel->primary_color ?? '#000000' }}dd !important;
        }
        
        .progress-section {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .quick-action {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .quick-action:hover {
            border-color: var(--hotel-primary-color);
            background: #f8f9fa;
        }
        
        .stats-card {
            background: linear-gradient(135deg, var(--hotel-primary-color) 0%, {{ Auth::user()->hotel->primary_color ?? '#000000' }}dd 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="onboarding-container">
        <!-- Welcome Header -->
        <div class="welcome-header">
            @if(Auth::user()->hotel && Auth::user()->hotel->logo_path)
                <img src="{{ Auth::user()->hotel->logo_url }}" alt="{{ Auth::user()->hotel->name }}" class="hotel-logo">
            @else
                <div class="hotel-logo d-flex align-items-center justify-content-center bg-light rounded">
                    <i class="ri ri-restaurant-line" style="font-size: 2rem; color: var(--hotel-primary-color);"></i>
                </div>
            @endif
            <h1 class="display-4 fw-bold" style="color: var(--hotel-primary-color);">
                {{ Auth::user()->hotel->name ?? 'Membership MS' }} - System Guide
            </h1>
            <p class="lead text-muted">Learn about all the features and capabilities of your restaurant loyalty management system.</p>
            
            <!-- Skip button for existing users -->
            <div class="mt-3">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ri ri-arrow-left-line me-1"></i>
                    Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Progress Steps -->
        <div class="step-indicator">
            <div class="step active">1</div>
            <div class="step">2</div>
            <div class="step">3</div>
            <div class="step">4</div>
            <div class="step">5</div>
        </div>

        <!-- System Overview -->
        <div class="feature-card">
            <div class="row">
                <div class="col-md-8">
                    <h2 class="fw-bold mb-3" style="color: var(--hotel-primary-color);">
                        <i class="ri ri-rocket-line me-2"></i>
                        What Membership MS Can Do For You
                    </h2>
                    <p class="lead">Transform your restaurant with a powerful loyalty program that rewards your customers and drives repeat business.</p>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <h3 class="mb-0">12+</h3>
                        <p class="mb-0">Powerful Features</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feature Grid -->
        <div class="row">
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="feature-card h-100">
                    <div class="feature-icon">
                        <i class="ri ri-user-star-line"></i>
                    </div>
                    <h4>Member Management</h4>
                    <p>Create unlimited member accounts with detailed profiles, preferences, and dining history.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="feature-card h-100">
                    <div class="feature-icon">
                        <i class="ri ri-percent-line"></i>
                    </div>
                    <h4>Smart Discounts</h4>
                    <p>Progressive discount system (5% to 20%) based on visit frequency automatically calculated.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="feature-card h-100">
                    <div class="feature-icon">
                        <i class="ri ri-star-line"></i>
                    </div>
                    <h4>Points System</h4>
                    <p>Earn points for dining visits (1 point per person, min 50k spending). Qualify for enhanced discounts with 5+ points.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="feature-card h-100">
                    <div class="feature-icon">
                        <i class="ri ri-trending-up-line"></i>
                    </div>
                    <h4>Discount Progression</h4>
                    <p>Membership types with visit-based discount increases. VIP members get higher rates faster.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="feature-card h-100">
                    <div class="feature-icon">
                        <i class="ri ri-restaurant-line"></i>
                    </div>
                    <h4>Visit Recording</h4>
                    <p>Two-step process: record visits when customers arrive, process checkout when they leave.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="feature-card h-100">
                    <div class="feature-icon">
                        <i class="ri ri-search-line"></i>
                    </div>
                    <h4>Quick Search</h4>
                    <p>Find members instantly by name, membership ID, phone, or email with real-time results.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="feature-card h-100">
                    <div class="feature-icon">
                        <i class="ri ri-gift-line"></i>
                    </div>
                    <h4>Birthday Alerts</h4>
                    <p>Automatic birthday notifications and personalized email greetings to boost engagement.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="feature-card h-100">
                    <div class="feature-icon">
                        <i class="ri ri-bank-card-line"></i>
                    </div>
                    <h4>Cashier System</h4>
                    <p>Process payments with automatic discount calculation based on membership type and points.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="feature-card h-100">
                    <div class="feature-icon">
                        <i class="ri ri-bar-chart-line"></i>
                    </div>
                    <h4>Analytics & Reports</h4>
                    <p>Comprehensive insights into member behavior, visit patterns, and revenue impact.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="feature-card h-100">
                    <div class="feature-icon">
                        <i class="ri ri-history-line"></i>
                    </div>
                    <h4>Dining History</h4>
                    <p>Complete dining history with points tracking, export functionality, and detailed analytics.</p>
                </div>
            </div>
        </div>

        <!-- Quick Start Actions -->
        <div class="progress-section">
            <h3 class="fw-bold mb-4" style="color: var(--hotel-primary-color);">
                <i class="ri ri-play-circle-line me-2"></i>
                Quick Start Guide
            </h3>
            <div class="row">
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="quick-action" onclick="window.location.href='{{ route('hotel.profile') }}'">
                        <i class="ri ri-palette-line" style="font-size: 2rem; color: var(--hotel-primary-color);"></i>
                        <h5 class="mt-2">Customize Brand</h5>
                        <p class="text-muted small">Upload logo & set colors</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="quick-action" onclick="window.location.href='{{ route('membership-types.index') }}'">
                        <i class="ri ri-vip-crown-line" style="font-size: 2rem; color: var(--hotel-primary-color);"></i>
                        <h5 class="mt-2">Membership Types</h5>
                        <p class="text-muted small">Set up pricing & progression</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="quick-action" onclick="window.location.href='{{ route('members.create') }}'">
                        <i class="ri ri-user-add-line" style="font-size: 2rem; color: var(--hotel-primary-color);"></i>
                        <h5 class="mt-2">Add Members</h5>
                        <p class="text-muted small">Create first member</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="quick-action" onclick="window.location.href='{{ route('dining.index') }}'">
                        <i class="ri ri-restaurant-line" style="font-size: 2rem; color: var(--hotel-primary-color);"></i>
                        <h5 class="mt-2">Record Visit</h5>
                        <p class="text-muted small">Test visit system</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="quick-action" onclick="window.location.href='{{ route('cashier.index') }}'">
                        <i class="ri ri-bank-card-line" style="font-size: 2rem; color: var(--hotel-primary-color);"></i>
                        <h5 class="mt-2">Cashier System</h5>
                        <p class="text-muted small">Process payments</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="quick-action" onclick="window.location.href='{{ route('dining.history') }}'">
                        <i class="ri ri-history-line" style="font-size: 2rem; color: var(--hotel-primary-color);"></i>
                        <h5 class="mt-2">Dining History</h5>
                        <p class="text-muted small">View analytics</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pro Tips -->
        <div class="feature-card">
            <h3 class="fw-bold mb-4" style="color: var(--hotel-primary-color);">
                <i class="ri ri-lightbulb-line me-2"></i>
                Pro Tips for Success
            </h3>
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="ri ri-check-line text-success me-2"></i>Capture member allergies and preferences for better service</li>
                        <li class="mb-2"><i class="ri ri-check-line text-success me-2"></i>Use birthday alerts to send personalized greetings</li>
                        <li class="mb-2"><i class="ri ri-check-line text-success me-2"></i>Regularly review analytics to understand patterns</li>
                        <li class="mb-2"><i class="ri ri-check-line text-success me-2"></i>Set up discount progression rules for each membership type</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="ri ri-check-line text-success me-2"></i>Train staff on the two-step visit recording process</li>
                        <li class="mb-2"><i class="ri ri-check-line text-success me-2"></i>Keep member information updated for accurate communications</li>
                        <li class="mb-2"><i class="ri ri-check-line text-success me-2"></i>Use the search feature to quickly find members during busy times</li>
                        <li class="mb-2"><i class="ri ri-check-line text-success me-2"></i>Monitor points accumulation to encourage repeat visits</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Support & Next Steps -->
        <div class="row">
            <div class="col-md-8">
                <div class="feature-card">
                    <h3 class="fw-bold mb-3" style="color: var(--hotel-primary-color);">
                        <i class="ri ri-customer-service-line me-2"></i>
                        Need Help?
                    </h3>
                    <p>Our support team is here to help you get the most out of Membership MS:</p>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Email:</strong> support@kinara.co.tz</p>
                            <p><strong>Phone:</strong> +255 XXX XXX XXX</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Hours:</strong> Monday - Friday, 8:00 AM - 6:00 PM EAT</p>
                            <p><strong>Response Time:</strong> Within 24 hours</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card text-center">
                    <h4 class="fw-bold mb-3" style="color: var(--hotel-primary-color);">Ready to Start?</h4>
                    <p class="mb-3">Begin managing your restaurant's loyalty program today!</p>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
                        <i class="ri ri-arrow-right-line me-2"></i>
                        Go to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mark onboarding as completed when user clicks "Go to Dashboard"
        document.querySelector('a[href="{{ route("dashboard") }}"]').addEventListener('click', function() {
            // You can add AJAX call here to mark onboarding as completed
            localStorage.setItem('onboarding_completed', 'true');
        });
    </script>
</body>
</html> 