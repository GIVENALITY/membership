@extends('layouts.app')

@section('title', 'Member Emails')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">
                        <i class="icon-base ri ri-mail-line me-2"></i>
                        Member Email Management
                    </h4>
                    <a href="{{ route('members.emails.compose') }}" class="btn btn-primary">
                        <i class="icon-base ri ri-mail-add-line me-2"></i>
                        Compose Email
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <i class="icon-base ri ri-check-line me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <i class="icon-base ri ri-error-warning-line me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $totalMembers }}</h4>
                                            <small>Total Members</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="icon-base ri ri-user-line fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $activeMembers }}</h4>
                                            <small>Active Members</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="icon-base ri ri-user-star-line fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $inactiveMembers }}</h4>
                                            <small>Inactive Members</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="icon-base ri ri-user-unfollow-line fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $totalMembers - $inactiveMembers }}</h4>
                                            <small>With Email</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="icon-base ri ri-mail-line fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="icon-base ri ri-flashlight-line me-2"></i>
                                        Quick Actions
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('members.emails.compose') }}?type=all" class="btn btn-outline-primary w-100">
                                                <i class="icon-base ri ri-mail-send-line me-2"></i>
                                                Email All Members
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('members.emails.compose') }}?type=active" class="btn btn-outline-success w-100">
                                                <i class="icon-base ri ri-mail-send-line me-2"></i>
                                                Email Active Members
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('members.emails.compose') }}?type=inactive" class="btn btn-outline-warning w-100">
                                                <i class="icon-base ri ri-mail-send-line me-2"></i>
                                                Email Inactive Members
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('members.emails.compose') }}?type=selected" class="btn btn-outline-info w-100">
                                                <i class="icon-base ri ri-user-settings-line me-2"></i>
                                                Email Selected Members
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('members.emails.compose') }}?type=custom" class="btn btn-outline-warning w-100">
                                                <i class="icon-base ri ri-mail-add-line me-2"></i>
                                                Email Custom Addresses
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Templates -->
                    @if($recentTemplates->isNotEmpty())
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="icon-base ri ri-file-list-line me-2"></i>
                                        Recent Email Templates
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Template Name</th>
                                                    <th>Subject</th>
                                                    <th>Created</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($recentTemplates as $template)
                                                <tr>
                                                    <td>{{ $template->name }}</td>
                                                    <td>{{ $template->subject }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($template->created_at)->format('M d, Y H:i') }}</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary" onclick="loadTemplate({{ $template->id }})">
                                                            <i class="icon-base ri ri-edit-line"></i>
                                                            Use Template
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function loadTemplate(templateId) {
    // Redirect to compose page with template ID
    window.location.href = `{{ route('members.emails.compose') }}?template=${templateId}`;
}
</script>
@endpush
@endsection
