@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Admin Dashboard</h1>
    
    <!-- Stats Overview -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Orders</h5>
                    <h2 class="mb-0">{{ $pendingOrders + $inProgressOrders + $completedOrders }}</h2>
                    <div class="d-flex justify-content-between mt-2">
                        <div>
                            <span class="badge bg-light text-dark">{{ $pendingOrders }} Pending</span>
                        </div>
                        <div>
                            <span class="badge bg-warning text-dark">{{ $inProgressOrders }} In Progress</span>
                        </div>
                        <div>
                            <span class="badge bg-success">{{ $completedOrders }} Completed</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Files</h5>
                    <h2 class="mb-0">{{ $totalFiles }}</h2>
                    <div class="progress mt-3 bg-light">
                        <div class="progress-bar bg-danger" role="progressbar" 
                             style="width: {{ $totalFiles > 0 ? ($unclaimedFiles / $totalFiles) * 100 : 0 }}%" 
                             aria-valuenow="{{ $totalFiles > 0 ? ($unclaimedFiles / $totalFiles) * 100 : 0 }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            {{ $unclaimedFiles }}
                        </div>
                        <div class="progress-bar bg-warning" role="progressbar" 
                             style="width: {{ $totalFiles > 0 ? ($inProgressFiles / $totalFiles) * 100 : 0 }}%" 
                             aria-valuenow="{{ $totalFiles > 0 ? ($inProgressFiles / $totalFiles) * 100 : 0 }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            {{ $inProgressFiles }}
                        </div>
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: {{ $totalFiles > 0 ? ($completedFiles / $totalFiles) * 100 : 0 }}%" 
                             aria-valuenow="{{ $totalFiles > 0 ? ($completedFiles / $totalFiles) * 100 : 0 }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            {{ $completedFiles }}
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <small><span class="badge bg-danger">{{ $unclaimedFiles }} Unclaimed</span></small>
                        <small><span class="badge bg-warning text-dark">{{ $inProgressFiles }} In Progress</span></small>
                        <small><span class="badge bg-success">{{ $completedFiles }} Completed</span></small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Employees</h5>
                    <h2 class="mb-0">{{ $employeeStats->count() }}</h2>
                    <p class="mt-2">
                        <a href="{{ route('users.index') }}" class="text-white">
                            <i class="fas fa-arrow-right me-1"></i> Manage Employees
                        </a>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Completion Rate</h5>
                    <h2 class="mb-0">{{ $totalFiles > 0 ? round(($completedFiles / $totalFiles) * 100) : 0 }}%</h2>
                    <div class="progress mt-3 bg-light">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: {{ $totalFiles > 0 ? ($completedFiles / $totalFiles) * 100 : 0 }}%" 
                             aria-valuenow="{{ $totalFiles > 0 ? ($completedFiles / $totalFiles) * 100 : 0 }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Orders and Employee Performance -->
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Orders</h5>
                    <a href="{{ route('orders.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Create Order
                    </a>
                </div>
                <div class="card-body">
                    @if($recentOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order Name</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                    <tr>
                                        <td>{{ $order->name }}</td>
                                        <td>
                                            @if($order->status == 'pending')
                                                <span class="badge bg-secondary">Pending</span>
                                            @elseif($order->status == 'in_progress')
                                                <span class="badge bg-warning">In Progress</span>
                                            @elseif($order->status == 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="progress">
                                                @php
                                                    $totalFiles = $order->files()->count();
                                                    $completedFiles = $order->files()->where('status', 'completed')->count();
                                                    $progress = $totalFiles > 0 ? ($completedFiles / $totalFiles) * 100 : 0;
                                                @endphp
                                                <div class="progress-bar bg-success" role="progressbar" 
                                                     style="width: {{ $progress }}%" 
                                                     aria-valuenow="{{ $progress }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                    {{ round($progress) }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($order->status == 'completed')
                                                <a href="{{ route('orders.download', $order) }}" class="btn btn-sm btn-success" title="Download">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end mt-3">
                            <a href="{{ route('orders.index') }}" class="btn btn-primary">View All Orders</a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted">No orders found.</p>
                            <a href="{{ route('orders.create') }}" class="btn btn-primary mt-2">
                                <i class="fas fa-plus me-1"></i> Create Your First Order
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Employee Performance</h5>
                    <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Add Employee
                    </a>
                </div>
                <div class="card-body">
                    @if($employeeStats->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Files Claimed</th>
                                        <th>Files Completed</th>
                                        <th>Completion Rate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employeeStats as $employee)
                                    <tr>
                                        <td>{{ $employee->name }}</td>
                                        <td>{{ $employee->claimed_files_count }}</td>
                                        <td>{{ $employee->completed_files_count }}</td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar bg-success" role="progressbar" 
                                                     style="width: {{ $employee->claimed_files_count > 0 ? ($employee->completed_files_count / $employee->claimed_files_count) * 100 : 0 }}%" 
                                                     aria-valuenow="{{ $employee->claimed_files_count > 0 ? ($employee->completed_files_count / $employee->claimed_files_count) * 100 : 0 }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                    {{ $employee->claimed_files_count > 0 ? round(($employee->completed_files_count / $employee->claimed_files_count) * 100) : 0 }}%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end mt-3">
                            <a href="{{ route('users.index') }}" class="btn btn-primary">Manage Employees</a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted">No employees found.</p>
                            <a href="{{ route('users.create') }}" class="btn btn-primary mt-2">
                                <i class="fas fa-plus me-1"></i> Add Your First Employee
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- System Activity Log -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Recent Activity</h5>
                </div>
                <div class="card-body">
                    @if(isset($recentActivities) && $recentActivities->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentActivities as $activity)
                                    <tr>
                                        <td>{{ $activity->created_at->diffForHumans() }}</td>
                                        <td>{{ $activity->user->name }}</td>
                                        <td>{{ $activity->action }}</td>
                                        <td>{{ $activity->details }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted">No recent activity found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection