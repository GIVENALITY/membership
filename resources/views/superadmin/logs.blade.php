@extends('layouts.app')

@section('title', __('app.system_logs'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="icon-base ri ri-file-list-line me-2"></i>
                        {{ __('app.system_logs') }}
                    </h4>
                    <p class="card-subtitle text-muted">{{ __('app.view_system_logs_and_errors') }}</p>
                </div>
                <div class="card-body">
                    @if(empty($logs))
                        <div class="text-center py-4">
                            <div class="text-muted">
                                <i class="icon-base ri ri-file-list-line fs-1 mb-3"></i>
                                <h6>{{ __('app.no_logs_found') }}</h6>
                                <p>{{ __('app.no_logs_description') }}</p>
                            </div>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>{{ __('app.timestamp') }}</th>
                                        <th>{{ __('app.level') }}</th>
                                        <th>{{ __('app.message') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($logs as $log)
                                        @php
                                            $logParts = explode('] ', $log, 2);
                                            if (count($logParts) >= 2) {
                                                $timestamp = trim($logParts[0], '[]');
                                                $message = $logParts[1];
                                                
                                                // Determine log level
                                                $level = 'info';
                                                if (str_contains(strtolower($message), 'error')) {
                                                    $level = 'error';
                                                } elseif (str_contains(strtolower($message), 'warning')) {
                                                    $level = 'warning';
                                                } elseif (str_contains(strtolower($message), 'debug')) {
                                                    $level = 'debug';
                                                }
                                            } else {
                                                $timestamp = '';
                                                $message = $log;
                                                $level = 'info';
                                            }
                                        @endphp
                                        <tr>
                                            <td>
                                                <small class="text-muted">{{ $timestamp }}</small>
                                            </td>
                                            <td>
                                                @if($level === 'error')
                                                    <span class="badge bg-danger">{{ __('app.error') }}</span>
                                                @elseif($level === 'warning')
                                                    <span class="badge bg-warning">{{ __('app.warning') }}</span>
                                                @elseif($level === 'debug')
                                                    <span class="badge bg-secondary">{{ __('app.debug') }}</span>
                                                @else
                                                    <span class="badge bg-info">{{ __('app.info') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <code class="small">{{ $message }}</code>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 