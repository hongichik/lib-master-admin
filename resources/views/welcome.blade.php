@extends('master-admin::master-admin.layout.layout-master')

@section('title', 'Welcome to Master Admin')

@section('page_title', 'Welcome')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="#">Home</a></li>
<li class="breadcrumb-item active" aria-current="page">Welcome</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Welcome to Master Admin</h5>
            </div>
            <div class="card-body">
                <p>This is a demonstration of the Master Admin package with AdminLTE integration.</p>
                <p>Use this template as a starting point for your admin interfaces.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/master-admin/js/app.js') }}"></script>
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/master-admin/css/app.css') }}">
@endpush