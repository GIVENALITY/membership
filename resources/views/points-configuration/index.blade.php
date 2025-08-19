@extends('layouts.app')

@section('title', 'Points Configuration')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Points Configuration</h4>
                    <div>
                        <a href="{{ route('points-configuration.create') }}" class="btn btn-primary">
                            <i class="icon-base ri ri-add-line me-2"></i>Create Configuration
                        </a>
                        <a href="{{ route('points-configuration.multipliers') }}" class="btn btn-outline-secondary">
                            <i class="icon-base ri ri-settings-line me-2"></i>Multipliers
                        </a>
                        <a href="{{ route('points-configuration.tiers') }}" class="btn btn-outline-info">
                            <i class="icon-base ri ri-vip-crown-line me-2"></i>Tiers
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-label-primary">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="icon-base ri ri-settings-line fs-3"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h5 class="mb-0">{{ $configurations->count() }}</h5>
                                            <small>Active Configurations</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-label-success">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="icon-base ri ri-time-line fs-3"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h5 class="mb-0">{{ $multipliers->count() }}</h5>
                                            <small>Active Multipliers</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-label-warning">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="icon-base ri ri-vip-crown-line fs-3"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h5 class="mb-0">{{ $tiers->count() }}</h5>
                                            <small>Points Tiers</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-label-info">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="icon-base ri ri-calculator-line fs-3"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h5 class="mb-0">{{ $configurations->where('is_active', true)->count() }}</h5>
                                            <small>Active Rules</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Test Calculator -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Test Points Calculation</h5>
                                </div>
                                <div class="card-body">
                                    <form id="testCalculationForm">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label">Spending Amount (TZS)</label>
                                                <input type="number" class="form-control" id="testSpending" value="100000">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Number of People</label>
                                                <input type="number" class="form-control" id="testPeople" value="2">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Event Type</label>
                                                <select class="form-select" id="testEventType">
                                                    <option value="dining_visit">Dining Visit</option>
                                                    <option value="special_event">Special Event</option>
                                                    <option value="referral">Referral</option>
                                                    <option value="social_media">Social Media</option>
                                                    <option value="birthday_bonus">Birthday Bonus</option>
                                                    <option value="holiday_bonus">Holiday Bonus</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">&nbsp;</label>
                                                <button type="submit" class="btn btn-primary d-block w-100">
                                                    <i class="icon-base ri ri-calculator-line me-2"></i>Calculate Points
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    <div id="testResults" class="mt-3" style="display: none;">
                                        <div class="alert alert-info">
                                            <h6>Calculation Results:</h6>
                                            <div id="resultsContent"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Configurations Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Sort Order</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($configurations as $config)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <i class="icon-base ri {{ $config->icon }}" style="color: {{ $config->color }};"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-0">{{ $config->name }}</h6>
                                                @if($config->description)
                                                    <small class="text-muted">{{ $config->description }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-secondary">{{ ucwords(str_replace('_', ' ', $config->type)) }}</span>
                                    </td>
                                    <td>
                                        @if($config->is_active)
                                            <span class="badge bg-label-success">Active</span>
                                        @else
                                            <span class="badge bg-label-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $config->sort_order }}</td>
                                    <td>{{ $config->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('points-configuration.edit', $config) }}">
                                                        <i class="icon-base ri ri-edit-line me-2"></i>Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item text-danger" onclick="deleteConfiguration({{ $config->id }})">
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
                                            <p class="mt-2">No points configurations found</p>
                                            <a href="{{ route('points-configuration.create') }}" class="btn btn-primary">
                                                Create Your First Configuration
                                            </a>
                                        </div>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Configuration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this points configuration? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Test calculation form
    const testForm = document.getElementById('testCalculationForm');
    const testResults = document.getElementById('testResults');
    const resultsContent = document.getElementById('resultsContent');

    testForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData();
        formData.append('spending_amount', document.getElementById('testSpending').value);
        formData.append('number_of_people', document.getElementById('testPeople').value);
        formData.append('event_type', document.getElementById('testEventType').value);

        fetch('{{ route("points-configuration.test") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            let html = `<strong>Total Points: ${data.total_points}</strong><br>`;
            
            if (data.breakdown && data.breakdown.length > 0) {
                html += '<div class="mt-2"><strong>Breakdown:</strong><ul>';
                data.breakdown.forEach(item => {
                    html += `<li>${item.description}: ${item.points} points</li>`;
                });
                html += '</ul></div>';
            }

            resultsContent.innerHTML = html;
            testResults.style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            resultsContent.innerHTML = '<div class="text-danger">Error calculating points</div>';
            testResults.style.display = 'block';
        });
    });
});

function deleteConfiguration(id) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const form = document.getElementById('deleteForm');
    form.action = `/points-configuration/${id}`;
    modal.show();
}
</script>
@endsection
