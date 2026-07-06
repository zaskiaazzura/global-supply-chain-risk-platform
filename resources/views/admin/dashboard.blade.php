@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1><i class="fas fa-shield-alt text-primary"></i> Admin Dashboard</h1>
            <p class="text-muted">Kelola data sistem supply chain risk monitor</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <h2>{{ $totalUsers }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Countries</h5>
                    <h2>{{ $totalCountries }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Ports</h5>
                    <h2>{{ $totalPorts }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Risk Scores</h5>
                    <h2>{{ $totalRiskScores }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Data -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Recent Users</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @forelse($recentUsers as $user)
                            <li class="list-group-item">
                                {{ $user->name }} 
                                <span class="badge bg-secondary">{{ $user->role }}</span>
                                <small class="text-muted float-end">{{ $user->created_at->diffForHumans() }}</small>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">No users yet</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Recent Ports</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @forelse($recentPorts as $port)
                            <li class="list-group-item">
                                {{ $port->name }}
                                <span class="badge bg-info">{{ $port->country->name ?? 'N/A' }}</span>
                                <small class="text-muted float-end">{{ $port->created_at->diffForHumans() }}</small>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">No ports yet</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection