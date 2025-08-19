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
                                    </div>
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

function updateColorPreview() {
    const primaryColor = document.getElementById('primary_color').value;
    const secondaryColor = document.getElementById('secondary_color').value;
    
    const previews = document.querySelectorAll('.color-preview');
    if (previews.length >= 2) {
        previews[0].style.backgroundColor = primaryColor;
        previews[1].style.backgroundColor = secondaryColor;
    }
}
</script>
@endpush 