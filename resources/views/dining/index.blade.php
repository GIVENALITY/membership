@extends('layouts.app')

@section('title', 'Dining Management - ' . (Auth::user()->hotel->name ?? 'Membership MS'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Dining Management</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#recordVisitModal">
                        <i class="icon-base ri ri-restaurant-line me-2"></i>
                        Record New Visit
                    </button>
                </div>
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

                    <!-- Active Visits -->
                    <div class="mb-4">
                        <h6 class="text-primary">
                            <i class="icon-base ri ri-time-line me-2"></i>
                            Active Visits ({{ $activeVisits->count() }})
                        </h6>
                        @if($activeVisits->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Member</th>
                                            <th>People</th>
                                            <th>Arrived</th>
                                            <th>Duration</th>
                                            <th>Notes</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($activeVisits as $visit)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-3">
                                                            <span class="avatar-initial rounded-circle bg-label-primary">
                                                                {{ substr($visit->member->first_name, 0, 1) }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $visit->member->full_name }}</h6>
                                                            <small class="text-muted">{{ $visit->member->membership_id }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-label-info">{{ $visit->number_of_people }} people</span>
                                                </td>
                                                <td>{{ $visit->created_at->format('H:i') }}</td>
                                                <td>{{ $visit->created_at->diffForHumans() }}</td>
                                                <td>
                                                    @if($visit->notes)
                                                        <small class="text-muted">{{ Str::limit($visit->notes, 30) }}</small>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-success" 
                                                                data-bs-toggle="modal" data-bs-target="#checkoutModal{{ $visit->id }}">
                                                            <i class="icon-base ri ri-bank-card-line"></i>
                                                            Checkout
                                                        </button>
                                                        <form action="{{ route('dining.cancel', $visit) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                    onclick="return confirm('Cancel this visit?')">
                                                                <i class="icon-base ri ri-close-line"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="icon-base ri ri-restaurant-line text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2">No active visits</p>
                            </div>
                        @endif
                    </div>

                    <!-- Recent Completed Visits -->
                    <div>
                        <h6 class="text-success">
                            <i class="icon-base ri ri-check-line me-2"></i>
                            Recent Completed Visits
                        </h6>
                        @if($completedVisits->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Member</th>
                                            <th>Amount</th>
                                            <th>Discount</th>
                                            <th>Final</th>
                                            <th>Completed</th>
                                            <th>Receipt</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($completedVisits as $visit)
                                            <tr>
                                                <td>
                                                    <div>
                                                        <h6 class="mb-0">{{ $visit->member->full_name }}</h6>
                                                        <small class="text-muted">{{ $visit->member->membership_id }}</small>
                                                    </div>
                                                </td>
                                                <td>TZS {{ number_format($visit->amount_spent) }}</td>
                                                <td>
                                                    <span class="text-success">-TZS {{ number_format($visit->discount_amount) }}</span>
                                                    <small class="text-muted">({{ $visit->discount_percentage }}%)</small>
                                                </td>
                                                <td>
                                                    <strong>TZS {{ number_format($visit->final_amount) }}</strong>
                                                </td>
                                                <td>{{ $visit->checked_out_at->format('M j, H:i') }}</td>
                                                <td>
                                                    @if($visit->receipt_path)
                                                        <a href="{{ $visit->receipt_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="icon-base ri ri-file-text-line"></i>
                                                        </a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="icon-base ri ri-check-line text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2">No completed visits yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Record Visit Modal -->
<div class="modal fade" id="recordVisitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record New Visit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('dining.record-visit') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="member_search" class="form-label">Search Member *</label>
                        <input type="text" class="form-control" id="member_search" 
                               placeholder="Search by name, membership ID, phone, or email..." required>
                        <input type="hidden" name="member_id" id="member_id" required>
                        <div id="member_results" class="mt-2"></div>
                    </div>

                    <div class="mb-3">
                        <label for="number_of_people" class="form-label">Number of People *</label>
                        <input type="number" class="form-control" id="number_of_people" name="number_of_people" 
                               min="1" max="50" value="1" required>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                  placeholder="Any special requests or notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Record Visit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Checkout Modals -->
@foreach($activeVisits as $visit)
<div class="modal fade" id="checkoutModal{{ $visit->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Checkout - {{ $visit->member->full_name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('dining.checkout', $visit) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Member:</strong> {{ $visit->member->full_name }} ({{ $visit->member->membership_id }})<br>
                        <strong>Discount Rate:</strong> {{ $visit->member->current_discount_rate }}%<br>
                        <strong>Arrived:</strong> {{ $visit->created_at->format('M j, Y H:i') }}<br>
                        <strong>Duration:</strong> {{ $visit->created_at->diffForHumans() }}
                    </div>

                    <div class="mb-3">
                        <label for="amount_spent{{ $visit->id }}" class="form-label">Amount Spent (TZS) *</label>
                        <input type="number" class="form-control" id="amount_spent{{ $visit->id }}" 
                               name="amount_spent" min="0" step="100" required>
                    </div>

                    <div class="mb-3">
                        <label for="receipt{{ $visit->id }}" class="form-label">Upload Receipt</label>
                        <input type="file" class="form-control" id="receipt{{ $visit->id }}" 
                               name="receipt" accept="image/*">
                    </div>

                    <div class="mb-3">
                        <label for="checkout_notes{{ $visit->id }}" class="form-label">Checkout Notes</label>
                        <textarea class="form-control" id="checkout_notes{{ $visit->id }}" 
                                  name="checkout_notes" rows="3" 
                                  placeholder="Any notes about the visit..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Complete Checkout</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('page-js')
<script>
// AJAX member search
let searchTimeout;
document.getElementById('member_search').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const query = this.value;
    const resultsDiv = document.getElementById('member_results');
    
    if (query.length < 2) {
        resultsDiv.innerHTML = '';
        return;
    }
    
    searchTimeout = setTimeout(() => {
        fetch(`{{ route('dining.search-members') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                resultsDiv.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(member => {
                        const div = document.createElement('div');
                        div.className = 'member-result p-2 border-bottom cursor-pointer';
                        div.innerHTML = `
                            <strong>${member.first_name} ${member.last_name}</strong><br>
                            <small class="text-muted">
                                ${member.membership_id} | ${member.phone || 'No phone'} | ${member.current_discount_rate}% discount
                            </small>
                        `;
                        div.onclick = () => selectMember(member);
                        resultsDiv.appendChild(div);
                    });
                } else {
                    resultsDiv.innerHTML = '<div class="text-muted p-2">No members found</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                resultsDiv.innerHTML = '<div class="text-danger p-2">Error searching members</div>';
            });
    }, 300);
});

function selectMember(member) {
    document.getElementById('member_search').value = `${member.first_name} ${member.last_name} (${member.membership_id})`;
    document.getElementById('member_id').value = member.id;
    document.getElementById('member_results').innerHTML = '';
}

// Auto-calculate discount for checkout
document.querySelectorAll('[id^="amount_spent"]').forEach(input => {
    input.addEventListener('input', function() {
        const visitId = this.id.replace('amount_spent', '');
        const amount = parseFloat(this.value) || 0;
        const discountRate = {{ $activeVisits->first() ? $activeVisits->first()->member->current_discount_rate : 0 }};
        const discount = (amount * discountRate) / 100;
        const final = amount - discount;
        
        // You can add a display for the calculated values if needed
        console.log(`Amount: ${amount}, Discount: ${discount}, Final: ${final}`);
    });
});
</script>

<style>
.member-result:hover {
    background-color: #f8f9fa;
}
.cursor-pointer {
    cursor: pointer;
}
</style>
@endpush 