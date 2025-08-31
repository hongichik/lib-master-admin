<!-- blade -->
@extends('layouts.layout-master')

@section('title', 'Quản lý Tài khoản Admin')
@section('page_title', 'Quản lý Tài khoản Admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Danh sách tài khoản</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.role.admin.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Thêm tài khoản
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="admins-table">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th>Tên</th>
                                <th>Email</th>
                                <th>Vai trò</th>
                                <th>Trạng thái</th>
                                <th width="15%">Hành động</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    $('#admins-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{!! route('admin.role.admin.index') !!}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'roles', name: 'roles', orderable: false, searchable: false },
            { data: 'is_active', name: 'is_active', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });
});
</script>
@endpush
