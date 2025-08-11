@extends('layouts.app')

@section('title', 'Email Templates Settings - ' . (Auth::user()->hotel->name ?? 'Membership MS'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="icon-base ri ri-mail-line me-2"></i>
                        Email Templates Settings
                    </h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Email Configuration Overview -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="icon-base ri ri-information-line me-2"></i>
                            Email Configuration Overview
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">Current Email Settings</h6>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                            <li><i class="icon-base ri ri-check-line text-primary me-2"></i>Welcome emails sent on member registration</li>
                                            <li><i class="icon-base ri ri-check-line text-primary me-2"></i>Birthday emails sent automatically</li>
                                            <li><i class="icon-base ri ri-check-line text-primary me-2"></i>Membership cards attached to welcome emails</li>
                                            <li><i class="icon-base ri ri-check-line text-primary me-2"></i>Hotel branding included in emails</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">Email Features</h6>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                            <li><i class="icon-base ri ri-check-line text-info me-2"></i>Customizable email templates</li>
                                            <li><i class="icon-base ri ri-check-line text-info me-2"></i>Automatic email scheduling</li>
                                            <li><i class="icon-base ri ri-check-line text-info me-2"></i>Email delivery tracking</li>
                                            <li><i class="icon-base ri ri-check-line text-info me-2"></i>Hotel-specific branding</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email Templates -->
                    <div class="mb-4">
                        <h5 class="text-success mb-3">
                            <i class="icon-base ri ri-file-text-line me-2"></i>
                            Email Templates
                        </h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-success">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0">Welcome Email Template</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted small">Sent to new members upon registration</p>
                                        <div class="mb-3">
                                            <strong>Subject:</strong> Welcome to {{ Auth::user()->hotel->name ?? 'Our Restaurant' }}!
                                        </div>
                                        <div class="mb-3">
                                            <strong>Includes:</strong>
                                            <ul class="list-unstyled small">
                                                <li>• Personalized welcome message</li>
                                                <li>• Membership card attachment</li>
                                                <li>• Membership type details</li>
                                                <li>• Points system explanation</li>
                                                <li>• Hotel contact information</li>
                                            </ul>
                                        </div>
                                        <button type="button" class="btn btn-outline-success btn-sm">
                                            <i class="icon-base ri ri-edit-line me-1"></i>
                                            Customize Template
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-warning">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="mb-0">Birthday Email Template</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted small">Sent to members on their birthday</p>
                                        <div class="mb-3">
                                            <strong>Subject:</strong> Happy Birthday from {{ Auth::user()->hotel->name ?? 'Our Restaurant' }}!
                                        </div>
                                        <div class="mb-3">
                                            <strong>Includes:</strong>
                                            <ul class="list-unstyled small">
                                                <li>• Birthday greetings</li>
                                                <li>• Special birthday discount offer</li>
                                                <li>• Points bonus information</li>
                                                <li>• Reservation invitation</li>
                                                <li>• Hotel contact information</li>
                                            </ul>
                                        </div>
                                        <button type="button" class="btn btn-outline-warning btn-sm">
                                            <i class="icon-base ri ri-edit-line me-1"></i>
                                            Customize Template
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email Statistics -->
                    <div class="mb-4">
                        <h5 class="text-info mb-3">
                            <i class="icon-base ri ri-bar-chart-line me-2"></i>
                            Email Statistics
                        </h5>
                        <div class="row">
                            @php
                                try {
                                    $welcomeEmails = \App\Models\EmailNotification::where('type', 'welcome')->count();
                                    $birthdayEmails = \App\Models\EmailNotification::where('type', 'birthday')->count();
                                    $deliveredEmails = \App\Models\EmailNotification::where('status', 'sent')->count();
                                    $pendingEmails = \App\Models\EmailNotification::where('status', 'pending')->count();
                                } catch (\Exception $e) {
                                    $welcomeEmails = 0;
                                    $birthdayEmails = 0;
                                    $deliveredEmails = 0;
                                    $pendingEmails = 0;
                                }
                            @endphp
                            <div class="col-md-3 mb-3">
                                <div class="card border-success">
                                    <div class="card-body text-center">
                                        <h3 class="text-success">{{ $welcomeEmails }}</h3>
                                        <p class="mb-0 text-muted">Welcome Emails Sent</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card border-warning">
                                    <div class="card-body text-center">
                                        <h3 class="text-warning">{{ $birthdayEmails }}</h3>
                                        <p class="mb-0 text-muted">Birthday Emails Sent</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card border-info">
                                    <div class="card-body text-center">
                                        <h3 class="text-info">{{ $deliveredEmails }}</h3>
                                        <p class="mb-0 text-muted">Emails Delivered</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card border-primary">
                                    <div class="card-body text-center">
                                        <h3 class="text-primary">{{ $pendingEmails }}</h3>
                                        <p class="mb-0 text-muted">Pending Emails</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email Management Actions -->
                    <div class="mb-4">
                        <h5 class="text-dark mb-3">
                            <i class="icon-base ri ri-tools-line me-2"></i>
                            Email Management Actions
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">Template Management</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <button type="button" class="btn btn-outline-primary btn-sm">
                                                <i class="icon-base ri ri-edit-line me-2"></i>
                                                Edit Welcome Template
                                            </button>
                                            <button type="button" class="btn btn-outline-warning btn-sm">
                                                <i class="icon-base ri ri-edit-line me-2"></i>
                                                Edit Birthday Template
                                            </button>
                                            <button type="button" class="btn btn-outline-info btn-sm">
                                                <i class="icon-base ri ri-add-line me-2"></i>
                                                Create New Template
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-secondary">
                                    <div class="card-header bg-secondary text-white">
                                        <h6 class="mb-0">Email Operations</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <button type="button" class="btn btn-outline-secondary btn-sm">
                                                <i class="icon-base ri ri-send-plane-line me-2"></i>
                                                Send Test Email
                                            </button>
                                            <a href="{{ route('members.index') }}" class="btn btn-outline-info btn-sm">
                                                <i class="icon-base ri ri-mail-line me-2"></i>
                                                View Member Emails
                                            </a>
                                            <a href="{{ route('dining.history') }}" class="btn btn-outline-success btn-sm">
                                                <i class="icon-base ri ri-bar-chart-line me-2"></i>
                                                Email Analytics
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email Configuration -->
                    <div class="mb-4">
                        <h5 class="text-warning mb-3">
                            <i class="icon-base ri ri-settings-4-line me-2"></i>
                            Email Configuration
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="mb-0">Email Settings</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">From Email Address</label>
                                            <input type="email" class="form-control" value="{{ Auth::user()->hotel->email ?? 'noreply@example.com' }}" readonly>
                                            <small class="text-muted">This is the email address that appears as the sender</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">From Name</label>
                                            <input type="text" class="form-control" value="{{ Auth::user()->hotel->name ?? 'Membership MS' }}" readonly>
                                            <small class="text-muted">This is the name that appears as the sender</small>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="auto_send_emails" checked>
                                                <label class="form-check-label" for="auto_send_emails">
                                                    Automatically send welcome emails
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="auto_birthday_emails" checked>
                                                <label class="form-check-label" for="auto_birthday_emails">
                                                    Automatically send birthday emails
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">Email Preview</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="mb-2">
                                                <strong>From:</strong> {{ Auth::user()->hotel->name ?? 'Membership MS' }} &lt;{{ Auth::user()->hotel->email ?? 'noreply@example.com' }}&gt;
                                            </div>
                                            <div class="mb-2">
                                                <strong>To:</strong> member@example.com
                                            </div>
                                            <div class="mb-2">
                                                <strong>Subject:</strong> Welcome to {{ Auth::user()->hotel->name ?? 'Our Restaurant' }}!
                                            </div>
                                            <div class="mb-2">
                                                <strong>Message:</strong>
                                                <p class="text-muted small mb-0">
                                                    Dear [Member Name],<br>
                                                    Welcome to {{ Auth::user()->hotel->name ?? 'Our Restaurant' }}! We're excited to have you as a member of our loyalty program...
                                                </p>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-outline-info btn-sm mt-2">
                                            <i class="icon-base ri ri-eye-line me-1"></i>
                                            Preview Full Email
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email System Information -->
                    <div class="alert alert-info">
                        <h6><i class="icon-base ri ri-information-line me-2"></i>Email System Information</h6>
                        <p class="mb-0">
                            The email system automatically sends welcome emails to new members and birthday emails to existing members. 
                            All emails include your hotel's branding and can be customized to match your business needs. 
                            Email delivery is tracked and you can view the history of all sent emails.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 