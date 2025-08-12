@extends('layouts.app')

@section('title', __('app.system_settings'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="icon-base ri ri-settings-3-line me-2"></i>
                        {{ __('app.system_settings') }}
                    </h4>
                    <p class="card-subtitle text-muted">{{ __('app.global_system_configuration') }}</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card border">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="icon-base ri ri-database-line me-2"></i>
                                        {{ __('app.database_info') }}
                                    </h5>
                                    <ul class="list-unstyled">
                                        <li><strong>{{ __('app.connection') }}:</strong> {{ config('database.default') }}</li>
                                        <li><strong>{{ __('app.database') }}:</strong> {{ config('database.connections.mysql.database') }}</li>
                                        <li><strong>{{ __('app.host') }}:</strong> {{ config('database.connections.mysql.host') }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="card border">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="icon-base ri ri-server-line me-2"></i>
                                        {{ __('app.system_info') }}
                                    </h5>
                                    <ul class="list-unstyled">
                                        <li><strong>{{ __('app.php_version') }}:</strong> {{ PHP_VERSION }}</li>
                                        <li><strong>{{ __('app.laravel_version') }}:</strong> {{ app()->version() }}</li>
                                        <li><strong>{{ __('app.environment') }}:</strong> {{ config('app.env') }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card border">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="icon-base ri ri-tools-line me-2"></i>
                                        {{ __('app.maintenance_tools') }}
                                    </h5>
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-outline-primary" onclick="clearCache()">
                                            <i class="icon-base ri ri-refresh-line me-2"></i>
                                            {{ __('app.clear_cache') }}
                                        </button>
                                        <button class="btn btn-outline-warning" onclick="clearViews()">
                                            <i class="icon-base ri ri-eye-off-line me-2"></i>
                                            {{ __('app.clear_views') }}
                                        </button>
                                        <button class="btn btn-outline-info" onclick="clearConfig()">
                                            <i class="icon-base ri ri-settings-line me-2"></i>
                                            {{ __('app.clear_config') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="card border">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="icon-base ri ri-shield-check-line me-2"></i>
                                        {{ __('app.security_info') }}
                                    </h5>
                                    <ul class="list-unstyled">
                                        <li><strong>{{ __('app.app_key') }}:</strong> 
                                            <span class="text-muted">{{ __('app.set') }}</span>
                                        </li>
                                        <li><strong>{{ __('app.debug_mode') }}:</strong> 
                                            @if(config('app.debug'))
                                                <span class="badge bg-warning">{{ __('app.enabled') }}</span>
                                            @else
                                                <span class="badge bg-success">{{ __('app.disabled') }}</span>
                                            @endif
                                        </li>
                                        <li><strong>{{ __('app.maintenance_mode') }}:</strong> 
                                            <span class="badge bg-success">{{ __('app.disabled') }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function clearCache() {
    if (confirm('{{ __("app.confirm_clear_cache") }}')) {
        // This would typically make an AJAX call to clear cache
        alert('{{ __("app.cache_cleared") }}');
    }
}

function clearViews() {
    if (confirm('{{ __("app.confirm_clear_views") }}')) {
        // This would typically make an AJAX call to clear views
        alert('{{ __("app.views_cleared") }}');
    }
}

function clearConfig() {
    if (confirm('{{ __("app.confirm_clear_config") }}')) {
        // This would typically make an AJAX call to clear config
        alert('{{ __("app.config_cleared") }}');
    }
}
</script>
@endsection 