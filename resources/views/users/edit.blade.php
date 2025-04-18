@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Edit User: {{ $user->name }}</h1>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Users
        </a>
    </div>
    
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">User Details</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                        <option value="">-- Select Role --</option>
                        <option value="admin" {{ (old('role', $user->role) === 'admin') ? 'selected' : '' }}>Admin</option>
                        <option value="employee_1" {{ (old('role', $user->role) === 'employee_1') ? 'selected' : '' }}>Employee 1</option>
                        <option value="employee_2" {{ (old('role', $user->role) === 'employee_2') ? 'selected' : '' }}>Employee 2</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        <strong>Admin:</strong> Can create orders, manage users, and oversee all operations.<br>
                        <strong>Employee 1:</strong> Can claim and process files.<br>
                        <strong>Employee 2:</strong> Same as Employee 1 but for a different team.
                    </small>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        Leave blank to keep the current password. If changing, password must be at least 8 characters long.
                    </small>
                </div>
                
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-secondary me-md-2">
                        <i class="fas fa-redo me-1"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- User Statistics -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">User Statistics</h5>
                </div>
                <div class="card-body">
                    @php
                        $claimedFiles = $user->claimedFiles()->count();
                        $completedFiles = $user->claimedFiles()->where('status', 'completed')->count();
                        $inProgressFiles = $user->claimedFiles()->where('status', 'in_progress')->count();
                    @endphp
                    
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <div class="border rounded p-3">
                                <h3 class="mb-0">{{ $claimedFiles }}</h3>
                                <p class="mb-0">Total Files</p>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="border rounded p-3">
                                <h3 class="mb-0 text-warning">{{ $inProgressFiles }}</h3>
                                <p class="mb-0">In Progress</p>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="border rounded p-3">
                                <h3 class="mb-0 text-success">{{ $completedFiles }}</h3>
                                <p class="mb-0">Completed</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h6>Completion Rate:</h6>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ $claimedFiles > 0 ? ($completedFiles / $claimedFiles) * 100 : 0 }}%" 
                                 aria-valuenow="{{ $claimedFiles > 0 ? ($completedFiles / $claimedFiles) * 100 : 0 }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                {{ $claimedFiles > 0 ? round(($completedFiles / $claimedFiles) * 100) : 0 }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Recent Activity</h5>
                </div>
                <div class="card-body p-0">
                    @php
                        $recentActivity = $user->activityLogs()->with('file')->latest()->limit(5)->get();
                    @endphp
                    
                    @if($recentActivity->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentActivity as $activity)
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">
                                            @if($activity->action === 'claimed')
                                                <i class="fas fa-hand-paper text-primary me-1"></i>
                                            @elseif($activity->action === 'edited')
                                                <i class="fas fa-edit text-warning me-1"></i>
                                            @elseif($activity->action === 'completed')
                                                <i class="fas fa-check text-success me-1"></i>
                                            @elseif($activity->action === 'opened')
                                                <i class="fas fa-external-link-alt text-info me-1"></i>
                                            @else
                                                <i class="fas fa-history text-secondary me-1"></i>
                                            @endif
                                            {{ ucfirst($activity->action) }} File
                                        </h6>
                                        <small>{{ $activity->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ $activity->file->original_name }}</p>
                                    <small>{{ $activity->description }}</small>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-history fa-2x mb-2 text-muted"></i>
                            <p class="mb-0">No recent activity</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection