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
                    <label for="files" class="form-label">Upload Images <span class="text-danger">*</span></label>
                    <input type="file" class="form-control @error('files.*') is-invalid @enderror" id="files" name="files[]" multiple accept=".jpg,.jpeg,.png,.webp" required>
                    @error('files.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        You can select and upload multiple image files. Supported formats: JPEG, PNG, WebP.
                    </small>
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Upload Instructions:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Select multiple image files (JPEG, PNG, WebP) directly from your computer.</li>
                        <li>The system will organize them automatically under a root folder.</li>
                        <li>Maximum size per file: 10MB. Please avoid uploading unnecessary large files.</li>
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
