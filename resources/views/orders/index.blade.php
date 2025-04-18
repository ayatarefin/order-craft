@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Orders</h1>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('orders.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Create New Order
        </a>
        @endif
    </div>
    
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Filter Orders</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('orders.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Order name or ID...">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i> Apply Filters
                    </button>
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Orders List -->
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">All Orders</h5>
        </div>
        <div class="card-body">
            @if($orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Created By</th>
                                <th>Files</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->name }}</td>
                                <td>
                                    <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                    </span>
                                </td>
                                <td>{{ $order->creator->name }}</td>
                                <td>
                                    @php
                                        $totalFiles = $order->files()->count();
                                        $completedFiles = $order->files()->where('status', 'completed')->count();
                                        $completionPercentage = $totalFiles > 0 ? round(($completedFiles / $totalFiles) * 100) : 0;
                                    @endphp
                                    
                                    <div class="progress" style="height: 20px;" title="{{ $completedFiles }}/{{ $totalFiles }} files completed">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: {{ $completionPercentage }}%;" 
                                             aria-valuenow="{{ $completionPercentage }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            {{ $completionPercentage }}%
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if(auth()->user()->isAdmin())
                                            @if($order->status === 'completed')
                                            <a href="{{ route('orders.download', $order) }}" class="btn btn-sm btn-success" title="Download Completed Files">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            @endif
                                            
                                            <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <form action="{{ route('orders.destroy', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this order?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#claimFilesModal" data-order-id="{{ $order->id }}" title="Claim Files">
                                                <i class="fas fa-hand-paper"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-3x mb-3 text-muted"></i>
                    <h5>No orders found</h5>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('orders.create') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-plus me-1"></i> Create New Order
                        </a>
                    @else
                        <p class="text-muted mt-3">There are currently no orders available.</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Claim Files Modal (for employee) -->
@if(!auth()->user()->isAdmin())
<div class="modal fade" id="claimFilesModal" tabindex="-1" aria-labelledby="claimFilesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="claimFilesModalLabel">Claim Files to Work On</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('files.claim-batch') }}" method="POST">
                @csrf
                <input type="hidden" id="modalOrderId" name="order_id" value="">
                <div class="modal-body">
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
@endif
@endsection

@section('scripts')
<script>
    // Script to set order ID in claim files modal
    document.addEventListener('DOMContentLoaded', function() {
        const claimFilesModal = document.getElementById('claimFilesModal');
        if (claimFilesModal) {
            claimFilesModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const orderId = button.getAttribute('data-order-id');
                
                if (orderId) {
                    document.getElementById('modalOrderId').value = orderId;
                }
            });
        }
    });
</script>
@endsection