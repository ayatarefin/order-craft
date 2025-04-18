@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ $order->name }}</h1>
        <div>
            <a href="{{ route('orders.index') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i> Back to Orders
            </a>
            @if(auth()->user()->isAdmin() && $order->status === 'completed')
            <a href="{{ route('orders.download', $order) }}" class="btn btn-success">
                <i class="fas fa-download me-1"></i> Download Completed Files
            </a>
            @endif
        </div>
    </div>
    
    <div class="row mb-4">
        <!-- Order Details -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Order Details</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Order ID:</dt>
                        <dd class="col-sm-8">#{{ $order->id }}</dd>
                        
                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </dd>
                        
                        <dt class="col-sm-4">Created By:</dt>
                        <dd class="col-sm-8">{{ $order->creator->name }}</dd>
                        
                        <dt class="col-sm-4">Created At:</dt>
                        <dd class="col-sm-8">{{ $order->created_at->format('M d, Y H:i') }}</dd>
                        
                        <dt class="col-sm-4">Description:</dt>
                        <dd class="col-sm-8">{{ $order->description ?? 'No description' }}</dd>
                    </dl>
                </div>
                @if(auth()->user()->isAdmin())
                <div class="card-footer bg-white border-top d-flex justify-content-end">
                    <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-warning me-2">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                    <form action="{{ route('orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this order?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="fas fa-trash me-1"></i> Delete
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Order Progress -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Order Progress</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3 text-center">
                            <div class="border rounded p-3 h-100 d-flex flex-column justify-content-center">
                                <h3 class="mb-0" id="total-count">{{ $totalFiles }}</h3>
                                <p class="mb-0">Total Files</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="border rounded p-3 h-100 d-flex flex-column justify-content-center">
                                <h3 class="mb-0 text-secondary" id="unclaimed-count">{{ $unclaimedFiles }}</h3>
                                <p class="mb-0">Unclaimed</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="border rounded p-3 h-100 d-flex flex-column justify-content-center">
                                <h3 class="mb-0 text-warning" id="in-progress-count">{{ $inProgressFiles }}</h3>
                                <p class="mb-0">In Progress</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="border rounded p-3 h-100 d-flex flex-column justify-content-center">
                                <h3 class="mb-0 text-success" id="completed-count">{{ $completedFiles }}</h3>
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
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#claimFilesModal" data-order-id="{{ $order->id }}">
                            <i class="fas fa-hand-paper me-1"></i> Claim Files to Work On
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    @if(auth()->user()->isAdmin() && $employeeProgress->count() > 0)
    <!-- Employee Progress -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Employee Progress</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Claimed Files</th>
                                    <th>Completed Files</th>
                                    <th>Completion Rate</th>
                                    <th>Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employeeProgress as $employee)
                                <tr>
                                    <td>{{ $employee->name }}</td>
                                    <td>{{ $employee->claimed_count }}</td>
                                    <td>{{ $employee->completed_count }}</td>
                                    <td>{{ $employee->claimed_count > 0 ? round(($employee->completed_count / $employee->claimed_count) * 100) : 0 }}%</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                 style="width: {{ $employee->claimed_count > 0 ? ($employee->completed_count / $employee->claimed_count) * 100 : 0 }}%" 
                                                 aria-valuenow="{{ $employee->claimed_count > 0 ? ($employee->completed_count / $employee->claimed_count) * 100 : 0 }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                {{ $employee->claimed_count > 0 ? round(($employee->completed_count / $employee->claimed_count) * 100) : 0 }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <!-- File Browser -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">File Browser</h5>
                </div>
                <div class="card-body">
                    @if($rootFolders->count() > 0 || $order->files()->whereNull('folder_id')->exists())
                        <div class="row">
                            <!-- Folder Navigation -->
                            <div class="col-md-4 mb-4">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Folders</h6>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="list-group list-group-flush">
                                            @foreach($rootFolders as $folder)
                                                <a href="{{ route('folders.show', $folder) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <i class="fas fa-folder me-2 text-warning"></i> {{ $folder->name }}
                                                    </div>
                                                    <span class="badge bg-primary rounded-pill">
                                                        {{ $folder->files->count() + $folder->children->sum(function($child) { return $child->files->count(); }) }}
                                                    </span>
                                                </a>
                                            @endforeach
                                            
                                            @if($order->files()->whereNull('folder_id')->exists())
                                                <a href="{{ route('files.index', ['order_id' => $order->id, 'folder_id' => 0]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <i class="fas fa-file-image me-2 text-info"></i> Root Files
                                                    </div>
                                                    <span class="badge bg-primary rounded-pill">
                                                        {{ $order->files()->whereNull('folder_id')->count() }}
                                                    </span>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Recent Files -->
                            <div class="col-md-8">
                                <h6 class="mb-3">Recent Files</h6>
                                <div class="row" id="files-container">
                                    @php $recentFiles = $order->files()->latest()->limit(8)->get(); @endphp
                                    @foreach($recentFiles as $file)
                                        <div class="col-md-3 mb-4">
                                            <div class="card file-card h-100 {{ $file->status === 'completed' ? 'border-success' : ($file->status === 'in_progress' ? 'border-warning' : '') }}" data-file-id="{{ $file->id }}">
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
                                                    <p class="card-text small mb-0">
                                                        <i class="fas fa-folder me-1 text-muted"></i> 
                                                        {{ $file->folder ? $file->folder->name : 'Root' }}
                                                    </p>
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
                                
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-2">
                                    <a href="{{ route('files.index', ['order_id' => $order->id]) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-search me-1"></i> Browse All Files
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-folder-open fa-3x mb-3 text-muted"></i>
                            <h5>No files found in this order</h5>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Claim Files Modal (for employee) -->
@if(!auth()->user()->isAdmin() && $unclaimedFiles > 0)
<div class="modal fade" id="claimFilesModal" tabindex="-1" aria-labelledby="claimFilesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="claimFilesModalLabel">Claim Files from {{ $order->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('files.claim-batch') }}" method="POST">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="fileCount" class="form-label">Number of Files to Claim (1-20)</label>
                        <input type="number" class="form-control" id="fileCount" name="count" min="1" max="20" value="10" required>
                        <small class="form-text text-muted">There are {{ $unclaimedFiles }} unclaimed files available in this order.</small>
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

@section('scripts')
<script>
    window.orderId = {{ $order->id }};
</script>
@endsection