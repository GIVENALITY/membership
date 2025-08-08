@extends('layouts.app')

@section('title', 'Membership Types')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Membership Types</h4>
        <a href="{{ route('membership-types.create') }}" class="btn btn-primary">
          <i class="icon-base ri ri-add-line me-2"></i>
          Add New Type
        </a>
      </div>
      <div class="card-body">
        @if (session('success'))
          <div class="alert alert-success">
            {{ session('success') }}
          </div>
        @endif

        @if (session('error'))
          <div class="alert alert-danger">
            {{ session('error') }}
          </div>
        @endif

        <div class="row">
          @forelse($membershipTypes as $type)
            <div class="col-md-6 col-lg-4 mb-4">
              <div class="card h-100 {{ $type->is_active ? 'border-success' : 'border-secondary' }}">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h5 class="card-title mb-0">{{ $type->name }}</h5>
                  <div>
                    @if($type->is_active)
                      <span class="badge bg-label-success">Active</span>
                    @else
                      <span class="badge bg-label-secondary">Inactive</span>
                    @endif
                  </div>
                </div>
                <div class="card-body">
                  @if($type->description)
                    <p class="text-muted mb-3">{{ $type->description }}</p>
                  @endif
                  
                  <div class="mb-3">
                    <h6 class="text-primary mb-1">{{ $type->formatted_price }}</h6>
                    <small class="text-muted">{{ $type->visits_limit_text }}</small>
                  </div>

                  <div class="mb-3">
                    <h6 class="mb-2">Perks:</h6>
                    {!! $type->perks_list !!}
                  </div>

                  <div class="mb-3">
                    <span class="badge bg-label-info">Base Discount: {{ $type->discount_rate }}%</span>
                  </div>

                  <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">{{ $type->members()->count() }} member(s)</small>
                    <div class="dropdown">
                      <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Actions
                      </button>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('membership-types.show', $type) }}"><i class="icon-base ri ri-eye-line me-2"></i>View</a></li>
                        <li><a class="dropdown-item" href="{{ route('membership-types.edit', $type) }}"><i class="icon-base ri ri-edit-line me-2"></i>Edit</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                          <form action="{{ route('membership-types.destroy', $type) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this membership type?')">
                              <i class="icon-base ri ri-delete-bin-line me-2"></i>Delete
                            </button>
                          </form>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          @empty
            <div class="col-12">
              <div class="text-center py-5">
                <i class="icon-base ri ri-user-star-line icon-4x text-muted mb-3"></i>
                <h5>No Membership Types Found</h5>
                <p class="text-muted">Create your first membership type to get started.</p>
                <a href="{{ route('membership-types.create') }}" class="btn btn-primary">
                  <i class="icon-base ri ri-add-line me-2"></i>
                  Create First Type
                </a>
              </div>
            </div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</div>
@endsection 