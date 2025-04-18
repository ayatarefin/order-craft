@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ $file->original_name }}</h1>
        <div>
            @if($file->folder)
                <a href="{{ route('folders.show', $file->folder) }}" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i> Back to Folder
                </a>
            @else
                <a href="{{ route('orders.show', $file->order) }}" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i> Back to Order
                </a>
            @endif
            
            @if($file->claimed_by == auth()->id())
                <div class="btn-group" role="group">
                    <a href="{{ route('files.edit', $file) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                    <a href="{{ route('files.open-in-editor', $file) }}" class="btn btn-primary">
                        <i class="fas fa-external-link-alt me-1"></i> Open in Editor
                    </a>
                    @if($file->status !== 'completed')
                        <form action="{{ route('files.complete', $file) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check me-1"></i> Mark as Completed
                            </button>
                        </form>
                    @endif
                </div>
            @elseif($file->status === 'unclaimed')
                <form action="{{ route('files.claim-batch') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $file->order_id }}">
                    <input type="hidden" name="file_id" value="{{ $file->id }}">
                    <input type="hidden" name="count" value="1">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-hand-paper me-1"></i> Claim This File
                    </button>
                </form>
            @endif
        </div>
    </div>
    
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('orders.show', $file->order) }}">
                    <i class="fas fa-folder me-1"></i> {{ $file->order->name }}
                </a>
            </li>
            
            @if($file->folder)
                @php
                    $ancestors = [];
                    $parent = $file->folder;
                    
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
            @endif
            
            <li class="breadcrumb-item active" aria-current="page">
                {{ $file->original_name }}
            </li>
        </ol>
    </nav>
    
    <div class="row">
        <!-- File Preview and Details -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">File Preview</h5>
                </div>
                <div class="card-body text-center">
                    <img src="{{ asset('storage/' . $file->path) }}" class="img-fluid" alt="{{ $file->original_name }}" style="max-height: 500px;">
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">File Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row mb-0">
                                <dt class="col-sm-4">Original Name:</dt>
                                <dd class="col-sm-8">{{ $file->original_name }}</dd>
                                
                                <dt class="col-sm-4">File Type:</dt>
                                <dd class="col-sm-8">{{ $file->mime_type }}</dd>
                                
                                <dt class="col-sm-4">Size:</dt>
                                <dd class="col-sm-8">{{ round($file->size / 1024, 2) }} KB</dd>
                                
                                <dt class="col-sm-4">Order:</dt>
                                <dd class="col-sm-8">
                                    <a href="{{ route('orders.show', $file->order) }}">
                                        {{ $file->order->name }}
                                    </a>
                                </dd>
                                
                                <dt class="col-sm-4">Folder:</dt>
                                <dd class="col-sm-8">
                                    @if($file->folder)
                                        <a href="{{ route('folders.show', $file->folder) }}">
                                            {{ $file->folder->name }}
                                        </a>
                                    @else
                                        Root
                                    @endif
                                </dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row mb-0">
                                <dt class="col-sm-4">Status:</dt>
                                <dd class="col-sm-8">
                                    <span class="badge bg-{{ $file->status === 'completed' ? 'success' : ($file->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst(str_replace('_', ' ', $file->status)) }}
                                    </span>
                                </dd>
                                
                                <dt class="col-sm-4">Claimed By:</dt>
                                <dd class="col-sm-8">
                                    {{ $file->claimed_by ? $file->claimedByUser->name : 'Unclaimed' }}
                                </dd>
                                
                                @if($file->claimed_at)
                                <dt class="col-sm-4">Claimed At:</dt>
                                <dd class="col-sm-8">{{ $file->claimed_at->format('M d, Y H:i') }}</dd>
                                @endif
                                
                                @if($file->completed_at)
                                <dt class="col-sm-4">Completed At:</dt>
                                <dd class="col-sm-8">{{ $file->completed_at->format('M d, Y H:i') }}</dd>
                                @endif
                                
                                <dt class="col-sm-4">Created At:</dt>
                                <dd class="col-sm-8">{{ $file->created_at->format('M d, Y H:i') }}</dd>
                                
                                <dt class="col-sm-4">Updated At:</dt>
                                <dd class="col-sm-8">{{ $file->updated_at->format('M d, Y H:i') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Activity Logs -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Activity Log</h5>
                </div>
                <div class="card-body p-0">
                    @if($activityLogs->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($activityLogs as $log)
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">
                                            @if($log->action === 'claimed')
                                                <i class="fas fa-hand-paper text-primary me-1"></i>
                                            @elseif($log->action === 'edited')
                                                <i class="fas fa-edit text-warning me-1"></i>
                                            @elseif($log->action === 'completed')
                                                <i class="fas fa-check text-success me-1"></i>
                                            @elseif($log->action === 'opened')
                                                <i class="fas fa-external-link-alt text-info me-1"></i>
                                            @else
                                                <i class="fas fa-history text-secondary me-1"></i>
                                            @endif
                                            {{ ucfirst($log->action) }}
                                        </h6>
                                        <small>{{ $log->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ $log->description }}</p>
                                    <small>By {{ $log->user->name }}</small>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-history fa-2x mb-2 text-muted"></i>
                            <p class="mb-0">No activity logs found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection