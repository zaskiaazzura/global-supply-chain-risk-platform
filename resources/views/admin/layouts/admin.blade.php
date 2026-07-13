<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @stack('styles')
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background: #2c3e50;
            padding-top: 20px;
        }
        .sidebar .nav-link {
            color: #b0b8c4;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 4px 10px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            background: #34495e;
            color: white;
        }
        .sidebar .nav-link.active {
            background: #3498db;
            color: white;
        }
        .sidebar .nav-link i {
            width: 25px;
            margin-right: 10px;
        }
        .sidebar .nav-link.logout {
            color: #e74c3c;
        }
        .sidebar .nav-link.logout:hover {
            background: #e74c3c;
            color: white;
        }
        .sidebar .brand {
            color: white;
            font-size: 1.3rem;
            font-weight: bold;
            padding: 15px 20px;
            border-bottom: 1px solid #34495e;
            margin-bottom: 20px;
        }
        .sidebar .brand i {
            margin-right: 10px;
        }
        .main-content {
            padding: 20px;
        }
        .stat-card {
            border-radius: 12px;
            padding: 20px;
            color: white;
            margin-bottom: 20px;
        }
        .stat-card h3 {
            font-size: 2rem;
            font-weight: 700;
        }
        .stat-card .icon {
            font-size: 2.5rem;
            opacity: 0.5;
            float: right;
        }
        .stat-card.blue { background: linear-gradient(135deg, #3498db, #2980b9); }
        .stat-card.green { background: linear-gradient(135deg, #2ecc71, #27ae60); }
        .stat-card.orange { background: linear-gradient(135deg, #f39c12, #e67e22); }
        .stat-card.red { background: linear-gradient(135deg, #e74c3c, #c0392b); }
        .stat-card.purple { background: linear-gradient(135deg, #9b59b6, #8e44ad); }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <div class="brand">
                    <i class="fas fa-shield-alt"></i> Admin Panel
                </div>
                <!-- Admin Sidebar -->
                <nav class="nav flex-column">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                        <i class="fas fa-users"></i> Manage Users
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.ports.*') ? 'active' : '' }}" href="{{ route('admin.ports.index') }}">
                        <i class="fas fa-anchor"></i> Manage Ports
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.articles.*') ? 'active' : '' }}" href="{{ route('admin.articles.index') }}">
                        <i class="fas fa-newspaper"></i> Manage Articles
                    </a>
                    
                    <hr style="border-color: #34495e; margin: 15px 10px;">
                    
                    <!-- ✅ LINK KE PUBLIC DASHBOARD -->
                    <a class="nav-link" href="{{ route('dashboard') }}">
                        <i class="fas fa-globe"></i> Public Dashboard
                    </a>
                    
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="nav-link logout" style="background: none; border: none; width: 100%; text-align: left;">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @stack('scripts')
</body>
</html>