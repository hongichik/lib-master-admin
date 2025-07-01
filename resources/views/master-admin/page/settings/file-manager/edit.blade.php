@extends('master-admin::master-admin.layout.layout-master')

@section('title', 'Edit File: ' . $fileName)

@section('page_title', 'Edit File: ' . $fileName)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="mb-0">{{ $fileName }}</h5>
                <a href="{{ route('master-admin.settings.file-manager.index', ['disk' => $disk, 'path' => dirname($filePath)]) }}" 
                   class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('master-admin.settings.file-manager.edit') }}" method="POST">
                    @csrf
                    <input type="hidden" name="path" value="{{ $filePath }}">
                    <input type="hidden" name="disk" value="{{ $disk }}">
                    
                    <div class="mb-3">
                        <textarea name="content" class="form-control" rows="20" style="font-family: monospace;">{{ $content }}</textarea>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('master-admin.settings.file-manager.view', ['path' => $filePath, 'disk' => $disk]) }}" 
                           class="btn btn-info">
                            <i class="bi bi-eye"></i> View
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
