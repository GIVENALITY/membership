@extends('layouts.app')

@section('title', __('app.view_card') . ' - ' . $member->full_name)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">
                        <i class="icon-base ri ri-credit-card-line me-2"></i>
                        {{ __('app.member_card') }} - {{ $member->full_name }}
                    </h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('members.cards.download', $member) }}" class="btn btn-primary">
                            <i class="icon-base ri ri-download-line me-2"></i>
                            {{ __('app.download_card') }}
                        </a>
                        <a href="{{ route('members.cards.index') }}" class="btn btn-secondary">
                            <i class="icon-base ri ri-arrow-left-line me-2"></i>
                            {{ __('app.back_to_cards') }}
                        </a>
                    </div>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <h5>{{ __('app.member_details') }}</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>{{ __('app.name') }}:</strong> {{ $member->full_name }}</p>
                                <p><strong>{{ __('app.membership_id') }}:</strong> {{ $member->membership_id }}</p>
                                <p><strong>{{ __('app.email') }}:</strong> {{ $member->email }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>{{ __('app.membership_type') }}:</strong> 
                                    {{ $member->membershipType ? $member->membershipType->name : __('app.no_type') }}
                                </p>
                                <p><strong>{{ __('app.hotel') }}:</strong> 
                                    {{ $member->hotel ? $member->hotel->name : __('app.no_hotel') }}
                                </p>
                                <p><strong>{{ __('app.join_date') }}:</strong> 
                                    {{ $member->join_date ? $member->join_date->format('M d, Y') : __('app.not_available') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card-preview">
                        <h5 class="mb-3">{{ __('app.card_preview') }}</h5>
                        <div class="card-image-container">
                            <img src="{{ $cardUrl }}" alt="{{ __('app.member_card') }}" class="img-fluid rounded shadow" style="max-width: 400px;">
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="alert alert-info">
                            <i class="icon-base ri ri-information-line me-2"></i>
                            {{ __('app.card_info') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
