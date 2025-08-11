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
                                <label for="hotel_name" class="form-label">Hotel Name *</label>
                                <input type="text" class="form-control" id="hotel_name" name="hotel_name" 
                                       value="{{ old('hotel_name', $hotel->name) }}" required />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="hotel_email" class="form-label">Hotel Email *</label>
                                <input type="email" class="form-control" id="hotel_email" name="hotel_email" 
                                       value="{{ old('hotel_email', $hotel->email) }}" required />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="hotel_phone" class="form-label">Hotel Phone</label>
                                <input type="text" class="form-control" id="hotel_phone" name="hotel_phone" 
                                       value="{{ old('hotel_phone', $hotel->phone) }}" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="hotel_website" class="form-label">Hotel Website</label>
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
                                <label for="hotel_logo" class="form-label">Hotel Logo</label>
                                @if($hotel->logo_path)
                                    <div class="mb-2">
                                        <img src="{{ $hotel->logo_url }}" alt="Current Logo" class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                @endif
                                <input type="file" class="form-control" id="hotel_logo" name="hotel_logo" accept="image/*" />
                                <small class="form-text text-muted">Upload a new logo to replace the current one</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="hotel_banner" class="form-label">Hotel Banner</label>
                                @if($hotel->banner_path)
                                    <div class="mb-2">
                                        <img src="{{ $hotel->banner_url }}" alt="Current Banner" class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                @endif
                                <input type="file" class="form-control" id="hotel_banner" name="hotel_banner" accept="image/*" />
                                <small class="form-text text-muted">Upload a new banner to replace the current one</small>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Update Hotel Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 