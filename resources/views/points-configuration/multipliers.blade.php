@extends('layouts.app')

@section('title', 'Points Multipliers')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Points Multipliers</h4>
                    <a href="{{ route('points-configuration.index') }}" class="btn btn-secondary">
                        <i class="icon-base ri ri-arrow-left-line me-2"></i>Back to Configuration
                    </a>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="icon-base ri ri-information-line me-2"></i>Points Multipliers</h6>
                        <p class="mb-0">Multipliers increase the points earned by members based on various conditions like membership type, visit frequency, spending tiers, and time-based bonuses.</p>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Multiplier</th>
                                    <th>Conditions</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($multipliers as $multiplier)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <i class="icon-base ri {{ $multiplier->icon }}" style="color: {{ $multiplier->color }};"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-0">{{ $multiplier->name }}</h6>
                                                @if($multiplier->description)
                                                    <small class="text-muted">{{ $multiplier->description }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-secondary">{{ ucwords(str_replace('_', ' ', $multiplier->multiplier_type)) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-success">{{ $multiplier->multiplier_value }}x</span>
                                    </td>
                                    <td>
                                        @if($multiplier->membershipType)
                                            <span class="badge bg-label-primary">{{ $multiplier->membershipType->name }}</span>
                                        @else
                                            <span class="text-muted">All types</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($multiplier->is_active)
                                            <span class="badge bg-label-success">Active</span>
                                        @else
                                            <span class="badge bg-label-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="editMultiplier({{ $multiplier->id }})">
                                                        <i class="icon-base ri ri-edit-line me-2"></i>Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item text-danger" onclick="deleteMultiplier({{ $multiplier->id }})">
                                                        <i class="icon-base ri ri-delete-bin-line me-2"></i>Delete
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="icon-base ri ri-settings-line fs-1"></i>
                                            <p class="mt-2">No multipliers configured</p>
                                            <p class="small">Multipliers will be configured through the main points configuration system.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Multiplier Types Explanation -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Multiplier Types</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card bg-label-primary">
                                        <div class="card-body">
                                            <h6><i class="icon-base ri ri-vip-crown-line me-2"></i>Membership Type</h6>
                                            <p class="mb-0">Different earning rates for different membership types (e.g., VIP members get 1.5x points)</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-label-success">
                                        <div class="card-body">
                                            <h6><i class="icon-base ri ri-time-line me-2"></i>Visit Frequency</h6>
                                            <p class="mb-0">Bonus for regular visitors (e.g., members who visit 5+ times get 2x points)</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="card bg-label-warning">
                                        <div class="card-body">
                                            <h6><i class="icon-base ri ri-money-dollar-circle-line me-2"></i>Spending Tier</h6>
                                            <p class="mb-0">Higher earning for higher spending (e.g., spending >100k gets 1.3x points)</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-label-info">
                                        <div class="card-body">
                                            <h6><i class="icon-base ri ri-calendar-line me-2"></i>Time-based</h6>
                                            <p class="mb-0">Bonuses for specific times/days (e.g., weekend visits get 1.2x points)</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Multiplier Modal -->
<div class="modal fade" id="editMultiplierModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Multiplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editMultiplierForm">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" id="multiplierName" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Multiplier Value</label>
                            <input type="number" class="form-control" id="multiplierValue" step="0.1" min="0.1" required>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Membership Type</label>
                            <select class="form-select" id="membershipType">
                                <option value="">All Types</option>
                                @foreach($membershipTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="multiplierActive" checked>
                                <label class="form-check-label">Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="multiplierDescription" rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveMultiplier()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentMultiplierId = null;

function editMultiplier(id) {
    currentMultiplierId = id;
    // In a real implementation, you would fetch the multiplier data and populate the form
    const modal = new bootstrap.Modal(document.getElementById('editMultiplierModal'));
    modal.show();
}

function saveMultiplier() {
    // In a real implementation, you would save the multiplier data
    alert('Multiplier editing functionality will be implemented in the next phase.');
    const modal = bootstrap.Modal.getInstance(document.getElementById('editMultiplierModal'));
    modal.hide();
}

function deleteMultiplier(id) {
    if (confirm('Are you sure you want to delete this multiplier?')) {
        // In a real implementation, you would delete the multiplier
        alert('Multiplier deletion functionality will be implemented in the next phase.');
    }
}
</script>
@endsection
