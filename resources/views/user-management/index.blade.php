@extends('layouts.app')

@section('title', 'User Management - ' . (Auth::user()->hotel->name ?? 'Membership MS'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="icon-base ri ri-team-line me-2"></i>
                        User Management
                    </h4>
                    <a href="{{ route('user-management.create') }}" class="btn btn-primary">
                        <i class="icon-base ri ri-user-add-line me-2"></i>
                        Add New User
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Role Information -->
                    <div class="alert alert-info mb-4">
                        <h6><i class="icon-base ri ri-information-line me-2"></i>User Roles & Permissions</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <strong class="text-primary">Manager:</strong>
                                <small class="d-block">Full access to all features</small>
                            </div>
                            <div class="col-md-3">
                                <strong class="text-warning">Cashier:</strong>
                                <small class="d-block">Payment processing & member search</small>
                            </div>
                            <div class="col-md-3">
                                <strong class="text-info">Front Desk:</strong>
                                <small class="d-block">Member check-in & basic operations</small>
                            </div>
                            <div class="col-md-3">
                                <strong class="text-danger">Admin:</strong>
                                <small class="d-block">System administration (you)</small>
                            </div>
                        </div>
                    </div>

                    <!-- Users Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Contact</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3">
                                                    <span class="avatar-initial rounded-circle bg-label-{{ $user->role_badge_color }}">
                                                        {{ substr($user->name, 0, 1) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                                    <small class="text-muted">{{ $user->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $user->role_badge_color }}">
                                                {{ $user->role_display_name }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($user->phone)
                                                <small class="text-muted">{{ $user->phone }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $user->created_at->format('M j, Y') }}</small>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('user-management.edit', $user) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="icon-base ri ri-edit-line"></i>
                                                </a>
                                                
                                                @if($user->id !== auth()->id())
                                                    <form action="{{ route('user-management.toggle-status', $user) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-outline-{{ $user->is_active ? 'warning' : 'success' }}"
                                                                onclick="return confirm('{{ $user->is_active ? 'Deactivate' : 'Activate' }} this user?')">
                                                            <i class="icon-base ri ri-{{ $user->is_active ? 'close' : 'check' }}-line"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <form action="{{ route('user-management.destroy', $user) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                onclick="return confirm('Delete this user? This action cannot be undone.')">
                                                            <i class="icon-base ri ri-delete-bin-line"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-muted small">Current User</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="icon-base ri ri-team-line icon-3x text-muted mb-3"></i>
                                            <h6>No Users Found</h6>
                                            <p class="text-muted">Start by adding your first team member</p>
                                            <a href="{{ route('user-management.create') }}" class="btn btn-primary">
                                                <i class="icon-base ri ri-user-add-line me-2"></i>
                                                Add First User
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 