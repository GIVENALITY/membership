@extends('layouts.app')

@section('title', 'Create Event')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Create New Event</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Event Title *</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description *</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="start_date" class="form-label">Start Date & Time *</label>
                                            <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" 
                                                   id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                            @error('start_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="end_date" class="form-label">End Date & Time *</label>
                                            <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" 
                                                   id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                            @error('end_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="till_late" name="till_late" 
                                               value="1" {{ old('till_late') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="till_late">
                                            Till Late
                                        </label>
                                        <div class="form-text">Check this if the event runs until late (end time will show as "Till Late")</div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                           id="location" name="location" value="{{ old('location') }}" 
                                           placeholder="e.g., Main Ballroom, Conference Room A">
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="max_capacity" class="form-label">Maximum Capacity</label>
                                            <input type="number" class="form-control @error('max_capacity') is-invalid @enderror" 
                                                   id="max_capacity" name="max_capacity" value="{{ old('max_capacity') }}" 
                                                   min="1" placeholder="Leave empty for unlimited">
                                            @error('max_capacity')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="price" class="form-label">Price per Person</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ Auth::user()->hotel->currency_symbol ?? '$' }}</span>
                                                <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                                       id="price" name="price" value="{{ old('price', 0) }}" 
                                                       min="0" step="0.01">
                                            </div>
                                            @error('price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="image" class="form-label">Event Image</label>
                                    <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                           id="image" name="image" accept="image/*">
                                    <div class="form-text">Recommended size: 800x600px. Max size: 2MB.</div>
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_public" name="is_public" 
                                               value="1" {{ old('is_public', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_public">
                                            Public Event
                                        </label>
                                        <div class="form-text">Allow external registrations</div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                               value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active Event
                                        </label>
                                        <div class="form-text">Enable this event</div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="registration_closed" name="registration_closed" 
                                               value="1" {{ old('registration_closed') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="registration_closed">
                                            Close Registration
                                        </label>
                                        <div class="form-text">Prevent new registrations for this event</div>
                                    </div>
                                </div>

                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Event Preview</h6>
                                        <div id="event-preview">
                                            <p class="text-muted">Fill in the details to see a preview</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('events.index') }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-back"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save"></i> Create Event
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('title');
    const descriptionInput = document.getElementById('description');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const locationInput = document.getElementById('location');
    const priceInput = document.getElementById('price');
    const previewDiv = document.getElementById('event-preview');

    function updatePreview() {
        const title = titleInput.value || 'Event Title';
        const description = descriptionInput.value || 'Event description will appear here...';
        const startDate = startDateInput.value ? new Date(startDateInput.value).toLocaleDateString() : 'TBD';
        const location = locationInput.value || 'Location TBD';
        const currencySymbol = '{{ Auth::user()->hotel->currency_symbol ?? "$" }}';
        const price = priceInput.value ? `${currencySymbol}${parseFloat(priceInput.value).toFixed(2)}` : 'Free';

        previewDiv.innerHTML = `
            <h6 class="fw-bold">${title}</h6>
            <p class="small text-muted mb-2">${description.substring(0, 100)}${description.length > 100 ? '...' : ''}</p>
            <div class="small">
                <div><strong>Date:</strong> ${startDate}</div>
                <div><strong>Location:</strong> ${location}</div>
                <div><strong>Price:</strong> ${price}</div>
            </div>
        `;
    }

    // Update preview on input changes
    [titleInput, descriptionInput, startDateInput, locationInput, priceInput].forEach(input => {
        input.addEventListener('input', updatePreview);
    });

    // Set minimum start date to now
    const now = new Date();
    const nowString = now.toISOString().slice(0, 16);
    startDateInput.min = nowString;

    // Update end date minimum when start date changes
    startDateInput.addEventListener('change', function() {
        endDateInput.min = this.value;
        updatePreview();
    });

    // Initial preview
    updatePreview();
});
</script>
@endpush
@endsection
