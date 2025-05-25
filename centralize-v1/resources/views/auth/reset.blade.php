@extends('layouts.auth.auth')
@section('title', 'Reset Password')
@section('content')
<div class="container-lg">
    <div class="row justify-content-center authentication authentication-basic align-items-center h-100">
        <div class="col-xxl-4 col-xl-5 col-lg-5 col-md-6 col-sm-8 col-12">
            <div class="card custom-card my-4 border z-3 position-relative">
                <div class="card-body p-0">
                    <div class="p-5">
                        <div class="d-flex align-items-center justify-content-center mb-3">
                            <a href="{{ url('/') }}">
                                <span class="auth-icon">
                                    <!-- SVG content here -->
                                </span>
                            </a>
                        </div>
                        <p class="h4 fw-semibold mb-0 text-center text-warning">Reset Password</p>
                        <p class="mb-3 text-muted fw-bold text-center text-secondary">Use simple password! Easy to remember.. Hard to forget!</p>
                        
                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif
                        
                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif
                        
                        <form method="POST" action="{{ route('password.reset.offline') }}">
                            @csrf
                            <div class="row gy-3">
                                <div class="col-xl-12">
                                    <label for="user_id" class="form-label text-default">Employee ID</label>
                                    <div class="position-relative">
                                        <input type="text" 
                                               class="form-control form-control-lg @error('user_id') is-invalid @enderror" 
                                               id="user_id"
                                               name="user_id"
                                               value="{{ old('user_id') }}"
                                               required
                                               autocomplete="user_id"
                                               placeholder="Your employee ID">
                                        @error('user_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <label for="date_hired" class="form-label text-default">Hired date</label>
                                    <div class="position-relative">
                                        <input type="text" 
                                               class="form-control form-control-lg @error('date_hired') is-invalid @enderror" 
                                               id="date_hired"
                                               name="date_hired"
                                               value="{{ old('date_hired') }}"
                                               required
                                               placeholder="YYYY-MM-DD">
                                        @error('date_hired')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <label for="password" class="form-label text-default">New Password</label>
                                    <div class="position-relative">
                                        <input type="password" 
                                               class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                               id="password"
                                               name="password"
                                               required
                                               placeholder="New password">
                                        <a href="javascript:void(0);" 
                                           class="show-password-button text-muted" 
                                           onclick="createpassword('password',this)" 
                                           id="button-addon21">
                                            <i class="ri-eye-off-line align-middle"></i>
                                        </a>
                                        <div id="capslock-warning" class="text-danger mt-1" style="display: none;">
                                            <small><i class="ri-error-warning-line"></i> Caps Lock is ON</small>
                                        </div>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <label for="password_confirmation" class="form-label text-default">Confirm Password</label>
                                    <div class="position-relative">
                                        <input type="password" 
                                               class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror" 
                                               id="password_confirmation"
                                               name="password_confirmation"
                                               required
                                               placeholder="Confirm password">
                                        <a href="javascript:void(0);" 
                                           class="show-password-button text-muted"  
                                           onclick="createpassword('password_confirmation',this)" 
                                           id="button-addon22">
                                            <i class="ri-eye-off-line align-middle"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary">Reset Password</button>
                            </div>
                        </form>
                        <div class="text-center">
                            <p class="text-muted mt-3 mb-0">Remember your password? <a href="{{ route('login') }}" class="text-primary">Sign In</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection