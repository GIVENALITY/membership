@extends('layouts.app')

@section('title', __('app.dining_management') . ' - ' . (Auth::user()->hotel->name ?? 'Membership MS'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <!-- Process Flow Guide -->
            <div class="alert alert-info">
                <h6><i class="icon-base ri ri-information-line me-2"></i>{{ __('app.dining_process_flow') }}</h6>
                <div class="row">
                    <div class="col-md-6">
                        <strong>{{ __('app.step_1_checkin') }}:</strong>
                        <ol class="mb-0 small">
                            <li>{{ __('app.search_member_by_name_number') }}</li>
                            <li>{{ __('app.record_number_of_guests') }}</li>
                            <li>{{ __('app.review_member_preferences') }}</li>
                            <li>{{ __('app.checkin_member_visit_stays_open') }}</li>
                        </ol>
                    </div>
                    <div class="col-md-6">
                        <strong>{{ __('app.step_2_checkout') }}:</strong>
                        <ol class="mb-0 small">
                            <li>{{ __('app.find_member_in_current_visits') }}</li>
                            <li>{{ __('app.enter_bill_amount') }}</li>
                            <li>{{ __('app.system_calculates_discount') }}</li>
                            <li>{{ __('app.upload_receipt_close_visit') }}</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Check-in Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="icon-base ri ri-user-add-line me-2"></i>
                        {{ __('app.step_1_member_checkin') }}
                    </h4>
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

                    <!-- Member Search -->
                    <div class="mb-4">
                        <label for="memberSearch" class="form-label">
                            <i class="icon-base ri ri-search-line me-2"></i>
                            {{ __('app.search_member_name_or_number') }}
                        </label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="memberSearch" 
                                   placeholder="{{ __('app.type_member_name_or_number') }}">
                            <button class="btn btn-outline-primary" type="button" onclick="searchMembers()">
                                <i class="icon-base ri ri-search-line"></i>
                                {{ __('app.search') }}
                            </button>
                        </div>
                        <small class="text-muted">{{ __('app.search_by_member_name_or_id') }}</small>
                    </div>

                    <!-- Search Results -->
                    <div id="searchResults" class="mb-4" style="display: none;">
                        <h6>{{ __('app.search_results') }}:</h6>
                        <div id="memberResults" class="list-group"></div>
                    </div>

                    <!-- Check-in Form -->
                    <div id="checkinForm" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">{{ __('app.member_information') }}</h6>
                                    </div>
                                    <div class="card-body" id="memberInfo">
                                        <!-- Member details will be populated here -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0">{{ __('app.checkin_details') }}</h6>
                                    </div>
                                    <div class="card-body">
                                        <form id="checkinFormElement">
                                            <div class="mb-3">
                                                <label for="numberOfPeople" class="form-label">Number of Guests</label>
                                                <input type="number" class="form-control" id="numberOfPeople" 
                                                       name="number_of_people" min="1" max="20" value="1" required>
                                                <small class="text-muted">Total number of people dining</small>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="notes" class="form-label">Special Notes</label>
                                                <textarea class="form-control" id="notes" name="notes" rows="3" 
                                                          placeholder="Any special requests or notes for the waiter..."></textarea>
                                                <small class="text-muted">Communicate preferences to waiter</small>
                                            </div>

                                            <div class="d-grid">
                                                <button type="button" class="btn btn-success" onclick="checkInMember()">
                                                    <i class="icon-base ri ri-check-line me-2"></i>
                                                    Check-in Member
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Visits Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="icon-base ri ri-time-line me-2"></i>
                        Current Active Visits
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Membership ID</th>
                                    <th>Guests</th>
                                    <th>Check-in Time</th>
                                    <th>Notes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="currentVisitsTable">
                                <!-- Current visits will be populated here -->
                            </tbody>
                        </table>
                    </div>
                    <div id="noVisitsMessage" class="text-center py-4">
                        <i class="icon-base ri ri-time-line icon-3x text-muted mb-3"></i>
                        <h6>No Active Visits</h6>
                        <p class="text-muted">Check in members to see them here</p>
                    </div>
                </div>
            </div>

            <!-- Checkout Section -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="icon-base ri ri-bill-line me-2"></i>
                        Step 2: Payment & Checkout
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <h6><i class="icon-base ri ri-information-line me-2"></i>Checkout Process</h6>
                        <p class="mb-0">
                            When member is ready to pay: Enter bill amount → System calculates discount → Upload receipt → Close visit
                        </p>
                    </div>

                    <!-- Checkout Form -->
                    <div id="checkoutForm" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="mb-0">Payment Details</h6>
                                    </div>
                                    <div class="card-body">
                                        <form id="checkoutFormElement">
                                            <div class="mb-3">
                                                <label for="billAmount" class="form-label">Total Bill Amount (TZS)</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">TZS</span>
                                                    <input type="number" class="form-control" id="billAmount" 
                                                           name="amount_spent" step="100" min="0" required>
                                                </div>
                                                <small class="text-muted">Enter the total bill amount before discount</small>
                                            </div>

                                            <div class="mb-3">
                                                <label for="numberOfPeople" class="form-label">Number of People</label>
                                                <input type="number" class="form-control" id="numberOfPeople" 
                                                       name="number_of_people" min="1" max="50" value="1" required>
                                                <small class="text-muted">Number of people in the party</small>
                                            </div>

                                            @if(auth()->user()->hotel->getSetting('receipt_required', false))
                                            <div class="mb-3">
                                                <label for="receipt" class="form-label">Upload Receipt <span class="text-danger">*</span></label>
                                                <input type="file" class="form-control" id="receipt" name="receipt" 
                                                       accept="image/*" required>
                                                <small class="text-muted">Upload receipt image (JPG, PNG, GIF) - required</small>
                                            </div>
                                            @else
                                            <div class="mb-3">
                                                <label for="receipt" class="form-label">Upload Receipt (Optional)</label>
                                                <input type="file" class="form-control" id="receipt" name="receipt" 
                                                       accept="image/*">
                                                <small class="text-muted">Upload receipt image (JPG, PNG, GIF) - optional</small>
                                            </div>
                                            @endif

                                            <div class="mb-3">
                                                <label for="checkoutNotes" class="form-label">Checkout Notes</label>
                                                <textarea class="form-control" id="checkoutNotes" name="checkout_notes" 
                                                          rows="2" placeholder="Any additional notes..."></textarea>
                                            </div>

                                            <div class="d-grid">
                                                <button type="button" class="btn btn-warning" onclick="processCheckout()">
                                                    <i class="icon-base ri ri-bill-line me-2"></i>
                                                    Process Payment & Checkout
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">Discount Preview</h6>
                                    </div>
                                    <div class="card-body" id="discountPreview">
                                        <div class="text-center text-muted">
                                            <i class="icon-base ri ri-calculator-line icon-2x mb-2"></i>
                                            <p>Enter bill amount to see discount calculation</p>
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

<!-- Member Details Modal -->
<div class="modal fade" id="memberDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Member Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="memberDetailsContent">
                <!-- Member details will be populated here -->
            </div>
        </div>
    </div>
</div>

<script>
let selectedMember = null;
let currentVisits = [];

document.addEventListener('DOMContentLoaded', function() {
    loadCurrentVisits();
    
    // Auto-search on input
    const searchInput = document.getElementById('memberSearch');
    searchInput.addEventListener('input', function() {
        if (this.value.length >= 2) {
            searchMembers();
        }
    });
    
    // Calculate discount on bill amount change
    const billAmountInput = document.getElementById('billAmount');
    billAmountInput.addEventListener('input', function() {
        calculateDiscount();
    });
});

function searchMembers() {
    const searchTerm = document.getElementById('memberSearch').value;
    if (!searchTerm) return;

    fetch(`{{ route('dining.search-members') }}?search=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(data => {
            const resultsDiv = document.getElementById('memberResults');
            const searchResultsDiv = document.getElementById('searchResults');
            
            if (data.members && data.members.length > 0) {
                resultsDiv.innerHTML = data.members.map(member => `
                    <div class="list-group-item list-group-item-action member-result" 
                         onclick="selectMember(${member.id})" style="cursor: pointer;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">${member.name}</h6>
                                <small class="text-muted">ID: ${member.membership_id} | Phone: ${member.phone || 'N/A'}</small>
                                ${member.preferenceIndicators || ''}
                            </div>
                            <i class="icon-base ri ri-arrow-right-line"></i>
                        </div>
                    </div>
                `).join('');
                searchResultsDiv.style.display = 'block';
            } else {
                resultsDiv.innerHTML = '<div class="text-center text-muted py-3">No members found</div>';
                searchResultsDiv.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error searching members:', error);
        });
}

function selectMember(memberId) {
    fetch(`/members/${memberId}/json`)
        .then(response => response.json())
        .then(member => {
            selectedMember = member;
            showMemberDetails(member);
            document.getElementById('checkinForm').style.display = 'block';
            document.getElementById('searchResults').style.display = 'none';
        })
        .catch(error => {
            console.error('Error fetching member details:', error);
        });
}

function showMemberDetails(member) {
    const memberInfo = document.getElementById('memberInfo');
    memberInfo.innerHTML = `
        <div class="mb-3">
            <h6>${member.name}</h6>
            <p class="text-muted mb-1">ID: ${member.membership_id}</p>
            <p class="text-muted mb-1">Phone: ${member.phone || 'N/A'}</p>
            <p class="text-muted mb-1">Email: ${member.email || 'N/A'}</p>
        </div>
        
        <div class="mb-3">
            <h6>Membership Status</h6>
            <p class="text-muted mb-1">Type: ${member.membership_type?.name || 'N/A'}</p>
            <p class="text-muted mb-1">Points: ${member.current_points_balance || 0}</p>
            <p class="text-muted mb-1">Total Visits: ${member.total_visits || 0}</p>
        </div>
        
        <div class="mb-3">
            <h6>Preferences & Important Notes</h6>
            ${member.allergies ? `<div class="alert alert-sm alert-danger mb-2"><i class="icon-base ri ri-error-warning-line me-2"></i><strong>Allergies:</strong> ${member.allergies}</div>` : ''}
            ${member.dietary_preferences ? `<div class="alert alert-sm alert-warning mb-2"><i class="icon-base ri ri-restaurant-line me-2"></i><strong>Dietary:</strong> ${member.dietary_preferences}</div>` : ''}
            ${member.special_requests ? `<div class="alert alert-sm alert-info mb-2"><i class="icon-base ri ri-star-line me-2"></i><strong>Special:</strong> ${member.special_requests}</div>` : ''}
            ${member.additional_notes ? `<div class="alert alert-sm alert-secondary mb-2"><i class="icon-base ri ri-file-text-line me-2"></i><strong>Notes:</strong> ${member.additional_notes}</div>` : ''}
            ${member.emergency_contact ? `<div class="alert alert-sm alert-primary mb-2"><i class="icon-base ri ri-phone-line me-2"></i><strong>Emergency:</strong> ${member.emergency_contact}</div>` : ''}
        </div>
        
        ${member.is_birthday_visit ? '<div class="alert alert-warning"><i class="icon-base ri ri-cake-line me-2"></i><strong>Birthday Alert:</strong> Special birthday discount available!</div>' : ''}
        
        <div class="service-recommendation">
            <h6>Service Recommendations</h6>
            <ul class="list-unstyled small">
                ${member.allergies ? '<li><i class="icon-base ri ri-check-line text-success me-2"></i>Ensure kitchen is aware of allergies</li>' : ''}
                ${member.dietary_preferences ? '<li><i class="icon-base ri ri-check-line text-success me-2"></i>Offer dietary preference options</li>' : ''}
                ${member.special_requests ? '<li><i class="icon-base ri ri-check-line text-success me-2"></i>Accommodate special requests</li>' : ''}
                ${member.is_birthday_visit ? '<li><i class="icon-base ri ri-check-line text-success me-2"></i>Consider birthday celebration options</li>' : ''}
            </ul>
        </div>
    `;
}

function checkInMember() {
    if (!selectedMember) return;

    const formData = new FormData();
    formData.append('member_id', selectedMember.id);
    formData.append('number_of_people', document.getElementById('numberOfPeople').value);
    formData.append('notes', document.getElementById('notes').value);

    fetch('{{ route("dining.record-visit") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('Member checked in successfully!');
            document.getElementById('checkinForm').style.display = 'none';
            document.getElementById('memberSearch').value = '';
            selectedMember = null;
            loadCurrentVisits();
        } else {
            alert('Error: ' + (data.message || 'Unknown error occurred'));
        }
    })
    .catch(error => {
        console.error('Error checking in member:', error);
        alert('Error checking in member: ' + error.message);
    });
}

function loadCurrentVisits() {
    fetch('{{ route("dining.current-visits") }}')
        .then(response => response.json())
        .then(data => {
            currentVisits = data.visits || [];
            updateCurrentVisitsTable();
        })
        .catch(error => {
            console.error('Error loading current visits:', error);
        });
}

function updateCurrentVisitsTable() {
    const tableBody = document.getElementById('currentVisitsTable');
    const noVisitsMessage = document.getElementById('noVisitsMessage');
    
    if (currentVisits.length === 0) {
        tableBody.innerHTML = '';
        noVisitsMessage.style.display = 'block';
        return;
    }
    
    noVisitsMessage.style.display = 'none';
    tableBody.innerHTML = currentVisits.map(visit => `
        <tr>
            <td>${visit.member.name}</td>
            <td>${visit.member.membership_id}</td>
            <td>${visit.number_of_people}</td>
            <td>${new Date(visit.created_at).toLocaleTimeString()}</td>
            <td>${visit.notes || '-'}</td>
            <td>
                <button class="btn btn-sm btn-warning" onclick="startCheckout(${visit.id})">
                    <i class="icon-base ri ri-bill-line me-1"></i>
                    Checkout
                </button>
            </td>
        </tr>
    `).join('');
}

function startCheckout(visitId) {
    const visit = currentVisits.find(v => v.id === visitId);
    if (!visit) return;

    selectedMember = visit.member;
    document.getElementById('checkoutForm').style.display = 'block';
    document.getElementById('billAmount').focus();
    
    // Scroll to checkout section
    document.getElementById('checkoutForm').scrollIntoView({ behavior: 'smooth' });
}

function calculateDiscount() {
    const billAmount = parseFloat(document.getElementById('billAmount').value) || 0;
    if (billAmount <= 0) {
        document.getElementById('discountPreview').innerHTML = `
            <div class="text-center text-muted">
                <i class="icon-base ri ri-calculator-line icon-2x mb-2"></i>
                <p>Enter bill amount to see discount calculation</p>
            </div>
        `;
        return;
    }

    // This would be calculated server-side, but showing preview
    const discountRate = 10; // Example rate
    const discountAmount = (billAmount * discountRate) / 100;
    const finalAmount = billAmount - discountAmount;

    document.getElementById('discountPreview').innerHTML = `
        <div class="row text-center">
            <div class="col-6">
                <h6 class="text-muted">Bill Amount</h6>
                <h4 class="text-primary">TZS ${billAmount.toLocaleString()}</h4>
            </div>
            <div class="col-6">
                <h6 class="text-muted">Discount (${discountRate}%)</h6>
                <h4 class="text-success">-TZS ${discountAmount.toLocaleString()}</h4>
            </div>
        </div>
        <hr>
        <div class="text-center">
            <h6 class="text-muted">Final Amount</h6>
            <h3 class="text-warning">TZS ${finalAmount.toLocaleString()}</h3>
        </div>
    `;
}

function processCheckout() {
    if (!selectedMember) return;

    const formData = new FormData();
    formData.append('member_id', selectedMember.id);
    formData.append('amount_spent', document.getElementById('billAmount').value);
    formData.append('number_of_people', document.getElementById('numberOfPeople').value);
    formData.append('receipt', document.getElementById('receipt').files[0]);
    formData.append('checkout_notes', document.getElementById('checkoutNotes').value);
    formData.append('is_checked_out', 'true');

    fetch('{{ route("dining.process-payment") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
               .then(data => {
               if (data.success) {
                   alert('Payment processed successfully! Visit closed.');
                   document.getElementById('checkoutForm').style.display = 'none';
                   document.getElementById('checkoutFormElement').reset();
                   selectedMember = null;
                   // Reload current visits to remove the closed visit
                   loadCurrentVisits();
               } else {
            let errorMsg = 'Error: ' + data.message;
            if (data.debug_info) {
                errorMsg += '\n\nDebug Info:\n';
                errorMsg += 'File: ' + data.debug_info.file + '\n';
                errorMsg += 'Line: ' + data.debug_info.line + '\n';
                errorMsg += 'Error: ' + data.debug_info.error;
            }
            alert(errorMsg);
        }
    })
    .catch(error => {
        console.error('Error processing checkout:', error);
        alert('Error processing checkout: ' + error.message);
    });
}
</script>

<style>
.member-result:hover {
    background-color: #f8f9fa;
    border-left: 3px solid #007bff;
}

.alert-sm {
    padding: 0.5rem;
    font-size: 0.875rem;
}

.badge-sm {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.preference-alert {
    border-left: 3px solid;
    padding-left: 0.75rem;
}

.preference-alert.allergies {
    border-left-color: #dc3545;
    background-color: #f8d7da;
}

.preference-alert.dietary {
    border-left-color: #ffc107;
    background-color: #fff3cd;
}

.preference-alert.special {
    border-left-color: #17a2b8;
    background-color: #d1ecf1;
}

.preference-alert.birthday {
    border-left-color: #fd7e14;
    background-color: #ffeaa7;
}

.service-recommendation {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 0.375rem;
    border-left: 3px solid #28a745;
}
</style>

@endsection 