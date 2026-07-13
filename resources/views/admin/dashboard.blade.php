@extends('admin.layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <h1 class="mb-4"><i class="fas fa-shield-alt text-primary"></i> Admin Dashboard</h1>
    </div>
</div>

<div class="row">
    <!-- Total Users -->
    <div class="col-md-3">
        <div class="stat-card blue">
            <i class="fas fa-users icon"></i>
            <h3>{{ $totalUsers ?? 0 }}</h3>
            <p>Total Users</p>
        </div>
    </div>
    <!-- Total Countries -->
    <div class="col-md-3">
        <div class="stat-card green">
            <i class="fas fa-flag icon"></i>
            <h3>{{ $totalCountries ?? 0 }}</h3>
            <p>Countries</p>
        </div>
    </div>
    <!-- Total Ports -->
    <div class="col-md-3">
        <div class="stat-card orange">
            <i class="fas fa-anchor icon"></i>
            <h3>{{ $totalPorts ?? 0 }}</h3>
            <p>Ports</p>
        </div>
    </div>
    <!-- Total Articles -->
    <div class="col-md-3">
        <div class="stat-card purple">
            <i class="fas fa-newspaper icon"></i>
            <h3>{{ $totalArticles ?? 0 }}</h3>
            <p>Articles</p>
        </div>
    </div>
</div>

<!-- Recent Users -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Recent Users</h5>
            </div>
            <div class="card-body">
                @if(isset($recentUsers) && $recentUsers->count())
                    <ul class="list-group">
                        @foreach($recentUsers as $user)
                            <li class="list-group-item">
                                {{ $user->name }} 
                                <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-secondary' }}">{{ $user->role }}</span>
                                <small class="text-muted float-end">{{ $user->created_at->diffForHumans() }}</small>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">No users yet</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Recent Ports</h5>
            </div>
            <div class="card-body">
                @if(isset($recentPorts) && $recentPorts->count())
                    <ul class="list-group">
                        @foreach($recentPorts as $port)
                            <li class="list-group-item">
                                {{ $port->name }}
                                <span class="badge bg-info">{{ $port->country->name ?? 'N/A' }}</span>
                                <small class="text-muted float-end">{{ $port->created_at->diffForHumans() }}</small>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">No ports yet</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection