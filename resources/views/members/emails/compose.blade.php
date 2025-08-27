@extends('layouts.app')

@section('title', 'Compose Email')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">
                        <i class="icon-base ri ri-mail-add-line me-2"></i>
                        Compose Email
                    </h4>
                    <a href="{{ route('members.emails.index') }}" class="btn btn-secondary">
                        <i class="icon-base ri ri-arrow-left-line me-2"></i>
                        Back to Emails
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('members.emails.send') }}" method="POST" id="emailForm">
                        @csrf
                        
                        <!-- Email Details -->
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Subject *</label>
                                    <input type="text" class="form-control" id="subject" name="subject" 
                                           value="{{ old('subject') }}" required>
                                </div>

                                <!-- WYSIWYG Editor -->
                                <div class="mb-3">
                                    <label for="content" class="form-label">Email Content *</label>
                                    <div class="border rounded">
                                        <div id="toolbar">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="execCommand('bold')">
                                                <i class="icon-base ri ri-bold"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="execCommand('italic')">
                                                <i class="icon-base ri ri-italic"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="execCommand('underline')">
                                                <i class="icon-base ri ri-underline"></i>
                                            </button>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                                    <i class="icon-base ri ri-heading"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#" onclick="execCommand('formatBlock', '<h1>')">Heading 1</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="execCommand('formatBlock', '<h2>')">Heading 2</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="execCommand('formatBlock', '<h3>')">Heading 3</a></li>
                                                </ul>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="execCommand('insertUnorderedList')">
                                                <i class="icon-base ri ri-list-unordered"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="execCommand('insertOrderedList')">
                                                <i class="icon-base ri ri-list-ordered"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertLink()">
                                                <i class="icon-base ri ri-link"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertImage()">
                                                <i class="icon-base ri ri-image-line"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertTable()">
                                                <i class="icon-base ri ri-table-line"></i>
                                            </button>
                                        </div>
                                        <div id="editor" class="p-3" contenteditable="true" style="min-height: 300px; border-top: 1px solid #dee2e6;">
                                            <p>Dear {{ $hotel->name }} Member,</p>
                                            <p>Thank you for being part of our community!</p>
                                            <p>Best regards,<br>{{ $hotel->name }} Team</p>
                                        </div>
                                        <textarea name="content" id="content" style="display: none;">{{ old('content') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Recipient Selection -->
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Recipients</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Recipient Type</label>
                                            <select class="form-select" id="recipient_type" name="recipient_type" required>
                                                <option value="">Select Recipients</option>
                                                <option value="all" {{ request('type') === 'all' ? 'selected' : '' }}>All Members</option>
                                                <option value="active" {{ request('type') === 'active' ? 'selected' : '' }}>Active Members Only</option>
                                                <option value="inactive" {{ request('type') === 'inactive' ? 'selected' : '' }}>Inactive Members Only</option>
                                                <option value="selected">Selected Members</option>
                                                <option value="filtered">Filtered Members</option>
                                                <option value="bounced">Bounced List Members</option>
                                                <option value="custom">Custom Email Addresses</option>
                                            </select>
                                        </div>

                                        <!-- Selected Members -->
                                        <div id="selected-members-section" style="display: none;">
                                            <div class="mb-3">
                                                <label class="form-label">Search Members</label>
                                                <input type="text" class="form-control" id="member-search" 
                                                       placeholder="Search by name, email, or ID">
                                                <div id="member-suggestions" class="list-group mt-2" style="display: none;"></div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Selected Members</label>
                                                <div id="selected-members-list" class="border rounded p-2" style="min-height: 100px;">
                                                    <small class="text-muted">No members selected</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Filtered Members -->
                                        <div id="filtered-members-section" style="display: none;">
                                            <div class="mb-3">
                                                <label class="form-label">Membership Types</label>
                                                @foreach($membershipTypes as $type)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                           name="membership_type_ids[]" value="{{ $type->id }}" 
                                                           id="type_{{ $type->id }}">
                                                    <label class="form-check-label" for="type_{{ $type->id }}">
                                                        {{ $type->name }}
                                                    </label>
                                                </div>
                                                @endforeach
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Status</label>
                                                <select class="form-select" name="status_filter">
                                                    <option value="all">All Statuses</option>
                                                    <option value="active">Active Only</option>
                                                    <option value="inactive">Inactive Only</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Bounced Members -->
                                        <div id="bounced-members-section" style="display: none;">
                                            <div class="mb-3">
                                                <label class="form-label">Bounced Members (Last 30 Days)</label>
                                                <div class="alert alert-warning">
                                                    <i class="icon-base ri ri-alert-line me-2"></i>
                                                    <strong>Note:</strong> These members had emails that bounced or failed in the last 30 days. 
                                                    Consider using the <a href="{{ route('rate-limited-emails.index') }}" class="alert-link">rate-limited email system</a> for better delivery.
                                                </div>
                                                <div id="bounced-members-list" class="border rounded p-2" style="min-height: 100px;">
                                                    <div class="text-center text-muted">
                                                        <i class="icon-base ri ri-loader-4-line me-2"></i>
                                                        Loading bounced members...
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Custom Email Addresses -->
                                        <div id="custom-emails-section" style="display: none;">
                                            <div class="mb-3">
                                                <label class="form-label">Email Addresses</label>
                                                <textarea class="form-control" id="custom_emails" name="custom_emails" 
                                                          rows="4" placeholder="Enter email addresses, one per line or separated by commas&#10;Example:&#10;john@example.com&#10;jane@example.com&#10;or: john@example.com, jane@example.com"></textarea>
                                                <small class="form-text text-muted">
                                                    Enter one email per line or separate multiple emails with commas
                                                </small>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Recipient Names (Optional)</label>
                                                <textarea class="form-control" id="custom_names" name="custom_names" 
                                                          rows="3" placeholder="Enter names corresponding to emails (optional)&#10;Example:&#10;John Doe&#10;Jane Smith"></textarea>
                                                <small class="form-text text-muted">
                                                    If provided, names will be used in the email greeting
                                                </small>
                                            </div>
                                        </div>

                                        <!-- Recipient Count -->
                                        <div class="alert alert-info" id="recipient-count" style="display: none;">
                                            <i class="icon-base ri ri-information-line me-2"></i>
                                            <span id="count-text">0 recipients</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Send Options -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Send Options</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="send_immediately" 
                                                       name="send_immediately" value="1" checked>
                                                <label class="form-check-label" for="send_immediately">
                                                    Send Immediately
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="debug_mode" 
                                                       name="debug" value="1">
                                                <label class="form-check-label" for="debug_mode">
                                                    Debug Mode (Show debug info on screen)
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3" id="schedule-section" style="display: none;">
                                            <label class="form-label">Schedule for</label>
                                            <input type="datetime-local" class="form-control" name="scheduled_at">
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="icon-base ri ri-mail-send-line me-2"></i>
                                            Send Email
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
#toolbar {
    padding: 10px;
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

#toolbar .btn {
    margin-right: 5px;
}

#editor {
    outline: none;
    font-family: inherit;
}

#editor:focus {
    background-color: #fff;
}

.selected-member {
    background-color: #e3f2fd;
    border: 1px solid #2196f3;
    border-radius: 4px;
    padding: 5px 10px;
    margin: 2px 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.selected-member .remove-member {
    color: #f44336;
    cursor: pointer;
    font-weight: bold;
}
</style>
@endpush

@push('scripts')
<script>
let selectedMembers = [];

document.addEventListener('DOMContentLoaded', function() {
    // Handle recipient type change
    document.getElementById('recipient_type').addEventListener('change', function() {
        const selectedType = this.value;
        const selectedSection = document.getElementById('selected-members-section');
        const filteredSection = document.getElementById('filtered-members-section');
        const bouncedSection = document.getElementById('bounced-members-section');
        const customSection = document.getElementById('custom-emails-section');
        
        selectedSection.style.display = 'none';
        filteredSection.style.display = 'none';
        bouncedSection.style.display = 'none';
        customSection.style.display = 'none';
        
        if (selectedType === 'selected') {
            selectedSection.style.display = 'block';
        } else if (selectedType === 'filtered') {
            filteredSection.style.display = 'block';
        } else if (selectedType === 'bounced') {
            bouncedSection.style.display = 'block';
            loadBouncedMembers();
        } else if (selectedType === 'custom') {
            customSection.style.display = 'block';
        }
        
        updateRecipientCount();
    });
    
    // Handle send immediately checkbox
    document.getElementById('send_immediately').addEventListener('change', function() {
        const scheduleSection = document.getElementById('schedule-section');
        scheduleSection.style.display = this.checked ? 'none' : 'block';
    });
    
    // Handle member search
    document.getElementById('member-search').addEventListener('input', function() {
        const query = this.value;
        if (query.length >= 2) {
            searchMembers(query);
        } else {
            document.getElementById('member-suggestions').style.display = 'none';
        }
    });
    
    // Handle custom emails input
    document.getElementById('custom_emails').addEventListener('input', function() {
        updateRecipientCount();
    });
    
    // Handle filtered members filters
    document.querySelectorAll('input[name="membership_type_ids[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (document.getElementById('recipient_type').value === 'filtered') {
                updateRecipientCount();
            }
        });
    });
    
    document.querySelector('select[name="status_filter"]').addEventListener('change', function() {
        if (document.getElementById('recipient_type').value === 'filtered') {
            updateRecipientCount();
        }
    });
    
    // Handle form submission
    document.getElementById('emailForm').addEventListener('submit', function(e) {
        // Update hidden content field with editor content
        document.getElementById('content').value = document.getElementById('editor').innerHTML;
        
        // Validate selected members if needed
        if (document.getElementById('recipient_type').value === 'selected' && selectedMembers.length === 0) {
            e.preventDefault();
            alert('Please select at least one member.');
            return false;
        }
        
        // Add selected member IDs to form if sending to selected members or bounced members
        if (document.getElementById('recipient_type').value === 'selected' || 
            document.getElementById('recipient_type').value === 'bounced') {
            // Remove any existing selected_members inputs
            const existingInputs = document.querySelectorAll('input[name="selected_members[]"]');
            existingInputs.forEach(input => input.remove());
            
            // Add new inputs for selected members
            selectedMembers.forEach(member => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_members[]';
                input.value = member.id;
                this.appendChild(input);
            });
        }
        
        // Check if debug mode is enabled
        const debugMode = document.getElementById('debug_mode').checked;
        if (debugMode) {
            e.preventDefault();
            
            // Show loading message
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="icon-base ri ri-loader-4-line me-2"></i>Sending...';
            submitBtn.disabled = true;
            
            // Submit form via AJAX
            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Display debug information
                showDebugInfo(data);
                
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error sending email: ' + error.message);
                
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }
    });
});

