@extends('layouts.layout-master')

@section('title', 'Sửa Quyền')
@section('page_title', 'Sửa Quyền')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Sửa quyền</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.permission.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
            <form action="{{ route('admin.permission.update', $permission) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Tên quyền <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $permission->name) }}" required>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="slug">Slug <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $permission->slug) }}" required>
                        @error('slug')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="description">Mô tả</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $permission->description) }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="icon">Icon (class FontAwesome)</label>
                        <select id="icon_select" class="form-control">
                            <option value="">-- Chọn icon --</option>
                        </select>
                        <input type="hidden" id="icon" name="icon" value="{{ old('icon', $permission->icon) }}">
                        <div class="mt-2"><span id="icon_preview"></span></div>
                        @error('icon')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Cập nhật Quyền
                    </button>
                    <a href="{{ route('admin.permission.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .select2-results__option .fa, .select2-selection__rendered .fa { margin-right: 8px; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const icons = [
        'fas fa-user','fas fa-user-tie','fas fa-user-md','fas fa-user-nurse','fas fa-user-secret','fas fa-user-cog','fas fa-user-shield','fas fa-user-plus','fas fa-user-minus','fas fa-users','fas fa-users-cog',
        'fas fa-address-book','fas fa-address-card','fas fa-id-badge','fas fa-key','fas fa-lock','fas fa-unlock','fas fa-university','fas fa-home','fas fa-building','fas fa-briefcase',
        'fas fa-clipboard','fas fa-clipboard-list','fas fa-file','fas fa-file-alt','fas fa-file-invoice','fas fa-file-contract','fas fa-folder','fas fa-folder-open','fas fa-paperclip','fas fa-paper-plane',
        'fas fa-search','fas fa-search-plus','fas fa-search-minus','fas fa-plus','fas fa-minus','fas fa-edit','fas fa-pen','fas fa-pencil-alt','fas fa-trash','fas fa-cog','fas fa-tools','fas fa-wrench','fas fa-hammer',
        'fas fa-bolt','fas fa-sync','fas fa-exchange-alt','fas fa-chart-bar','fas fa-chart-line','fas fa-chart-pie','fas fa-chart-area','fas fa-dollar-sign','fas fa-money-bill','fas fa-money-bill-wave','fas fa-credit-card','fas fa-hand-holding-usd',
        'fas fa-calendar','fas fa-calendar-alt','fas fa-clock','fas fa-bell','fas fa-bell-slash','fas fa-envelope','fas fa-link','fas fa-globe','fas fa-map-marker-alt','fas fa-location-arrow',
        'fas fa-book','fas fa-book-open','fas fa-graduation-cap','fas fa-lightbulb','fas fa-eye','fas fa-eye-slash','fas fa-shield-alt','fas fa-shield-virus','fas fa-heart','fas fa-star',
        'fas fa-thumbs-up','fas fa-thumbs-down','fas fa-check','fas fa-check-circle','fas fa-times','fas fa-exclamation-triangle','fas fa-info-circle','fas fa-download','fas fa-upload','fas fa-cloud','fas fa-cloud-upload-alt','fas fa-file-download',
        'fas fa-print','fas fa-mobile-alt','fas fa-desktop','fas fa-laptop','fas fa-map','fas fa-map-pin','fas fa-compass'
    ];

    const $select = $('#icon_select');
    icons.forEach(ic => {
        const $opt = $('<option>').val(ic).html('<i class="' + ic + '"></i> ' + ic);
        $select.append($opt);
    });

    function formatIcon(opt) {
        if (!opt.id) return opt.text;
        return $('<span><i class="' + opt.id + '"></i> ' + opt.text + '</span>');
    }

    $select.select2({
        width: '100%',
        templateResult: formatIcon,
        templateSelection: formatIcon,
        escapeMarkup: function(m) { return m; }
    });

    // Set initial value if exists
    const initial = $('#icon').val();
    if (initial) {
        $select.val(initial).trigger('change');
        $('#icon_preview').html('<i class="' + initial + '"></i>');
    }

    $select.on('change', function() {
        const val = $(this).val();
        $('#icon').val(val);
        $('#icon_preview').html(val ? '<i class="' + val + '"></i>' : '');
    });
});
</script>
@endpush
