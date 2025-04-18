@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ $folder->name }}</h1>
        <div>
            <a href="{{ route('orders.show', $folder->order) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Order
            </a>
        </div>
    </div>
    
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('orders.show', $folder->order) }}">
                    <i class="fas fa-folder me-1"></i> {{ $folder->order->name }}
                </a>
            </li>
            
            @php
                $ancestors = [];
                $parent = $folder->parent;
                
                while ($parent) {
                    array_unshift($ancestors, $parent);
                    $parent = $parent->parent;
                }
            @endphp
            
            @foreach($ancestors as $ancestor)
                <li class="breadcrumb-item">
                    <a href="{{ route('folders.show', $ancestor) }}">
                        {{ $ancestor->name }}
                    </a>
                </li>
            @endforeach
            
            <li class="breadcrumb-item active" aria-current="page">
                {{ $folder->name }}
            </li>
        </ol>
    </nav>
    
    <div class="row mb-4">
        <!-- Subfolders -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Subfolders</h5>
                </div>
                <div class="card-body p-0">
                    @if($subfolders->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($subfolders as $subfolder)
                                <a href="{{ route('folders.show', $subfolder) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-folder me-2 text-warning"></i> {{ $subfolder->name }}
                                    </div>
                                    <span class="badge bg-primary rounded-pill">
                                        {{ $subfolder->files->count() + $subfolder->children->sum(function($child) { return $child->files->count(); }) }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-folder-open fa-2x mb-2 text-muted"></i>
                            <p class="mb-0">No subfolders found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Folder Stats -->
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Folder Statistics</h5>
                </div>
                <div class="card-body">
                    @php
                        $totalFiles = $folder->files()->count();
                        $unclaimedFiles = $folder->files()->where('status', 'unclaimed')->count();
                        $inProgressFiles = $folder->files()->where('status', 'in_progress')->count();
                        $completedFiles = $folder->files()->where('status', 'completed')->count();
                    @endphp
                    
                    <div class="row mb-4">
                        <div class="col-md-3 text-center">
                            <div class="border rounded p-3">
                                <h3 class="mb-0">{{ $totalFiles }}</h3>
                                <p class="mb-0">Total Files</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="border rounded p-3">
                                <h3 class="mb-0 text-secondary">{{ $unclaimedFiles }}</h3>
                                <p class="mb-0">Unclaimed</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="border rounded p-3">
                                <h3 class="mb-0 text-warning">{{ $inProgressFiles }}</h3>
                                <p class="mb-0">In Progress</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="border rounded p-3">
                                <h3 class="mb-0 text-success">{{ $completedFiles }}</h3>
                                <p class="mb-0">Completed</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="progress mb-3" style="height: 25px;">
                        <div class="progress-bar bg-secondary" role="progressbar" 
                             style="width: {{ $totalFiles > 0 ? ($unclaimedFiles / $totalFiles) * 100 : 0 }}%" 
                             aria-valuenow="{{ $totalFiles > 0 ? ($unclaimedFiles / $totalFiles) * 100 : 0 }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            {{ $totalFiles > 0 ? round(($unclaimedFiles / $totalFiles) * 100) : 0 }}% Unclaimed
                        </div>
                        <div class="progress-bar bg-warning" role="progressbar" 
                             style="width: {{ $totalFiles > 0 ? ($inProgressFiles / $totalFiles) * 100 : 0 }}%" 
                             aria-valuenow="{{ $totalFiles > 0 ? ($inProgressFiles / $totalFiles) * 100 : 0 }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            {{ $totalFiles > 0 ? round(($inProgressFiles / $totalFiles) * 100) : 0 }}% In Progress
                        </div>
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: {{ $totalFiles > 0 ? ($completedFiles / $totalFiles) * 100 : 0 }}%" 
                             aria-valuenow="{{ $totalFiles > 0 ? ($completedFiles / $totalFiles) * 100 : 0 }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            {{ $totalFiles > 0 ? round(($completedFiles / $totalFiles) * 100) : 0 }}% Completed
                        </div>
                    </div>
                    
                    @if(!auth()->user()->isAdmin() && $unclaimedFiles > 0)
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#claimFilesModal">
                            <i class="fas fa-hand-paper me-1"></i> Claim Files from This Folder
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Files -->
    <div class="card">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Files in this Folder</h5>
            <div>
                <div class="btn-group" role="group">
                    <a href="{{ route('files.index', ['folder_id' => $folder->id, 'status' => 'unclaimed']) }}" class="btn btn-outline-light {{ request('status') == 'unclaimed' ? 'active' : '' }}">
                        Unclaimed <span class="badge bg-secondary">{{ $unclaimedFiles }}</span>
                    </a>
                    <a href="{{ route('files.index', ['folder_id' => $folder->id, 'status' => 'in_progress']) }}" class="btn btn-outline-light {{ request('status') == 'in_progress' ? 'active' : '' }}">
                        In Progress <span class="badge bg-warning text-dark">{{ $inProgressFiles }}</span>
                    </a>
                    <a href="{{ route('files.index', ['folder_id' => $folder->id, 'status' => 'completed']) }}" class="btn btn-outline-light {{ request('status') == 'completed' ? 'active' : '' }}">
                        Completed <span class="badge bg-success">{{ $completedFiles }}</span>
                    </a>
                    <a href="{{ route('files.index', ['folder_id' => $folder->id]) }}" class="btn btn-outline-light {{ !request('status') ? 'active' : '' }}">
                        All <span class="badge bg-light text-dark">{{ $totalFiles }}</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($files->count() > 0)
                <div class="row">
                    @foreach($files as $file)
                        <div class="col-md-3 col-lg-2 mb-4">
                            <div class="card file-card h-100 {{ $file->status === 'completed' ? 'border-success' : ($file->status === 'in_progress' ? 'border-warning' : '') }}">
                                <div class="position-relative">
                                    <img src="{{ asset('storage/' . $file->path) }}" class="card-img-top" alt="{{ $file->original_name }}" style="height: 120px; object-fit: cover;">
                                    <div class="position-absolute top-0 end-0 p-2">
                                        <span class="badge bg-{{ $file->status === 'completed' ? 'success' : ($file->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($file->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <h6 class="card-title text-truncate" title="{{ $file->original_name }}">
                                        {{ $file->original_name }}
                                    </h6>
                                    @if($file->claimed_by)
                                        <p class="card-text small mb-0">
                                            <i class="fas fa-user me-1 text-muted"></i> 
                                            {{ $file->claimedByUser->name }}
                                        </p>
                                    @endif
                                </div>
                                <div class="card-footer bg-white p-2">
                                    <a href="{{ route('files.show', $file) }}" class="btn btn-sm btn-outline-primary w-100">
                                        <i class="fas fa-eye me-1"></i> View
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $files->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-image fa-3x mb-3 text-muted"></i>
                    <h5>No files found in this folder</h5>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Claim Files Modal -->
@if(!auth()->user()->isAdmin() && $unclaimedFiles > 0)
<div class="modal fade" id="claimFilesModal" tabindex="-1" aria-labelledby="claimFilesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="claimFilesModalLabel">Claim Files from {{ $folder->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('files.claim-batch') }}" method="POST">
                @csrf
                <input type="hidden" name="order_id" value="{{ $folder->order_id }}">
                <input type="hidden" name="folder_id" value="{{ $folder->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="fileCount" class="form-label">Number of Files to Claim (1-{{ min(20, $unclaimedFiles) }})</label>
                        <input type="number" class="form-control" id="fileCount" name="count" min="1" max="{{ min(20, $unclaimedFiles) }}" value="{{ min(10, $unclaimedFiles) }}" required>
                        <small class="form-text text-muted">There are {{ $unclaimedFiles }} unclaimed files available in this folder.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Claim Files</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection