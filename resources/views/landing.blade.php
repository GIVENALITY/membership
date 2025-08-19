<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant MS - Complete Membership Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --dark-color: #343a40;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
        }

        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, #0056b3 100%);
            color: white;
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .feature-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1.5rem;
        }

        .stats-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 80px 0;
        }

        .stat-card {
            text-align: center;
            padding: 2rem;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .benefit-section {
            padding: 80px 0;
        }

        .benefit-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 2rem;
        }

        .benefit-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--success-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .cta-section {
            background: linear-gradient(135deg, var(--dark-color) 0%, #495057 100%);
            color: white;
            padding: 80px 0;
        }

        .demo-section {
            background: #f8f9fa;
            padding: 80px 0;
        }

        .demo-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .pricing-section {
            padding: 80px 0;
        }

        .pricing-card {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            transition: border-color 0.3s ease;
            height: 100%;
        }

        .pricing-card.featured {
            border-color: var(--primary-color);
            transform: scale(1.05);
        }

        .btn-custom {
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-primary-custom {
            background: var(--primary-color);
            border: none;
            color: white;
        }

        .btn-primary-custom:hover {
            background: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,123,255,0.3);
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .section-subtitle {
            font-size: 1.2rem;
            color: #6c757d;
            margin-bottom: 3rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="ri ri-restaurant-line me-2"></i>
                Restaurant MS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#benefits">Benefits</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#demo">Demo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#pricing">Pricing</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary-custom btn-custom ms-2" href="/login">Get Started</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">
                        Transform Your Restaurant with Smart Membership Management
                    </h1>
                    <p class="lead mb-4">
                        Streamline operations, boost customer loyalty, and increase revenue with our comprehensive restaurant membership system. 
                        Everything you need to manage members, track visits, and reward loyal customers.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="/register" class="btn btn-light btn-custom">
                            <i class="ri ri-rocket-line me-2"></i>
                            Start Free Trial
                        </a>
                        <a href="#demo" class="btn btn-outline-light btn-custom">
                            <i class="ri ri-play-circle-line me-2"></i>
                            Watch Demo
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="position-relative">
                        <div class="bg-white rounded-3 p-4 shadow-lg">
                            <i class="ri ri-restaurant-line" style="font-size: 4rem; color: var(--primary-color);"></i>
                            <h4 class="mt-3">Restaurant Management System</h4>
                            <p class="text-muted">Complete solution for modern restaurants</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number">500+</div>
                        <div class="text-muted">Members Managed</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number">50%</div>
                        <div class="text-muted">Increase in Customer Retention</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number">24/7</div>
                        <div class="text-muted">System Availability</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number">100%</div>
                        <div class="text-muted">Cloud-Based Solution</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Powerful Features</h2>
                <p class="section-subtitle">Everything you need to manage your restaurant membership program</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card">
                        <div class="card-body text-center">
                            <div class="feature-icon bg-primary text-white">
                                <i class="ri ri-team-line"></i>
                            </div>
                            <h5 class="card-title">Member Management</h5>
                            <p class="card-text">
                                Complete member profiles with preferences, dietary restrictions, and visit history. 
                                Import members in bulk and manage their membership types.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card">
                        <div class="card-body text-center">
                            <div class="feature-icon bg-success text-white">
                                <i class="ri ri-restaurant-line"></i>
                            </div>
                            <h5 class="card-title">Dining Management</h5>
                            <p class="card-text">
                                Record visits, track spending, and automatically calculate discounts. 
                                Manage table reservations and dining preferences.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card">
                        <div class="card-body text-center">
                            <div class="feature-icon bg-warning text-white">
                                <i class="ri ri-star-line"></i>
                            </div>
                            <h5 class="card-title">Points & Rewards</h5>
                            <p class="card-text">
                                Automated points system with customizable rewards. 
                                Birthday bonuses, consecutive visit rewards, and discount progression.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card">
                        <div class="card-body text-center">
                            <div class="feature-icon bg-info text-white">
                                <i class="ri ri-card-line"></i>
                            </div>
                            <h5 class="card-title">Virtual & Physical Cards</h5>
                            <p class="card-text">
                                Generate custom membership cards with your branding. 
                                Track physical card issuance and delivery status.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card">
                        <div class="card-body text-center">
                            <div class="feature-icon bg-secondary text-white">
                                <i class="ri ri-file-chart-line"></i>
                            </div>
                            <h5 class="card-title">Analytics & Reports</h5>
                            <p class="card-text">
                                Comprehensive reports on member activity, revenue, and trends. 
                                Export data for business analysis and planning.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-card">
                        <div class="card-body text-center">
                            <div class="feature-icon bg-dark text-white">
                                <i class="ri ri-settings-3-line"></i>
                            </div>
                            <h5 class="card-title">Customizable Settings</h5>
                            <p class="card-text">
                                Configure roles, enable/disable features, and customize the system 
                                to match your restaurant's specific needs.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section id="benefits" class="benefit-section bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Why Choose Restaurant MS?</h2>
                <p class="section-subtitle">Transform your restaurant operations and boost customer loyalty</p>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="ri ri-line-chart-line"></i>
                        </div>
                        <div>
                            <h5>Increase Revenue</h5>
                            <p>Boost customer retention and average spending through targeted rewards and personalized experiences.</p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="ri ri-time-line"></i>
                        </div>
                        <div>
                            <h5>Save Time</h5>
                            <p>Automate membership management, reduce manual work, and focus on what matters most - your customers.</p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="ri ri-user-heart-line"></i>
                        </div>
                        <div>
                            <h5>Enhance Customer Experience</h5>
                            <p>Remember preferences, track visit history, and provide personalized service that keeps customers coming back.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="ri ri-shield-check-line"></i>
                        </div>
                        <div>
                            <h5>Secure & Reliable</h5>
                            <p>Cloud-based system with 99.9% uptime, secure data storage, and regular backups to protect your business.</p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="ri ri-smartphone-line"></i>
                        </div>
                        <div>
                            <h5>Mobile Friendly</h5>
                            <p>Access your system from anywhere with our responsive design that works on all devices.</p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="ri ri-customer-service-line"></i>
                        </div>
                        <div>
                            <h5>24/7 Support</h5>
                            <p>Get help whenever you need it with our dedicated support team and comprehensive documentation.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Demo Section -->
    <section id="demo" class="demo-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">See It In Action</h2>
                <p class="section-subtitle">Explore the key features of our restaurant management system</p>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <div class="demo-card">
                        <h5><i class="ri ri-team-line me-2 text-primary"></i>Member Management</h5>
                        <ul class="list-unstyled">
                            <li><i class="ri ri-check-line text-success me-2"></i>Complete member profiles</li>
                            <li><i class="ri ri-check-line text-success me-2"></i>Bulk member import</li>
                            <li><i class="ri ri-check-line text-success me-2"></i>Dietary preferences</li>
                            <li><i class="ri ri-check-line text-success me-2"></i>Emergency contacts</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="demo-card">
                        <h5><i class="ri ri-restaurant-line me-2 text-success"></i>Dining Operations</h5>
                        <ul class="list-unstyled">
                            <li><i class="ri ri-check-line text-success me-2"></i>Visit recording</li>
                            <li><i class="ri ri-check-line text-success me-2"></i>Automatic discounts</li>
                            <li><i class="ri ri-check-line text-success me-2"></i>Receipt management</li>
                            <li><i class="ri ri-check-line text-success me-2"></i>Payment processing</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="demo-card">
                        <h5><i class="ri ri-star-line me-2 text-warning"></i>Rewards System</h5>
                        <ul class="list-unstyled">
                            <li><i class="ri ri-check-line text-success me-2"></i>Points accumulation</li>
                            <li><i class="ri ri-check-line text-success me-2"></i>Birthday bonuses</li>
                            <li><i class="ri ri-check-line text-success me-2"></i>Visit progression</li>
                            <li><i class="ri ri-check-line text-success me-2"></i>Custom rewards</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="pricing-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Simple, Transparent Pricing</h2>
                <p class="section-subtitle">Choose the plan that fits your restaurant's needs</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="pricing-card">
                        <h4>Starter</h4>
                        <div class="display-4 fw-bold text-primary mb-3">$49</div>
                        <div class="text-muted mb-4">per month</div>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="ri ri-check-line text-success me-2"></i>Up to 100 members</li>
                            <li class="mb-2"><i class="ri ri-check-line text-success me-2"></i>Basic member management</li>
                            <li class="mb-2"><i class="ri ri-check-line text-success me-2"></i>Points system</li>
                            <li class="mb-2"><i class="ri ri-check-line text-success me-2"></i>Email support</li>
                        </ul>
                        <a href="/register" class="btn btn-outline-primary btn-custom w-100">Get Started</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="pricing-card featured">
                        <div class="badge bg-primary mb-3">Most Popular</div>
                        <h4>Professional</h4>
                        <div class="display-4 fw-bold text-primary mb-3">$99</div>
                        <div class="text-muted mb-4">per month</div>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="ri ri-check-line text-success me-2"></i>Up to 500 members</li>
                            <li class="mb-2"><i class="ri ri-check-line text-success me-2"></i>Advanced features</li>
                            <li class="mb-2"><i class="ri ri-check-line text-success me-2"></i>Virtual & physical cards</li>
                            <li class="mb-2"><i class="ri ri-check-line text-success me-2"></i>Analytics & reports</li>
                            <li class="mb-2"><i class="ri ri-check-line text-success me-2"></i>Priority support</li>
                        </ul>
                        <a href="/register" class="btn btn-primary-custom btn-custom w-100">Get Started</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="pricing-card">
                        <h4>Enterprise</h4>
                        <div class="display-4 fw-bold text-primary mb-3">$199</div>
                        <div class="text-muted mb-4">per month</div>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="ri ri-check-line text-success me-2"></i>Unlimited members</li>
                            <li class="mb-2"><i class="ri ri-check-line text-success me-2"></i>All features included</li>
                            <li class="mb-2"><i class="ri ri-check-line text-success me-2"></i>Custom integrations</li>
                            <li class="mb-2"><i class="ri ri-check-line text-success me-2"></i>Dedicated support</li>
                            <li class="mb-2"><i class="ri ri-check-line text-success me-2"></i>SLA guarantee</li>
                        </ul>
                        <a href="/register" class="btn btn-outline-primary btn-custom w-100">Contact Sales</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container text-center">
            <h2 class="mb-4">Ready to Transform Your Restaurant?</h2>
            <p class="lead mb-4">
                Join hundreds of restaurants that have already improved their operations and customer loyalty with Restaurant MS.
            </p>
            <div class="d-flex justify-content-center gap-3">
                <a href="/register" class="btn btn-light btn-custom">
                    <i class="ri ri-rocket-line me-2"></i>
                    Start Free Trial
                </a>
                <a href="/login" class="btn btn-outline-light btn-custom">
                    <i class="ri ri-login-box-line me-2"></i>
                    Login
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="ri ri-restaurant-line me-2"></i>Restaurant MS</h5>
                    <p class="text-muted">Complete membership management system for modern restaurants.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted mb-0">&copy; 2024 Restaurant MS. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
