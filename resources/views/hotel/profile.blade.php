@extends('layouts.app')

@section('title', 'Hotel Profile - Membership MS')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <h5 class="card-header">Hotel Profile Management</h5>
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

                    <form action="{{ route('hotel.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Hotel Information -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="hotel_name" class="form-label">Restaurant Name *</label>
                                <input type="text" class="form-control" id="hotel_name" name="hotel_name" 
                                       value="{{ old('hotel_name', $hotel->name) }}" required />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="hotel_email" class="form-label">Restaurant Email *</label>
                                <input type="email" class="form-control" id="hotel_email" name="hotel_email" 
                                       value="{{ old('hotel_email', $hotel->email) }}" required />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="hotel_phone" class="form-label">Restaurant Phone</label>
                                <input type="text" class="form-control" id="hotel_phone" name="hotel_phone" 
                                       value="{{ old('hotel_phone', $hotel->phone) }}" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="hotel_website" class="form-label">Restaurant Website</label>
                                <input type="url" class="form-control" id="hotel_website" name="hotel_website" 
                                       value="{{ old('hotel_website', $hotel->website) }}" placeholder="https://example.com" />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="hotel_city" class="form-label">City</label>
                                <input type="text" class="form-control" id="hotel_city" name="hotel_city" 
                                       value="{{ old('hotel_city', $hotel->city) }}" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="hotel_country" class="form-label">Country</label>
                                <input type="text" class="form-control" id="hotel_country" name="hotel_country" 
                                       value="{{ old('hotel_country', $hotel->country) }}" />
                            </div>
                        </div>

                        <!-- Currency Settings -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="icon-base ri ri-money-dollar-circle-line me-2"></i>
                                    Currency Settings
                                </h6>
                                <small class="text-muted">Set your restaurant's currency for pricing</small>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="currency" class="form-label">Currency</label>
                                        <select class="form-select" id="currency" name="currency" required>
                                            @foreach(\App\Models\Hotel::getAvailableCurrencies() as $code => $currency)
                                                <option value="{{ $code }}" 
                                                        {{ old('currency', $hotel->currency ?? 'USD') === $code ? 'selected' : '' }}>
                                                    {{ $currency['name'] }} ({{ $code }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">This will be used for all pricing in your restaurant</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="currency_symbol" class="form-label">Currency Symbol</label>
                                        <input type="text" class="form-control" id="currency_symbol" name="currency_symbol" 
                                               value="{{ old('currency_symbol', $hotel->currency_symbol ?? '$') }}" 
                                               maxlength="5" required />
                                        <small class="form-text text-muted">Symbol used to display prices (e.g., $, €, TSh)</small>
                                    </div>
                                </div>
                                
                                <!-- Currency Preview -->
                                <div class="mt-3">
                                    <label class="form-label">Currency Preview</label>
                                    <div class="d-flex gap-3">
                                        <div class="badge bg-primary fs-6">
                                            {{ $hotel->formatAmount(1000) }}
                                        </div>
                                        <div class="badge bg-success fs-6">
                                            {{ $hotel->formatAmount(0) }}
                                        </div>
                                        <div class="badge bg-info fs-6">
                                            {{ $hotel->formatAmount(2500.50) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="hotel_address" class="form-label">Address</label>
                            <textarea class="form-control" id="hotel_address" name="hotel_address" 
                                      rows="3">{{ old('hotel_address', $hotel->address) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="hotel_description" class="form-label">Description</label>
                            <textarea class="form-control" id="hotel_description" name="hotel_description" 
                                      rows="4">{{ old('hotel_description', $hotel->description) }}</textarea>
                        </div>

                        <!-- Hotel Images -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="hotel_logo" class="form-label">Restaurant Logo</label>
                                @if($hotel->logo_path)
                                    <div class="mb-2">
                                        <img src="{{ $hotel->logo_url }}" alt="Current Logo" class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                @endif
                                <input type="file" class="form-control" id="hotel_logo" name="hotel_logo" accept="image/*" />
                                <small class="form-text text-muted">Upload a new logo to replace the current one</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="hotel_banner" class="form-label">Restaurant Banner</label>
                                @if($hotel->banner_path)
                                    <div class="mb-2">
                                        <img src="{{ $hotel->banner_url }}" alt="Current Banner" class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                @endif
                                <input type="file" class="form-control" id="hotel_banner" name="hotel_banner" accept="image/*" />
                                <small class="form-text text-muted">Upload a new banner to replace the current one</small>
                            </div>
                        </div>

                        <!-- Branding Colors -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="icon-base ri ri-palette-line me-2"></i>
                                    Branding Colors
                                </h6>
                                <small class="text-muted">Customize your restaurant's color scheme</small>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="primary_color" class="form-label">Primary Color</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" 
                                                   id="primary_color" name="primary_color" 
                                                   value="{{ old('primary_color', $hotel->primary_color ?? '#007bff') }}" 
                                                   title="Choose primary color">
                                            <input type="text" class="form-control" 
                                                   value="{{ old('primary_color', $hotel->primary_color ?? '#007bff') }}" 
                                                   id="primary_color_text" placeholder="#007bff">
                                        </div>
                                        <small class="form-text text-muted">Used for buttons, links, and primary elements</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="secondary_color" class="form-label">Secondary Color</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" 
                                                   id="secondary_color" name="secondary_color" 
                                                   value="{{ old('secondary_color', $hotel->secondary_color ?? '#6c757d') }}" 
                                                   title="Choose secondary color">
                                            <input type="text" class="form-control" 
                                                   value="{{ old('secondary_color', $hotel->secondary_color ?? '#6c757d') }}" 
                                                   id="secondary_color_text" placeholder="#6c757d">
                                        </div>
                                        <small class="form-text text-muted">Used for secondary elements and accents</small>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="tertiary_color" class="form-label">Tertiary Color</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" 
                                                   id="tertiary_color" name="tertiary_color" 
                                                   value="{{ old('tertiary_color', $hotel->tertiary_color ?? '#28a745') }}" 
                                                   title="Choose tertiary color">
                                            <input type="text" class="form-control" 
                                                   value="{{ old('tertiary_color', $hotel->tertiary_color ?? '#28a745') }}" 
                                                   id="tertiary_color_text" placeholder="#28a745">
                                        </div>
                                        <small class="form-text text-muted">Used for success states and special elements</small>
                                    </div>
                                </div>
                                
                                <!-- Color Preview -->
                                <div class="mt-3">
                                    <label class="form-label">Color Preview</label>
                                    <div class="d-flex gap-3">
                                        <div class="color-preview" style="background-color: {{ $hotel->primary_color ?? '#007bff' }}; width: 60px; height: 40px; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: bold;">
                                            Primary
                                        </div>
                                        <div class="color-preview" style="background-color: {{ $hotel->secondary_color ?? '#6c757d' }}; width: 60px; height: 40px; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: bold;">
                                            Secondary
                                        </div>
                                        <div class="color-preview" style="background-color: {{ $hotel->tertiary_color ?? '#28a745' }}; width: 60px; height: 40px; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: bold;">
                                            Tertiary
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Email Settings -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="icon-base ri ri-mail-line me-2"></i>
                                    Email Settings
                                </h6>
                                <small class="text-muted">Configure email branding and sender information</small>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="reply_to_email" class="form-label">Reply-To Email</label>
                                        <input type="email" class="form-control" id="reply_to_email" name="reply_to_email" 
                                               value="{{ old('reply_to_email', $hotel->reply_to_email) }}" placeholder="contact@yourhotel.com">
                                        <small class="form-text text-muted">Email address where replies will be sent (optional)</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="alert alert-info mb-0">
                                            <i class="icon-base ri ri-information-line me-2"></i>
                                            <strong>Sender:</strong> Emails will be sent from the system email address but will display as "{{ $hotel->name }}" as the sender name.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="email_logo_url" class="form-label">Email Logo URL</label>
                                        <input type="url" class="form-control" id="email_logo_url" name="email_logo_url" 
                                               value="{{ old('email_logo_url', $hotel->email_logo_url) }}" placeholder="https://example.com/logo.png">
                                        <small class="form-text text-muted">URL to your logo for email headers (optional)</small>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="email_primary_color" class="form-label">Email Primary Color</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" 
                                                   id="email_primary_color" name="email_primary_color" 
                                                   value="{{ old('email_primary_color', $hotel->email_primary_color ?? '#1976d2') }}" 
                                                   title="Choose email primary color">
                                            <input type="text" class="form-control" 
                                                   value="{{ old('email_primary_color', $hotel->email_primary_color ?? '#1976d2') }}" 
                                                   id="email_primary_color_text" placeholder="#1976d2">
                                        </div>
                                        <small class="form-text text-muted">Main brand color for emails</small>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="email_secondary_color" class="form-label">Email Secondary Color</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" 
                                                   id="email_secondary_color" name="email_secondary_color" 
                                                   value="{{ old('email_secondary_color', $hotel->email_secondary_color ?? '#f8f9fa') }}" 
                                                   title="Choose email secondary color">
                                            <input type="text" class="form-control" 
                                                   value="{{ old('email_secondary_color', $hotel->email_secondary_color ?? '#f8f9fa') }}" 
                                                   id="email_secondary_color_text" placeholder="#f8f9fa">
                                        </div>
                                        <small class="form-text text-muted">Background color for headers/footers</small>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="email_accent_color" class="form-label">Email Accent Color</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" 
                                                   id="email_accent_color" name="email_accent_color" 
                                                   value="{{ old('email_accent_color', $hotel->email_accent_color ?? '#e3f2fd') }}" 
                                                   title="Choose email accent color">
                                            <input type="text" class="form-control" 
                                                   value="{{ old('email_accent_color', $hotel->email_accent_color ?? '#e3f2fd') }}" 
                                                   id="email_accent_color_text" placeholder="#e3f2fd">
                                        </div>
                                        <small class="form-text text-muted">Highlight color for member info boxes</small>
                                    </div>
                                </div>
                                
                                <!-- Email Color Preview -->
                                <div class="mt-3">
                                    <label class="form-label">Email Color Preview</label>
                                    <div class="d-flex gap-3">
                                        <div class="email-color-preview" style="background-color: {{ $hotel->email_primary_color ?? '#1976d2' }}; width: 60px; height: 40px; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: bold;">
                                            Primary
                                        </div>
                                        <div class="email-color-preview" style="background-color: {{ $hotel->email_secondary_color ?? '#f8f9fa' }}; width: 60px; height: 40px; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #333; font-size: 12px; font-weight: bold; border: 1px solid #ddd;">
                                            Secondary
                                        </div>
                                        <div class="email-color-preview" style="background-color: {{ $hotel->email_accent_color ?? '#e3f2fd' }}; width: 60px; height: 40px; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #333; font-size: 12px; font-weight: bold; border: 1px solid #ddd;">
                                            Accent
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info mt-3">
                                    <i class="icon-base ri ri-information-line me-2"></i>
                                    <strong>Note:</strong> These settings will be used in all member emails sent from your hotel.
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Update Restaurant Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-js')
<script>
// Currency auto-update
document.getElementById('currency').addEventListener('change', function() {
    const currencies = @json(\App\Models\Hotel::getAvailableCurrencies());
    const selectedCurrency = this.value;
    const symbolInput = document.getElementById('currency_symbol');
    
    if (currencies[selectedCurrency]) {
        symbolInput.value = currencies[selectedCurrency].symbol;
    }
});

// Sync color picker and text input
document.getElementById('primary_color').addEventListener('input', function() {
    document.getElementById('primary_color_text').value = this.value;
    updateColorPreview();
});

document.getElementById('primary_color_text').addEventListener('input', function() {
    document.getElementById('primary_color').value = this.value;
    updateColorPreview();
});

document.getElementById('secondary_color').addEventListener('input', function() {
    document.getElementById('secondary_color_text').value = this.value;
    updateColorPreview();
});

document.getElementById('secondary_color_text').addEventListener('input', function() {
    document.getElementById('secondary_color').value = this.value;
    updateColorPreview();
});

document.getElementById('tertiary_color').addEventListener('input', function() {
    document.getElementById('tertiary_color_text').value = this.value;
    updateColorPreview();
});

document.getElementById('tertiary_color_text').addEventListener('input', function() {
    document.getElementById('tertiary_color').value = this.value;
    updateColorPreview();
});

function updateColorPreview() {
    const primaryColor = document.getElementById('primary_color').value;
    const secondaryColor = document.getElementById('secondary_color').value;
    const tertiaryColor = document.getElementById('tertiary_color').value;
    
    const previews = document.querySelectorAll('.color-preview');
    if (previews.length >= 3) {
        previews[0].style.backgroundColor = primaryColor;
        previews[1].style.backgroundColor = secondaryColor;
        previews[2].style.backgroundColor = tertiaryColor;
    }
}

// Email color picker sync
document.getElementById('email_primary_color').addEventListener('input', function() {
    document.getElementById('email_primary_color_text').value = this.value;
    updateEmailColorPreview();
});

document.getElementById('email_primary_color_text').addEventListener('input', function() {
    document.getElementById('email_primary_color').value = this.value;
    updateEmailColorPreview();
});

document.getElementById('email_secondary_color').addEventListener('input', function() {
    document.getElementById('email_secondary_color_text').value = this.value;
    updateEmailColorPreview();
});

document.getElementById('email_secondary_color_text').addEventListener('input', function() {
    document.getElementById('email_secondary_color').value = this.value;
    updateEmailColorPreview();
});

document.getElementById('email_accent_color').addEventListener('input', function() {
    document.getElementById('email_accent_color_text').value = this.value;
    updateEmailColorPreview();
});

document.getElementById('email_accent_color_text').addEventListener('input', function() {
    document.getElementById('email_accent_color').value = this.value;
    updateEmailColorPreview();
});

function updateEmailColorPreview() {
    const primaryColor = document.getElementById('email_primary_color').value;
    const secondaryColor = document.getElementById('email_secondary_color').value;
    const accentColor = document.getElementById('email_accent_color').value;
    
    const previews = document.querySelectorAll('.email-color-preview');
    if (previews.length >= 3) {
        previews[0].style.backgroundColor = primaryColor;
        previews[1].style.backgroundColor = secondaryColor;
        previews[2].style.backgroundColor = accentColor;
    }
}
</script>
@endpush 