// WYSIWYG Editor Functions
function execCommand(command, value = null) {
    document.execCommand(command, false, value);
    document.getElementById('editor').focus();
}

function insertLink() {
    const url = prompt('Enter URL:');
    if (url) {
        execCommand('createLink', url);
    }
}

function insertImage() {
    const url = prompt('Enter image URL:');
    if (url) {
        execCommand('insertImage', url);
    }
}

function insertTable() {
    const rows = prompt('Enter number of rows:');
    const cols = prompt('Enter number of columns:');
    if (rows && cols) {
        let table = '<table border="1" style="border-collapse: collapse;">';
        for (let i = 0; i < rows; i++) {
            table += '<tr>';
            for (let j = 0; j < cols; j++) {
                table += '<td style="padding: 8px;">&nbsp;</td>';
            }
            table += '</tr>';
        }
        table += '</table>';
        execCommand('insertHTML', table);
    }
}

// Member Search Functions
function searchMembers(query) {
    fetch(`{{ route('members.emails.suggestions') }}?query=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            const suggestions = document.getElementById('member-suggestions');
            suggestions.innerHTML = '';
            
            data.forEach(member => {
                const item = document.createElement('div');
                item.className = 'list-group-item list-group-item-action';
                item.textContent = `${member.first_name} ${member.last_name} (${member.email})`;
                item.onclick = () => addMember(member);
                suggestions.appendChild(item);
            });
            
            suggestions.style.display = data.length > 0 ? 'block' : 'none';
        });
}

function addMember(member) {
    if (!selectedMembers.find(m => m.id === member.id)) {
        selectedMembers.push(member);
        updateSelectedMembersList();
        updateRecipientCount();
    }
    document.getElementById('member-search').value = '';
    document.getElementById('member-suggestions').style.display = 'none';
}

function removeMember(memberId) {
    selectedMembers = selectedMembers.filter(m => m.id !== memberId);
    updateSelectedMembersList();
    updateRecipientCount();
}

function updateSelectedMembersList() {
    const list = document.getElementById('selected-members-list');
    if (selectedMembers.length === 0) {
        list.innerHTML = '<small class="text-muted">No members selected</small>';
        return;
    }
    
    list.innerHTML = '';
    selectedMembers.forEach(member => {
        const div = document.createElement('div');
        div.className = 'selected-member';
        div.innerHTML = `
            <span>${member.first_name} ${member.last_name}</span>
            <span class="remove-member" onclick="removeMember(${member.id})">&times;</span>
        `;
        list.appendChild(div);
    });
}

function updateRecipientCount() {
    const recipientType = document.getElementById('recipient_type').value;
    const countElement = document.getElementById('count-text');
    const countContainer = document.getElementById('recipient-count');
    
    if (!recipientType) {
        countContainer.style.display = 'none';
        return;
    }
    
    if (recipientType === 'selected') {
        const count = selectedMembers.length;
        countElement.textContent = `${count} recipients`;
        countContainer.style.display = 'block';
    } else if (recipientType === 'custom') {
        const customEmails = document.getElementById('custom_emails').value;
        if (customEmails.trim()) {
            // Count emails (split by comma or newline)
            const emails = customEmails.split(/[,\n]/).filter(email => email.trim() !== '');
            countElement.textContent = `${emails.length} recipients`;
        } else {
            countElement.textContent = '0 recipients';
        }
        countContainer.style.display = 'block';
    } else if (recipientType === 'filtered') {
        // Show calculating message
        countElement.textContent = 'Calculating recipients...';
        countContainer.style.display = 'block';
        
        // Get filter parameters
        const membershipTypeIds = Array.from(document.querySelectorAll('input[name="membership_type_ids[]"]:checked'))
            .map(cb => cb.value);
        const statusFilter = document.querySelector('select[name="status_filter"]').value;
        
        // Make AJAX call to get filtered count
        fetch(`{{ route('members.emails.count') }}?type=filtered&membership_type_ids=${membershipTypeIds.join(',')}&status=${statusFilter}`)
            .then(response => response.json())
            .then(data => {
                countElement.textContent = `${data.count} recipients`;
            })
            .catch(error => {
                console.error('Error calculating recipients:', error);
                countElement.textContent = 'Error calculating recipients';
            });
    } else if (recipientType === 'bounced') {
        // For bounced members, count will be updated when members are loaded
        countElement.textContent = 'Loading bounced members...';
        countContainer.style.display = 'block';
    } else {
        // Show calculating message
        countElement.textContent = 'Calculating recipients...';
        countContainer.style.display = 'block';
        
        // Make AJAX call to get count for all/active/inactive
        fetch(`{{ route('members.emails.count') }}?type=${recipientType}`)
            .then(response => response.json())
            .then(data => {
                countElement.textContent = `${data.count} recipients`;
            })
            .catch(error => {
                console.error('Error calculating recipients:', error);
                countElement.textContent = 'Error calculating recipients';
            });
    }
}

function loadBouncedMembers() {
    const bouncedList = document.getElementById('bounced-members-list');
    
    // Show loading state
    bouncedList.innerHTML = `
        <div class="text-center text-muted">
            <i class="icon-base ri ri-loader-4-line me-2"></i>
            Loading bounced members...
        </div>
    `;
    
    // Fetch bounced members
    fetch('{{ route("members.emails.bounced") }}')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' && data.members.length > 0) {
                // Clear selected members array for bounced members
                selectedMembers = [];
                
                // Display bounced members
                bouncedList.innerHTML = data.members.map(member => `
                    <div class="selected-member">
                        <div>
                            <strong>${member.name}</strong><br>
                            <small class="text-muted">${member.email} (ID: ${member.membership_id})</small>
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-primary add-bounced-member" 
                                    data-member='${JSON.stringify(member)}'>
                                <i class="icon-base ri ri-add-line"></i> Add
                            </button>
                        </div>
                    </div>
                `).join('');
                
                // Add event listeners to add buttons
                document.querySelectorAll('.add-bounced-member').forEach(button => {
                    button.addEventListener('click', function() {
                        const member = JSON.parse(this.dataset.member);
                        addBouncedMember(member);
                    });
                });
                
                // Update recipient count
                document.getElementById('count-text').textContent = `${data.members.length} bounced members available`;
                
            } else {
                bouncedList.innerHTML = `
                    <div class="text-center text-muted">
                        <i class="icon-base ri ri-check-line me-2"></i>
                        No bounced members found in the last 30 days
                    </div>
                `;
                document.getElementById('count-text').textContent = '0 bounced members';
            }
        })
        .catch(error => {
            console.error('Error loading bounced members:', error);
            bouncedList.innerHTML = `
                <div class="text-center text-danger">
                    <i class="icon-base ri ri-error-warning-line me-2"></i>
                    Error loading bounced members
                </div>
            `;
            document.getElementById('count-text').textContent = 'Error loading bounced members';
        });
}

function addBouncedMember(member) {
    // Check if member is already selected
    if (selectedMembers.some(m => m.id === member.id)) {
        alert('This member is already selected.');
        return;
    }
    
    // Add to selected members array
    selectedMembers.push(member);
    
    // Update the selected members list
    updateSelectedMembersList();
    
    // Update recipient count
    document.getElementById('count-text').textContent = `${selectedMembers.length} recipients selected`;
}

function updateSelectedMembersList() {
    const selectedList = document.getElementById('selected-members-list');
    
    if (selectedMembers.length === 0) {
        selectedList.innerHTML = '<small class="text-muted">No members selected</small>';
    } else {
        selectedList.innerHTML = selectedMembers.map(member => `
            <div class="selected-member">
                <div>
                    <strong>${member.name}</strong><br>
                    <small class="text-muted">${member.email}</small>
                </div>
                <div>
                    <span class="remove-member" onclick="removeSelectedMember(${member.id})">×</span>
                </div>
            </div>
        `).join('');
    }
}

function removeSelectedMember(memberId) {
    selectedMembers = selectedMembers.filter(member => member.id !== memberId);
    updateSelectedMembersList();
    document.getElementById('count-text').textContent = `${selectedMembers.length} recipients selected`;
}

function showDebugInfo(data) {
    // Create debug modal
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = 'debugModal';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Email Debug Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-${data.status === 'completed' ? 'success' : 'danger'}">
                        <strong>Status:</strong> ${data.status}<br>
                        <strong>Message:</strong> ${data.message}
                    </div>
                    
                    <h6>Debug Information:</h6>
                    <pre style="background: #f8f9fa; padding: 15px; border-radius: 5px; max-height: 400px; overflow-y: auto;">${JSON.stringify(data.debug_info, null, 2)}</pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    `;
    
    // Add modal to page and show it
    document.body.appendChild(modal);
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
    
    // Remove modal from DOM after it's hidden
    modal.addEventListener('hidden.bs.modal', function() {
        document.body.removeChild(modal);
    });
}
</script>
@endpush
@endsection
