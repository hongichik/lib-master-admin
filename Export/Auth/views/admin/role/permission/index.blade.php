@extends('layouts.layout-master')

@section('title', 'Quản lý Quyền')
@section('page_title', 'Quản lý Quyền')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Danh sách quyền</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.role.permission.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Thêm quyền
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="permissions-table">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th>Tên quyền</th>
                                <th>Slug</th>
                                <th>Mô tả</th>
                                <th>Icon</th>
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
    $('#permissions-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{!! route('admin.role.permission.index') !!}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'slug', name: 'slug' },
            { data: 'description', name: 'description' },
            { data: 'icon', name: 'icon', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });
});
</script>
@endpush
