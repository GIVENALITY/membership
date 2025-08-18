@extends('layouts.app')

@section('title', __('app.all_hotels'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="icon-base ri ri-building-line me-2"></i>
                        {{ __('app.all_hotels') }}
                    </h4>
                    <p class="card-subtitle text-muted">{{ __('app.manage_all_hotels_in_system') }}</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('app.hotel_name') }}</th>
                                    <th>{{ __('app.email') }}</th>
                                    <th>{{ __('app.phone') }}</th>
                                    <th>{{ __('app.city') }}</th>
                                    <th>{{ __('app.status') }}</th>
                                    <th>{{ __('app.users') }}</th>
                                    <th>{{ __('app.managers') }}</th>
                                    <th>{{ __('app.actions') }}</th>
                                    <th>{{ __('app.created') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hotels as $hotel)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($hotel->logo_path)
                                                <img src="{{ asset('storage/' . $hotel->logo_path) }}" 
                                                     alt="{{ $hotel->name }}" 
                                                     class="rounded-circle me-2" 
                                                     width="32" height="32">
                                            @else
                                                <div class="avatar avatar-sm me-2">
                                                    <span class="avatar-initial rounded bg-label-primary">
                                                        {{ substr($hotel->name, 0, 1) }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ $hotel->name }}</h6>
                                                <small class="text-muted">{{ $hotel->address }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $hotel->email }}</td>
                                    <td>{{ $hotel->phone }}</td>
                                    <td>{{ $hotel->city }}, {{ $hotel->country }}</td>
                                    <td>
                                        @if($hotel->is_active)
                                            <span class="badge bg-success">{{ __('app.active') }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ __('app.inactive') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $hotel->users_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        @if($hotel->users->count() > 0)
                                            <div class="d-flex flex-column gap-1">
                                                @foreach($hotel->users as $manager)
                                                    <span class="badge bg-primary">{{ $manager->name }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted">No managers</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($hotel->users->count() > 0)
                                            <div class="d-flex gap-1">
                                                @foreach($hotel->users as $manager)
                                                    <a href="{{ route('impersonate.start', $manager->id) }}" 
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="Login as {{ $manager->name }}"
                                                       onclick="return confirm('Are you sure you want to login as {{ $manager->name }}?')">
                                                        <i class="icon-base ri ri-user-settings-line"></i>
                                                    </a>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted">No managers</span>
                                        @endif
                                    </td>
                                    <td>{{ $hotel->created_at->format('M d, Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="icon-base ri ri-building-line fs-1 mb-3"></i>
                                            <h6>{{ __('app.no_hotels_found') }}</h6>
                                            <p>{{ __('app.no_hotels_description') }}</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($hotels->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $hotels->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 