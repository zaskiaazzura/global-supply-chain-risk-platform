<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">
    <title>@yield('title', 'Supply Chain Risk Monitor')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Leaflet.js CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    
    @stack('styles')
    
    <style>
        body { background-color: #f0f2f5; }
        .sidebar {
            min-height: 100vh;
            background: #1a1a2e;
            padding-top: 20px;
        }
        .sidebar .brand {
            color: white;
            font-size: 1.2rem;
            font-weight: bold;
            padding: 15px 20px;
            border-bottom: 1px solid #16213e;
            margin-bottom: 20px;
        }
        .sidebar .brand i { margin-right: 10px; }
        .sidebar .nav-link {
            color: #a8a8b3;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 4px 10px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            background: #16213e;
            color: white;
        }
        .sidebar .nav-link.active {
            background: #0f3460;
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
        .main-content { padding: 20px; }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <div class="brand">
                    <i class="fas fa-globe"></i> Supply Chain Risk
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link {{ request()->routeIs('ports') ? 'active' : '' }}" href="{{ route('ports') }}">
                        <i class="fas fa-anchor"></i> Ports
                    </a>
                    <a class="nav-link {{ request()->routeIs('currency') ? 'active' : '' }}" href="{{ route('currency') }}">
                        <i class="fas fa-dollar-sign"></i> Currency
                    </a>
                    <a class="nav-link {{ request()->routeIs('news.*') ? 'active' : '' }}" href="{{ route('news.index') }}">
                        <i class="fas fa-newspaper"></i> Berita
                    </a>
                    <a class="nav-link {{ request()->routeIs('comparison') ? 'active' : '' }}" href="{{ route('comparison') }}">
                        <i class="fas fa-arrows-left-right"></i> Comparison
                    </a>
                    <a class="nav-link {{ request()->routeIs('watchlist') ? 'active' : '' }}" href="{{ route('watchlist') }}">
                        <i class="fas fa-star text-warning"></i> Favorit
                    </a>                
                        
                    <!-- Admin Link (hanya untuk admin) -->
                    @if(auth()->user()->role === 'admin')
                        <hr style="border-color: #16213e; margin: 15px 10px;">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}" style="color: #f39c12;">
                            <i class="fas fa-shield-alt"></i> Admin Panel
                        </a>
                    @endif
                        
                    <hr style="border-color: #16213e; margin: 15px 10px;">
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
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        window.baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content');
        console.log('Base URL:', window.baseUrl);
    </script>
    @stack('scripts')
</body>
</html>