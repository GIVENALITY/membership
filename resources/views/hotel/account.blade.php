@extends('layouts.app')

@section('title', 'Account Settings - Membership MS')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <h5 class="card-header">Account Settings</h5>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('hotel.account.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Account Information -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ old('name', $user->name) }}" required />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ old('email', $user->email) }}" required />
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Password Change Section -->
                        <h6 class="mb-3">Change Password</h6>
                        <p class="text-muted small">Leave password fields empty if you don't want to change your password.</p>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="current_password" class="form-control" name="current_password" 
                                           placeholder="Enter current password" />
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="new_password" class="form-control" name="new_password" 
                                           placeholder="Enter new password" />
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                                <small class="form-text text-muted">Minimum 8 characters</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="new_password_confirmation" class="form-control" name="new_password_confirmation" 
                                           placeholder="Confirm new password" />
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Update Account</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Account Information Card -->
            <div class="card">
                <h5 class="card-header">Account Information</h5>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Role:</strong> <span class="badge bg-primary">{{ ucfirst($user->role) }}</span></p>
                            <p><strong>Account Status:</strong> 
                                @if($user->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </p>
                            <p><strong>Member Since:</strong> {{ $user->created_at->format('F j, Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Last Login:</strong> {{ $user->last_login_at ?? 'Never' }}</p>
                            <p><strong>Hotel:</strong> {{ $user->hotel->name ?? 'Not assigned' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 