@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Edit {{ $file->original_name }}</h1>
        <div>
            <a href="{{ route('files.show', $file) }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i> Back to File
            </a>
            <a href="{{ route('files.open-in-editor', $file) }}" class="btn btn-primary">
                <i class="fas fa-external-link-alt me-1"></i> Open in Editor
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Current Version</h5>
                </div>
                <div class="card-body text-center">
                    <img src="{{ asset('storage/' . $file->path) }}" class="img-fluid" alt="{{ $file->original_name }}" style="max-height: 400px;">
                </div>
                <div class="card-footer bg-white">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Original Name:</dt>
                        <dd class="col-sm-8">{{ $file->original_name }}</dd>
                        
                        <dt class="col-sm-4">File Type:</dt>
                        <dd class="col-sm-8">{{ $file->mime_type }}</dd>
                        
                        <dt class="col-sm-4">Size:</dt>
                        <dd class="col-sm-8">{{ round($file->size / 1024, 2) }} KB</dd>
                        
                        <dt class="col-sm-4">Last Updated:</dt>
                        <dd class="col-sm-8">{{ $file->updated_at->format('M d, Y H:i') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Upload New Version</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('files.update', $file) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="file" class="form-label">Select New File <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" required>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Supported file types: JPEG, PNG, GIF, PSD, AI, and other common image formats.
                            </small>
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Warning:</strong> Uploading a new version will replace the current file. This action cannot be undone.
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Upload New Version
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('files.open-in-editor', $file) }}" class="btn btn-info">
                            <i class="fas fa-external-link-alt me-1"></i> Open in External Editor
                        </a>
                        
                        @if($file->status !== 'completed')
                            <form action="{{ route('files.complete', $file) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-check me-1"></i> Mark as Completed
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection