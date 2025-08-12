@extends('layouts.app')

@section('title', __('app.all_users'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="icon-base ri ri-team-line me-2"></i>
                        {{ __('app.all_users') }}
                    </h4>
                    <p class="card-subtitle text-muted">{{ __('app.manage_all_users_in_system') }}</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('app.user') }}</th>
                                    <th>{{ __('app.email') }}</th>
                                    <th>{{ __('app.role') }}</th>
                                    <th>{{ __('app.hotel') }}</th>
                                    <th>{{ __('app.status') }}</th>
                                    <th>{{ __('app.created') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                @if($user->avatar_path)
                                                    <img src="{{ asset('storage/' . $user->avatar_path) }}" 
                                                         alt="{{ $user->name }}" 
                                                         class="rounded-circle">
                                                @else
                                                    <span class="avatar-initial rounded bg-label-primary">
                                                        {{ substr($user->name, 0, 1) }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $user->name }}</h6>
                                                <small class="text-muted">{{ $user->phone ?? __('app.no_phone') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-{{ $user->role_badge_color }}">
                                            {{ $user->role_display_name }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($user->hotel)
                                            <span class="text-primary">{{ $user->hotel->name }}</span>
                                        @else
                                            <span class="text-muted">{{ __('app.no_hotel') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->is_active)
                                            <span class="badge bg-success">{{ __('app.active') }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ __('app.inactive') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="icon-base ri ri-team-line fs-1 mb-3"></i>
                                            <h6>{{ __('app.no_users_found') }}</h6>
                                            <p>{{ __('app.no_users_description') }}</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($users->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 