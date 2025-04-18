@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            @if(request('folder_id'))
                {{ \App\Models\Folder::find(request('folder_id'))->name ?? 'Root' }} Files
            @else
                @if(auth()->user()->isAdmin())
                    All Files
                @else
                    My Files
                @endif
            @endif
        </h1>
        
        <div>
            @if(request('folder_id'))
                @php
                    $folder = \App\Models\Folder::find(request('folder_id'));
                    if ($folder) {
                        $orderId = $folder->order_id;
                        $backRoute = route('folders.show', $folder);
                    } else {
                        $orderId = request('order_id');
                        $backRoute = route('orders.show', $orderId);
                    }
                @endphp
                <a href="{{ $backRoute }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            @elseif(request('order_id'))
                <a href="{{ route('orders.show', request('order_id')) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Order
                </a>
            @endif
        </div>
    </div>
    
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Filter Files</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('files.index') }}" method="GET" class="row g-3">
                @if(request('folder_id'))
                    <input type="hidden" name="folder_id" value="{{ request('folder_id') }}">
                @endif
                
                @if(request('order_id'))
                    <input type="hidden" name="order_id" value="{{ request('order_id') }}">
                @endif
                
                <div class="col-md-4">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="unclaimed" {{ request('status') == 'unclaimed' ? 'selected' : '' }}>Unclaimed</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                
                @if(auth()->user()->isAdmin() && !request('folder_id') && !request('order_id'))
                <div class="col-md-4">
                    <label for="order" class="form-label">Order</label>
                    <select class="form-select" id="order" name="order_id">
                        <option value="">All Orders</option>
                        @foreach(\App\Models\Order::all() as $order)
                            <option value="{{ $order->id }}" {{ request('order_id') == $order->id ? 'selected' : '' }}>
                                {{ $order->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="File name...">
                </div>
                
                <div class="col-12 d-flex">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i> Apply Filters
                    </button>
                    <a href="{{ route('files.index', array_filter([
                        'folder_id' => request('folder_id'), 
                        'order_id' => request('order_id')
                    ])) }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Files Grid -->
    <div class="card">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                {{ $files->total() }} Files Found
            </h5>
            
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-light active" id="grid-view-btn">
                    <i class="fas fa-th"></i> Grid
                </button>
                <button type="button" class="btn btn-outline-light" id="list-view-btn">
                    <i class="fas fa-list"></i> List
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($files->count() > 0)
                <!-- Grid View -->
                <div class="row" id="grid-view">
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
                                    <div class="btn-group w-100">
                                        <a href="{{ route('files.show', $file) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($file->claimed_by == auth()->id())
                                            <a href="{{ route('files.edit', $file) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('files.open-in-editor', $file) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                            <form action="{{ route('files.complete', $file) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-outline-success" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @elseif($file->status === 'unclaimed')
                                            <form action="{{ route('files.claim-batch') }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="order_id" value="{{ $file->order_id }}">
                                                <input type="hidden" name="file_id" value="{{ $file->id }}">
                                                <input type="hidden" name="count" value="1">
                                                <button type="submit" class="btn btn-sm btn-outline-primary" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                                    <i class="fas fa-hand-paper"></i> Claim
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- List View (hidden by default) -->
                <div class="table-responsive" id="list-view" style="display: none;">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Preview</th>
                                <th>Name</th>
                                <th>Folder</th>
                                <th>Status</th>
                                <th>Owner</th>
                                <th>Last Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($files as $file)
                            <tr>
                                <td>
                                    <img src="{{ asset('storage/' . $file->path) }}" alt="{{ $file->original_name }}" class="img-thumbnail" style="max-width: 50px; max-height: 50px;">
                                </td>
                                <td>{{ $file->original_name }}</td>
                                <td>{{ $file->folder ? $file->folder->name : 'Root' }}</td>
                                <td>
                                    <span class="badge bg-{{ $file->status === 'completed' ? 'success' : ($file->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($file->status) }}
                                    </span>
                                </td>
                                <td>{{ $file->claimed_by ? $file->claimedByUser->name : 'Unclaimed' }}</td>
                                <td>{{ $file->updated_at->format('M d, Y H:i') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('files.show', $file) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($file->claimed_by == auth()->id())
                                            <a href="{{ route('files.edit', $file) }}" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('files.open-in-editor', $file) }}" class="btn btn-sm btn-primary" title="Open in Editor">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                            <form action="{{ route('files.complete', $file) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-success" title="Mark as Completed">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @elseif($file->status === 'unclaimed')
                                            <form action="{{ route('files.claim-batch') }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="order_id" value="{{ $file->order_id }}">
                                                <input type="hidden" name="file_id" value="{{ $file->id }}">
                                                <input type="hidden" name="count" value="1">
                                                <button type="submit" class="btn btn-sm btn-primary" title="Claim">
                                                    <i class="fas fa-hand-paper"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $files->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-image fa-3x mb-3 text-muted"></i>
                    <h5>No files found</h5>
                    @if(!request('folder_id') && !request('order_id') && !auth()->user()->isAdmin())
                        <p class="text-muted mt-3">You haven't claimed any files yet.</p>
                        <a href="{{ route('orders.index') }}" class="btn btn-primary mt-2">
                            <i class="fas fa-search me-1"></i> Browse Orders
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Grid view and List view toggle
    document.addEventListener('DOMContentLoaded', function() {
        const gridViewBtn = document.getElementById('grid-view-btn');
        const listViewBtn = document.getElementById('list-view-btn');
        const gridView = document.getElementById('grid-view');
        const listView = document.getElementById('list-view');
        
        if (gridViewBtn && listViewBtn) {
            gridViewBtn.addEventListener('click', function() {
                gridView.style.display = 'flex';
                listView.style.display = 'none';
                gridViewBtn.classList.add('active');
                listViewBtn.classList.remove('active');
            });
            
            listViewBtn.addEventListener('click', function() {
                gridView.style.display = 'none';
                listView.style.display = 'block';
                listViewBtn.classList.add('active');
                gridViewBtn.classList.remove('active');
            });
        }
    });
</script>
@endsection