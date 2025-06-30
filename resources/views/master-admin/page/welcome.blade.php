@extends('master-admin::master-admin.layout.layout-master')

@section('title', 'Welcome to Master Admin')

@section('page_title', 'Dashboard')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="#">Home</a></li>
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Welcome to Master Admin</h5>
            </div>
            <div class="card-body">
                <p>This dashboard provides quick access to common Laravel administration tasks.</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Cache Management</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ url('master-admin/commands/cache-clear') }}" class="btn btn-outline-primary">
                        <i class="bi bi-trash"></i> Clear Application Cache
                    </a>
                    <a href="{{ url('master-admin/commands/config-clear') }}" class="btn btn-outline-primary">
                        <i class="bi bi-trash"></i> Clear Config Cache
                    </a>
                    <a href="{{ url('master-admin/commands/config-cache') }}" class="btn btn-outline-primary">
                        <i class="bi bi-gear-fill"></i> Cache Config
                    </a>
                    <a href="{{ url('master-admin/commands/route-clear') }}" class="btn btn-outline-primary">
                        <i class="bi bi-trash"></i> Clear Route Cache
                    </a>
                    <a href="{{ url('master-admin/commands/route-cache') }}" class="btn btn-outline-primary">
                        <i class="bi bi-signpost-split"></i> Cache Routes
                    </a>
                    <a href="{{ url('master-admin/commands/view-clear') }}" class="btn btn-outline-primary">
                        <i class="bi bi-trash"></i> Clear View Cache
                    </a>
                    <a href="{{ url('master-admin/commands/optimize-clear') }}" class="btn btn-outline-primary">
                        <i class="bi bi-lightning"></i> Clear All Caches
                    </a>
                    <a href="{{ url('master-admin/commands/optimize') }}" class="btn btn-outline-primary">
                        <i class="bi bi-lightning-charge"></i> Optimize Application
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Database Management</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ url('master-admin/commands/migrate') }}" class="btn btn-outline-primary">
                        <i class="bi bi-database"></i> Run Migrations
                    </a>
                    <a href="{{ url('master-admin/commands/migrate-fresh') }}" class="btn btn-outline-danger">
                        <i class="bi bi-database-x"></i> Fresh Migrations
                    </a>
                    <a href="{{ url('master-admin/commands/migrate-status') }}" class="btn btn-outline-primary">
                        <i class="bi bi-list-check"></i> Migration Status
                    </a>
                    <a href="{{ url('master-admin/commands/db-seed') }}" class="btn btn-outline-primary">
                        <i class="bi bi-card-list"></i> Run Seeders
                    </a>
                    <a href="{{ url('master-admin/commands/db-wipe') }}" class="btn btn-outline-danger">
                        <i class="bi bi-exclamation-triangle"></i> Wipe Database
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">System Management</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ url('master-admin/commands/storage-link') }}" class="btn btn-outline-primary">
                        <i class="bi bi-link"></i> Create Storage Link
                    </a>
                    <a href="{{ url('master-admin/commands/key-generate') }}" class="btn btn-outline-primary">
                        <i class="bi bi-key"></i> Generate App Key
                    </a>
                    <a href="{{ url('master-admin/commands/down') }}" class="btn btn-outline-warning">
                        <i class="bi bi-slash-circle"></i> Maintenance Mode On
                    </a>
                    <a href="{{ url('master-admin/commands/up') }}" class="btn btn-outline-success">
                        <i class="bi bi-check-circle"></i> Maintenance Mode Off
                    </a>
                    <a href="{{ url('master-admin/logs/view') }}" class="btn btn-outline-info">
                        <i class="bi bi-file-text"></i> View Laravel Logs
                    </a>
                    <a href="{{ url('master-admin/logs/clear') }}" class="btn btn-outline-warning">
                        <i class="bi bi-trash"></i> Clear Laravel Logs
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">System Information</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th style="width: 30%">Laravel Version</th>
                                <td>{{ app()->version() }}</td>
                            </tr>
                            <tr>
                                <th>PHP Version</th>
                                <td>{{ phpversion() }}</td>
                            </tr>
                            <tr>
                                <th>Server</th>
                                <td>{{ isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : 'Unknown' }}</td>
                            </tr>
                            <tr>
                                <th>Environment</th>
                                <td class="d-flex justify-content-between align-items-center">
                                    <span>{{ app()->environment() }}</span>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ url('master-admin/settings/environment/local') }}" class="btn btn-outline-primary {{ app()->environment() === 'local' ? 'active' : '' }}">Local</a>
                                        <a href="{{ url('master-admin/settings/environment/testing') }}" class="btn btn-outline-primary {{ app()->environment() === 'testing' ? 'active' : '' }}">Testing</a>
                                        <a href="{{ url('master-admin/settings/environment/production') }}" class="btn btn-outline-primary {{ app()->environment() === 'production' ? 'active' : '' }}">Production</a>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Debug Mode</th>
                                <td class="d-flex justify-content-between align-items-center">
                                    <span>{{ config('app.debug') ? 'Enabled' : 'Disabled' }}</span>
                                    <a href="{{ url('master-admin/settings/debug/' . (config('app.debug') ? 'off' : 'on')) }}" 
                                       class="btn btn-sm {{ config('app.debug') ? 'btn-warning' : 'btn-success' }}">
                                        {{ config('app.debug') ? 'Disable Debug Mode' : 'Enable Debug Mode' }}
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Database Configuration</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th style="width: 30%">Database Connection</th>
                                <td>{{ config('database.default') }}</td>
                            </tr>
                            <tr>
                                <th>Database Host</th>
                                <td>{{ config('database.connections.' . config('database.default') . '.host') }}</td>
                            </tr>
                            <tr>
                                <th>Database Name</th>
                                <td>{{ config('database.connections.' . config('database.default') . '.database') }}</td>
                            </tr>
                            <tr>
                                <th>Database Username</th>
                                <td>{{ config('database.connections.' . config('database.default') . '.username') }}</td>
                            </tr>
                            <tr>
                                <th>Database Status</th>
                                <td>
                                    @php
                                        try {
                                            DB::connection()->getPdo();
                                            $dbStatus = true;
                                        } catch (\Exception $e) {
                                            $dbStatus = false;
                                        }
                                    @endphp
                                    
                                    @if($dbStatus)
                                        <span class="badge bg-success">Connected</span>
                                    @else
                                        <span class="badge bg-danger">Disconnected</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Database Config</th>
                                <td>
                                    <a href="{{ url('master-admin/database/test-connection') }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-database-check"></i> Test Connection
                                    </a>
                                    <a href="{{ url('master-admin/database/config') }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-gear"></i> Configure Database
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Attach click handlers to buttons to show loading state
        document.querySelectorAll('.card-body a.btn').forEach(button => {
            button.addEventListener('click', function(e) {
                this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
                this.classList.add('disabled');
            });
        });
    });
</script>
@endpush
