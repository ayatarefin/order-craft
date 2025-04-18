@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Create New Order</h1>
        <a href="{{ route('orders.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Orders
        </a>
    </div>
    
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Order Details</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-3">
                    <label for="name" class="form-label">Order Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="files" class="form-label">Upload Files (ZIP) <span class="text-danger">*</span></label>
                    <input type="file" class="form-control @error('files') is-invalid @enderror" id="files" name="files" accept=".zip" required>
                    @error('files')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        Upload a ZIP file containing all the image files organized in folders. The system will maintain your folder structure.
                    </small>
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Upload Instructions:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Create a ZIP file with your folder structure containing all image files that need editing.</li>
                        <li>The system will extract and process all image files while preserving your folder structure.</li>
                        <li>Supported file types: JPEG, PNG, GIF, PSD, AI, and other common image formats.</li>
                        <li>Maximum file size: 500MB. For larger collections, please split them into multiple orders.</li>
                    </ul>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-secondary me-md-2">
                        <i class="fas fa-redo me-1"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Create Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection