<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Order Management') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Additional Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
        }
        
        .main-content {
            min-height: calc(100vh - 56px);
            padding: 20px;
        }
        
        .file-card {
            transition: all 0.3s ease;
        }
        
        .file-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .user-info {
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 15px;
        }
        
        .sidebar-footer {
            margin-top: auto;
            padding: 15px;
        }
        
        .logout-btn-sidebar {
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ route('dashboard') }}">
                    <i class="fas fa-tasks me-2"></i> OrderCraft
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">
                                <i class="fas fa-chart-line me-1"></i> Dashboard
                            </a>
                        </li>
                        
                        @if(auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.index') }}">
                                <i class="fas fa-users me-1"></i> Users
                            </a>
                        </li>
                        @endif
                        
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('orders.index') }}">
                                <i class="fas fa-folder me-1"></i> Orders
                            </a>
                        </li>
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-1"></i> {{ Auth::user()->name }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-md-3 col-lg-2 sidebar py-3">
                    <!-- User Info Section -->
                    <div class="user-info">
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-shrink-0">
                                <i class="fas fa-user-circle fa-2x text-primary"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                                <small class="text-muted">
                                    @if(auth()->user()->isAdmin())
                                        Administrator
                                    @else
                                        Employee
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Main Menu -->
                    <div class="list-group mb-4">
                        <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="fas fa-chart-line me-2"></i> Dashboard
                        </a>
                        <a href="{{ route('orders.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                            <i class="fas fa-folder me-2"></i> Orders
                        </a>
                        <a href="{{ route('files.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('files.*') ? 'active' : '' }}">
                            <i class="fas fa-image me-2"></i> My Files
                        </a>
                        
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('users.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <i class="fas fa-users me-2"></i> Users
                        </a>
                        @endif
                    </div>
                    
                    <!-- Sidebar Footer with Logout -->
                    <div class="sidebar-footer mt-auto">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-danger logout-btn-sidebar">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Main Content -->
                <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    
                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    
                    @yield('content')
                </main>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Additional Scripts -->
    @yield('scripts')
</body>
</html>