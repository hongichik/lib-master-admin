<!-- blade -->
@extends('layouts.layout-master')

@section('title', 'Quản lý Vai trò')
@section('page_title', 'Quản lý Vai trò')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Danh sách vai trò</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.role.role.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Thêm vai trò
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="roles-table">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th>Tên</th>
                                <th>Slug</th>
                                <th>Mô tả</th>
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
    $('#roles-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{!! route('admin.role.role.index') !!}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'slug', name: 'slug' },
            { data: 'description', name: 'description' },
            { data: 'is_active', name: 'is_active', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });
});
</script>
@endpush
