@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Employee Dashboard</h1>
    
    <!-- Stats Overview -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">My Progress</h5>
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h2 class="mb-0">{{ $claimedFiles->total() + $completedFiles }}</h2>
                            <p class="mb-0">Total Files</p>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <h4>{{ $completedFiles }}</h4>
                                <p class="mb-0">Completed</p>
                            </div>
                        </div>
                    </div>
                    <div class="progress mt-3 bg-light">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: {{ ($claimedFiles->total() + $completedFiles) > 0 ? ($completedFiles / ($claimedFiles->total() + $completedFiles)) * 100 : 0 }}%" 
                             aria-valuenow="{{ ($claimedFiles->total() + $completedFiles) > 0 ? ($completedFiles / ($claimedFiles->total() + $completedFiles)) * 100 : 0 }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                             {{ ($claimedFiles->total() + $completedFiles) > 0 ? round(($completedFiles / ($claimedFiles->total() + $completedFiles)) * 100) : 0 }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Files In Progress</h5>
                    <h2 class="mb-0">{{ $claimedFiles->total() }}</h2>
                    <p class="mt-2">
                        <a href="{{ route('files.index') }}?status=in_progress" class="text-dark">
                            <i class="fas fa-arrow-right me-1"></i> View All In-Progress Files
                        </a>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Available Orders</h5>
                    <h2 class="mb-0">{{ $availableOrders->count() }}</h2>
                    <p class="mt-2">
                        <a href="{{ route('orders.index') }}" class="text-white">
                            <i class="fas fa-arrow-right me-1"></i> Browse Available Orders
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Current Work -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Current Work</h5>
                    @if($availableOrders->count() > 0)
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#claimFilesModal">
                        <i class="fas fa-plus me-1"></i> Claim New Files
                    </button>
                    @endif
                </div>
                <div class="card-body">
                    @if($claimedFiles->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Preview</th>
                                        <th>File Name</th>
                                        <th>Order</th>
                                        <th>Claimed At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($claimedFiles as $file)
                                    <tr>
                                        <td>
                                            <img src="{{ asset('storage/' . $file->path) }}" 
                                                 alt="{{ $file->original_name }}" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 50px; max-height: 50px;">
                                        </td>
                                        <td>{{ $file->original_name }}</td>
                                        <td>
                                            <a href="{{ route('orders.show', $file->order) }}">
                                                {{ $file->order->name }}
                                            </a>
                                        </td>
                                        <td>{{ $file->claimed_at->diffForHumans() }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('files.show', $file) }}" class="btn btn-sm btn-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('files.edit', $file) }}" class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('files.edit-online', $file) }}" class="btn btn-sm btn-primary" title="Open in Editor">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                                <form action="{{ route('files.complete', $file) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-success" title="Mark as Completed">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $claimedFiles->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-folder-open fa-3x mb-3 text-muted"></i>
                            <h5>You don't have any files in progress</h5>
                            @if($availableOrders->count() > 0)
                                <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#claimFilesModal">
                                    <i class="fas fa-plus me-1"></i> Claim Files to Work On
                                </button>
                            @else
                                <p class="text-muted mt-3">There are currently no orders with available files to work on.</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Available Orders -->
    @if($availableOrders->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Available Orders</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($availableOrders as $order)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">{{ $order->name }}</h5>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">{{ Str::limit($order->description, 100) }}</p>
                                    <p class="text-muted">
                                        <small>
                                            <i class="far fa-calendar-alt me-1"></i> 
                                            Created: {{ $order->created_at->format('M d, Y') }}
                                        </small>
                                    </p>
                                    <p>
                                        <span class="badge bg-success">
                                            {{ $order->files()->where('status', 'unclaimed')->count() }} files available
                                        </span>
                                    </p>
                                </div>
                                <div class="card-footer bg-white border-top-0">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">View Details</a>
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#claimFilesModal" data-order-id="{{ $order->id }}">
                                            Claim Files
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Claim Files Modal -->
<div class="modal fade" id="claimFilesModal" tabindex="-1" aria-labelledby="claimFilesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="claimFilesModalLabel">Claim Files to Work On</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('files.claim-batch') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="orderSelect" class="form-label">Select Order</label>
                        <select class="form-select" id="orderSelect" name="order_id" required>
                            <option value="">-- Select an Order --</option>
                            @foreach($availableOrders as $order)
                                <option value="{{ $order->id }}">
                                    {{ $order->name }} ({{ $order->files()->where('status', 'unclaimed')->count() }} files available)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="fileCount" class="form-label">Number of Files to Claim (1-20)</label>
                        <input type="number" class="form-control" id="fileCount" name="count" min="1" max="20" value="10" required>
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
@endsection

@section('scripts')
<script>
    // Script to auto-select order in claim files modal
    document.addEventListener('DOMContentLoaded', function() {
        const claimFilesModal = document.getElementById('claimFilesModal');
        if (claimFilesModal) {
            claimFilesModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const orderId = button.getAttribute('data-order-id');
                
                if (orderId) {
                    const orderSelect = document.getElementById('orderSelect');
                    orderSelect.value = orderId;
                }
            });
        }
    });
</script>
@endsection