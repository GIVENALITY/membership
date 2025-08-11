@extends('layouts.app')

@section('title', 'My Profile - Membership MS')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <!-- Profile Card -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <img src="{{ $user->avatar_url }}" alt="User Avatar" 
                             class="rounded-circle img-fluid" style="width: 120px; height: 120px; object-fit: cover;">
                    </div>
                    <h5 class="card-title mb-1">{{ $user->full_name }}</h5>
                    <p class="text-muted mb-3">{{ ucfirst($user->role) }}</p>
                    <div class="d-flex justify-content-center">
                        <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }} me-2">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <span class="badge bg-primary">{{ $user->hotel->name ?? 'No Hotel' }}</span>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row text-center">
                        <div class="col-6">
                            <h6 class="mb-0">Member Since</h6>
                            <small class="text-muted">{{ $user->created_at->format('M Y') }}</small>
                        </div>
                        <div class="col-6">
                            <h6 class="mb-0">Last Login</h6>
                            <small class="text-muted">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Information -->
        <div class="col-md-8">
            <div class="card">
                <h5 class="card-header">Profile Information</h5>
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

                    <form action="{{ route('users.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
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

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="phone" name="phone" 
                                       value="{{ old('phone', $user->phone) }}" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="avatar" class="form-label">Profile Picture</label>
                                <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*" />
                                <small class="form-text text-muted">Upload a new profile picture</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" 
                                      rows="3">{{ old('address', $user->address) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="bio" class="form-label">Bio</label>
                            <textarea class="form-control" id="bio" name="bio" 
                                      rows="4" placeholder="Tell us about yourself...">{{ old('bio', $user->bio) }}</textarea>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                            <a href="{{ route('users.change-password') }}" class="btn btn-outline-secondary ms-2">Change Password</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Account Information -->
            <div class="card mt-4">
                <h5 class="card-header">Account Information</h5>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>User ID:</strong> {{ $user->id }}</p>
                            <p><strong>Role:</strong> <span class="badge bg-primary">{{ ucfirst($user->role) }}</span></p>
                            <p><strong>Status:</strong> 
                                @if($user->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Email Verified:</strong> 
                                @if($user->email_verified_at)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-warning">No</span>
                                @endif
                            </p>
                            <p><strong>Hotel:</strong> {{ $user->hotel->name ?? 'Not assigned' }}</p>
                            <p><strong>Joined:</strong> {{ $user->created_at->format('F j, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 