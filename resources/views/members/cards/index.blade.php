@extends('layouts.app')

@section('title', __('app.virtual_cards'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">
                        <i class="icon-base ri ri-credit-card-line me-2"></i>
                        {{ __('app.virtual_cards') }}
                    </h4>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#massGenerateModal">
                            <i class="icon-base ri ri-download-line me-2"></i>
                            {{ __('app.mass_generate_cards') }}
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <i class="icon-base ri ri-check-line me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <i class="icon-base ri ri-error-warning-line me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="alert alert-info alert-dismissible" role="alert">
                            <i class="icon-base ri ri-information-line me-2"></i>
                            {{ session('info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('card_generation_errors') && count(session('card_generation_errors')) > 0)
                        <div class="alert alert-warning alert-dismissible" role="alert">
                            <h6 class="alert-heading">
                                <i class="icon-base ri ri-error-warning-line me-2"></i>
                                {{ __('app.card_generation_errors') }}
                            </h6>
                            <ul class="mb-0">
                                @foreach(session('card_generation_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $totalMembers }}</h4>
                                            <small>{{ __('app.total_members') }}</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="icon-base ri ri-group-line fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $membersWithCards }}</h4>
                                            <small>{{ __('app.members_with_cards') }}</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="icon-base ri ri-check-line fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $membersWithoutCards }}</h4>
                                            <small>{{ __('app.members_without_cards') }}</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="icon-base ri ri-error-warning-line fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $totalMembers > 0 ? round(($membersWithCards / $totalMembers) * 100, 1) : 0 }}%</h4>
                                            <small>{{ __('app.card_coverage') }}</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="icon-base ri ri-percent-line fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('members.cards.index') }}" class="d-flex gap-2">
                                @if(auth()->user()->role === 'super_admin')
                                    <select name="hotel_id" class="form-select" onchange="this.form.submit()">
                                        <option value="">{{ __('app.all_hotels') }}</option>
                                        @foreach($hotels as $hotel)
                                            <option value="{{ $hotel->id }}" {{ $hotelId == $hotel->id ? 'selected' : '' }}>
                                                {{ $hotel->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </form>
                        </div>
                        <div class="col-md-6 text-end">
                            @if($membersWithoutCards > 0)
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#massGenerateModal">
                                    <i class="icon-base ri ri-download-line me-2"></i>
                                    {{ __('app.generate_all_missing_cards') }} ({{ $membersWithoutCards }})
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Members Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('app.member') }}</th>
                                    <th>{{ __('app.membership_id') }}</th>
                                    <th>{{ __('app.membership_type') }}</th>
                                    <th>{{ __('app.hotel') }}</th>
                                    <th>{{ __('app.card_status') }}</th>
                                    <th>{{ __('app.qr_code_status') }}</th>
                                    <th>{{ __('app.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($members as $member)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3">
                                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                                        {{ strtoupper(substr($member->first_name, 0, 1)) }}{{ strtoupper(substr($member->last_name, 0, 1)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $member->full_name }}</h6>
                                                    <small class="text-muted">{{ $member->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-label-info">{{ $member->membership_id }}</span>
                                        </td>
                                        <td>
                                            @if($member->membershipType)
                                                <span class="badge bg-label-primary">{{ $member->membershipType->name }}</span>
                                            @else
                                                <span class="text-muted">{{ __('app.no_type') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($member->hotel)
                                                <span class="badge bg-label-secondary">{{ $member->hotel->name }}</span>
                                            @else
                                                <span class="text-muted">{{ __('app.no_hotel') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($member->card_image_path)
                                                <span class="badge bg-success">
                                                    <i class="icon-base ri ri-check-line me-1"></i>
                                                    {{ __('app.card_available') }}
                                                </span>
                                            @else
                                                <span class="badge bg-warning">
                                                    <i class="icon-base ri ri-error-warning-line me-1"></i>
                                                    {{ __('app.no_card') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($member->hasQRCode())
                                                <span class="badge bg-success">
                                                    <i class="icon-base ri ri-qr-code-line me-1"></i>
                                                    {{ __('app.qr_available') }}
                                                </span>
                                            @else
                                                <span class="badge bg-warning">
                                                    <i class="icon-base ri ri-error-warning-line me-1"></i>
                                                    {{ __('app.no_qr') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    {{ __('app.actions') }}
                                                </button>
                                                <ul class="dropdown-menu">
                                                    @if($member->card_image_path)
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('members.cards.view', $member) }}" target="_blank">
                                                                <i class="icon-base ri ri-eye-line me-2"></i>
                                                                {{ __('app.view_card') }}
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('members.cards.download', $member) }}">
                                                                <i class="icon-base ri ri-download-line me-2"></i>
                                                                {{ __('app.download_card') }}
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('members.regenerate-card', $member) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item" onclick="return confirm('{{ __('app.confirm_regenerate_card') }}')">
                                                                    <i class="icon-base ri ri-refresh-line me-2"></i>
                                                                    {{ __('app.regenerate_card') }}
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="{{ route('members.cards.delete', $member) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('{{ __('app.confirm_delete_card') }}')">
                                                                    <i class="icon-base ri ri-delete-bin-line me-2"></i>
                                                                    {{ __('app.delete_card') }}
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @else
                                                        <li>
                                                            <form action="{{ route('members.cards.generate', $member) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="icon-base ri ri-add-line me-2"></i>
                                                                    {{ __('app.generate_card') }}
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="#" onclick="debugCard({{ $member->id }})">
                                                                <i class="icon-base ri ri-bug-line me-2"></i>
                                                                Debug Template
                                                            </a>
                                                        </li>
                                                    @endif

                                                    <!-- QR Code Actions -->
                                                    @if($member->hasQRCode())
                                                        <li>
                                                            <a class="dropdown-item" href="{{ $member->getQRCodeUrlAttribute() }}" target="_blank">
                                                                <i class="icon-base ri ri-qr-code-line me-2"></i>
                                                                {{ __('app.view_qr_code') }}
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('members.generate-qr-code', $member) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item" onclick="return confirm('{{ __('app.confirm_regenerate_qr') }}')">
                                                                    <i class="icon-base ri ri-refresh-line me-2"></i>
                                                                    {{ __('app.regenerate_qr') }}
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @else
                                                        <li>
                                                            <form action="{{ route('members.generate-qr-code', $member) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="icon-base ri ri-qr-code-line me-2"></i>
                                                                    {{ __('app.generate_qr_code') }}
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif

                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('members.show', $member) }}">
                                                            <i class="icon-base ri ri-user-line me-2"></i>
                                                            {{ __('app.view_member') }}
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="icon-base ri ri-user-line fs-1 mb-3"></i>
                                                <p>{{ __('app.no_members_found') }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $members->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mass Generate Modal -->
<div class="modal fade" id="massGenerateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="icon-base ri ri-download-line me-2"></i>
                    {{ __('app.mass_generate_cards') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('members.cards.mass-generate') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="icon-base ri ri-information-line me-2"></i>
                        {{ __('app.mass_generate_description') }}
                    </div>
                    
                    @if(auth()->user()->role === 'super_admin')
                        <div class="mb-3">
                            <label for="modal_hotel_id" class="form-label">{{ __('app.select_hotel') }}</label>
                            <select class="form-select" id="modal_hotel_id" name="hotel_id">
                                <option value="">{{ __('app.all_hotels') }}</option>
                                @foreach($hotels as $hotel)
                                    <option value="{{ $hotel->id }}" {{ $hotelId == $hotel->id ? 'selected' : '' }}>
                                        {{ $hotel->name }} ({{ $hotel->members()->whereNull('card_image_path')->count() }} {{ __('app.members_without_cards') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">{{ __('app.generation_summary') }}</label>
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <h4 class="text-warning mb-0">{{ $membersWithoutCards }}</h4>
                                        <small>{{ __('app.members_without_cards') }}</small>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="text-success mb-0">{{ $membersWithCards }}</h4>
                                        <small>{{ __('app.members_with_cards') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('app.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary" {{ $membersWithoutCards == 0 ? 'disabled' : '' }}>
                        <i class="icon-base ri ri-download-line me-2"></i>
                        {{ __('app.generate_cards') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update statistics when hotel filter changes
    const hotelSelect = document.querySelector('select[name="hotel_id"]');
    if (hotelSelect) {
        hotelSelect.addEventListener('change', function() {
            updateStats(this.value);
        });
    }

    function updateStats(hotelId) {
        fetch(`{{ route('members.cards.stats') }}?hotel_id=${hotelId}`)
            .then(response => response.json())
            .then(data => {
                // Update statistics cards
                document.querySelector('.bg-primary h4').textContent = data.total;
                document.querySelector('.bg-success h4').textContent = data.with_cards;
                document.querySelector('.bg-warning h4').textContent = data.without_cards;
                document.querySelector('.bg-info h4').textContent = data.percentage + '%';
            })
            .catch(error => console.error('Error updating stats:', error));
    }
    
    function debugCard(memberId) {
        fetch(`/members/${memberId}/cards/debug`)
            .then(response => response.json())
            .then(data => {
                console.log('Card Template Debug Info:', data);
                alert('Debug info logged to console. Check browser developer tools for details.\n\n' + 
                      'Template Path: ' + (data.template_path || 'N/A') + '\n' +
                      'Template Exists: ' + (data.template_exists ? 'Yes' : 'No') + '\n' +
                      'Has Card Template: ' + (data.has_card_template ? 'Yes' : 'No'));
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while debugging the card template.');
            });
    }
});
</script>
@endpush
