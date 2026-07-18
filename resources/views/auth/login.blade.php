@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-75">
        <div class="col-md-5">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-primary text-white text-center py-4 border-0">
                    <h4 class="mb-0">
                        <i class="fas fa-globe me-2"></i> Supply Chain Risk Monitor
                    </h4>
                    <p class="mb-0 small opacity-75">Login ke akun Anda</p>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> {{ $errors->first() }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.post') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-envelope text-primary me-1"></i> Email
                            </label>
                            <input type="email" name="email" class="form-control form-control-lg rounded-3 @error('email') is-invalid @enderror" placeholder="admin@example.com" required>
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-lock text-primary me-1"></i> Password
                            </label>
                            <input type="password" name="password" class="form-control form-control-lg rounded-3 @error('password') is-invalid @enderror" placeholder="••••••••" required>
                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3 mt-2">
                            <i class="fas fa-sign-in-alt me-2"></i> Login
                        </button>
                    </form>
                    <div class="text-center mt-3">
                        <p class="mb-0">
                            Belum punya akun? 
                            <a href="{{ route('register') }}" class="text-primary fw-semibold text-decoration-none">
                                <i class="fas fa-user-plus"></i> Register
                            </a>
                        </p>
                    </div>
                </div>
                <div class="card-footer bg-light text-center py-3 border-0">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt me-1"></i> Global Supply Chain Risk Monitoring
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .min-vh-75 {
        min-height: 75vh;
    }
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.15) !important;
    }
    .form-control {
        border: 2px solid #e9ecef;
        transition: border-color 0.3s ease;
    }
    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }
    .btn-primary {
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
        border: none;
        transition: transform 0.2s ease;
    }
    .btn-primary:hover {
        transform: scale(1.02);
        background: linear-gradient(135deg, #0b5ed7, #084298);
    }
</style>
@endsection