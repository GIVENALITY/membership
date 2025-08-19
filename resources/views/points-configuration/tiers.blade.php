@extends('layouts.app')

@section('title', 'Points Tiers')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Points Tiers</h4>
                    <a href="{{ route('points-configuration.index') }}" class="btn btn-secondary">
                        <i class="icon-base ri ri-arrow-left-line me-2"></i>Back to Configuration
                    </a>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="icon-base ri ri-information-line me-2"></i>Points Tiers</h6>
                        <p class="mb-0">Tiers provide different earning rates and benefits based on member points balance. Higher tiers offer better multipliers and exclusive benefits.</p>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tier</th>
                                    <th>Points Range</th>
                                    <th>Multiplier</th>
                                    <th>Benefits</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tiers as $tier)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <i class="icon-base ri {{ $tier->icon }}" style="color: {{ $tier->color }};"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-0">{{ $tier->name }}</h6>
                                                @if($tier->description)
                                                    <small class="text-muted">{{ $tier->description }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $tier->badge_class }}">{{ $tier->points_range }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-success">{{ $tier->formatted_multiplier }}</span>
                                    </td>
                                    <td>
                                        @if($tier->benefits && is_array($tier->benefits))
                                            @foreach(array_slice($tier->benefits, 0, 2) as $benefit)
                                                <span class="badge bg-label-primary me-1">{{ $benefit }}</span>
                                            @endforeach
                                            @if(count($tier->benefits) > 2)
                                                <span class="badge bg-label-secondary">+{{ count($tier->benefits) - 2 }} more</span>
                                            @endif
                                        @else
                                            <span class="text-muted">No benefits defined</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($tier->is_active)
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
                                                    <a class="dropdown-item" href="#" onclick="editTier({{ $tier->id }})">
                                                        <i class="icon-base ri ri-edit-line me-2"></i>Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item text-danger" onclick="deleteTier({{ $tier->id }})">
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
                                            <i class="icon-base ri ri-vip-crown-line fs-1"></i>
                                            <p class="mt-2">No tiers configured</p>
                                            <p class="small">Tiers will be configured through the main points configuration system.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Tier Progression Visualization -->
                    @if($tiers->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Tier Progression</h5>
                            <div class="tier-progression">
                                @foreach($tiers as $index => $tier)
                                    <div class="tier-step {{ $index === 0 ? 'first' : '' }} {{ $index === $tiers->count() - 1 ? 'last' : '' }}">
                                        <div class="tier-icon" style="background-color: {{ $tier->color }};">
                                            <i class="icon-base ri {{ $tier->icon }}"></i>
                                        </div>
                                        <div class="tier-info">
                                            <h6>{{ $tier->name }}</h6>
                                            <p class="mb-1">{{ $tier->points_range }}</p>
                                            <small class="text-muted">{{ $tier->formatted_multiplier }} multiplier</small>
                                        </div>
                                        @if($index < $tiers->count() - 1)
                                            <div class="tier-arrow">
                                                <i class="icon-base ri ri-arrow-right-line"></i>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Tier Benefits Examples -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Common Tier Benefits</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card bg-label-warning">
                                        <div class="card-body">
                                            <h6><i class="icon-base ri ri-vip-crown-line me-2"></i>Bronze (0-49 points)</h6>
                                            <ul class="mb-0">
                                                <li>1.0x points multiplier</li>
                                                <li>Basic member benefits</li>
                                                <li>Standard discounts</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-label-info">
                                        <div class="card-body">
                                            <h6><i class="icon-base ri ri-star-line me-2"></i>Silver (50-99 points)</h6>
                                            <ul class="mb-0">
                                                <li>1.2x points multiplier</li>
                                                <li>Priority seating</li>
                                                <li>Enhanced discounts</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-label-success">
                                        <div class="card-body">
                                            <h6><i class="icon-base ri ri-vip-crown-line me-2"></i>Gold (100+ points)</h6>
                                            <ul class="mb-0">
                                                <li>1.5x points multiplier</li>
                                                <li>VIP treatment</li>
                                                <li>Exclusive benefits</li>
                                            </ul>
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

<!-- Edit Tier Modal -->
<div class="modal fade" id="editTierModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Tier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editTierForm">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Tier Name</label>
                            <input type="text" class="form-control" id="tierName" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Multiplier</label>
                            <input type="number" class="form-control" id="tierMultiplier" step="0.1" min="0.1" required>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Minimum Points</label>
                            <input type="number" class="form-control" id="tierMinPoints" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Maximum Points</label>
                            <input type="number" class="form-control" id="tierMaxPoints" min="0">
                            <small class="text-muted">Leave empty for unlimited</small>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Sort Order</label>
                            <input type="number" class="form-control" id="tierSortOrder" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="tierActive" checked>
                                <label class="form-check-label">Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="tierDescription" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <label class="form-label">Benefits (one per line)</label>
                            <textarea class="form-control" id="tierBenefits" rows="4" placeholder="Priority seating&#10;Enhanced discounts&#10;VIP treatment"></textarea>
                            <small class="text-muted">Enter each benefit on a new line</small>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveTier()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<style>
.tier-progression {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin: 2rem 0;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 10px;
}

.tier-step {
    display: flex;
    align-items: center;
    flex: 1;
    position: relative;
}

.tier-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    margin-right: 1rem;
}

.tier-info {
    flex: 1;
}

.tier-info h6 {
    margin: 0;
    font-size: 0.9rem;
}

.tier-info p {
    margin: 0;
    font-size: 0.8rem;
    font-weight: bold;
}

.tier-arrow {
    margin: 0 1rem;
    color: #6c757d;
    font-size: 1.5rem;
}

@media (max-width: 768px) {
    .tier-progression {
        flex-direction: column;
        gap: 1rem;
    }
    
    .tier-arrow {
        transform: rotate(90deg);
        margin: 0.5rem 0;
    }
}
</style>

<script>
let currentTierId = null;

function editTier(id) {
    currentTierId = id;
    // In a real implementation, you would fetch the tier data and populate the form
    const modal = new bootstrap.Modal(document.getElementById('editTierModal'));
    modal.show();
}

function saveTier() {
    // In a real implementation, you would save the tier data
    alert('Tier editing functionality will be implemented in the next phase.');
    const modal = bootstrap.Modal.getInstance(document.getElementById('editTierModal'));
    modal.hide();
}

function deleteTier(id) {
    if (confirm('Are you sure you want to delete this tier?')) {
        // In a real implementation, you would delete the tier
        alert('Tier deletion functionality will be implemented in the next phase.');
    }
}
</script>
@endsection
