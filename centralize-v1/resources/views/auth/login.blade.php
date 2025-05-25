@extends('layouts.auth.auth')
@section('title', 'Login')
@section('content')
<div class="container">
    <div class="row justify-content-center authentication authentication-basic align-items-center h-100">
        <div class="col-xxl-4 col-xl-5 col-lg-5 col-md-6 col-sm-8 col-12">
            <div class="card custom-card my-4 border z-3 position-relative">
                <div class="card-body p-0">
                    <div class="p-5">
                        <div class="d-flex align-items-center justify-content-center mb-3">
                            <a href="{{ url('/') }}">
                            <span class="auth-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" id="password"><path fill="#6446fe" d="M59,8H5A1,1,0,0,0,4,9V55a1,1,0,0,0,1,1H59a1,1,0,0,0,1-1V9A1,1,0,0,0,59,8ZM58,54H6V10H58Z" class="color1d1f47 svgShape"></path><path fill="#6446fe" d="M36,35H28a3,3,0,0,1-3-3V27a3,3,0,0,1,3-3h8a3,3,0,0,1,3,3v5A3,3,0,0,1,36,35Zm-8-9a1,1,0,0,0-1,1v5a1,1,0,0,0,1,1h8a1,1,0,0,0,1-1V27a1,1,0,0,0-1-1Z" class="color0055ff svgShape"></path><path fill="#6446fe" d="M36 26H28a1 1 0 0 1-1-1V24a5 5 0 0 1 10 0v1A1 1 0 0 1 36 26zm-7-2h6a3 3 0 0 0-6 0zM32 31a1 1 0 0 1-1-1V29a1 1 0 0 1 2 0v1A1 1 0 0 1 32 31z" class="color0055ff svgShape"></path><path fill="#6446fe" d="M59 8H5A1 1 0 0 0 4 9v8a1 1 0 0 0 1 1H20.08a1 1 0 0 0 .63-.22L25.36 14H59a1 1 0 0 0 1-1V9A1 1 0 0 0 59 8zm-1 4H25l-.21 0a1.09 1.09 0 0 0-.42.2L19.73 16H6V10H58zM50 49H14a1 1 0 0 1-1-1V39a1 1 0 0 1 1-1H50a1 1 0 0 1 1 1v9A1 1 0 0 1 50 49zM15 47H49V40H15z" class="color1d1f47 svgShape"></path><circle cx="19.5" cy="43.5" r="1.5" fill="#6446fe" class="color0055ff svgShape"></circle><circle cx="24.5" cy="43.5" r="1.5" fill="#6446fe" class="color0055ff svgShape"></circle><circle cx="29.5" cy="43.5" r="1.5" fill="#6446fe" class="color0055ff svgShape"></circle><circle cx="34.5" cy="43.5" r="1.5" fill="#6446fe" class="color0055ff svgShape"></circle><circle cx="39.5" cy="43.5" r="1.5" fill="#6446fe" class="color0055ff svgShape"></circle><circle cx="44.5" cy="43.5" r="1.5" fill="#6446fe" class="color0055ff svgShape"></circle><path fill="#6446fe" d="M60 9a1 1 0 0 0-1-1H28.81l2.37-2.37A19.22 19.22 0 0 1 60 31zM35.19 56l-2.37 2.37A19.22 19.22 0 0 1 4 33V55a1 1 0 0 0 1 1z" opacity=".3" class="color0055ff svgShape"></path></svg>
                                </span>
                            </a>
                            
                            
                        </div>
                        <p class="h3 fw-bold mb-0 text-center text-success">Login</p>
                        <p class="mb-3 text-muted fw-normal text-center">Welcome to Visual Central Dashboard</p>
                        
                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="row gy-3">
                                <div class="col-xl-12">
                                    <label for="login" class="form-label text-default">Employee ID or Knox ID</label>
                                    <div class="position-relative">
                                        <input type="text" 
                                               class="form-control form-control-lg @error('login') is-invalid @enderror" 
                                               id="login" 
                                               name="login" 
                                               value="{{ old('login') }}" 
                                               required 
                                               autocomplete="login" 
                                               autofocus>
                                        @error('login')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xl-12 mb-2">
                                    <label for="password" class="form-label text-default d-block">
                                        Password
                                    </label>
                                    <div class="position-relative">
                                        <input type="password" 
                                               class="form-control form-control-lg @error('password') is-invalid @enderror @if($errors->has('email')) is-invalid @endif" 
                                               id="signin-password" 
                                               name="password" 
                                               required 
                                               autocomplete="current-password">
                                        <a href="javascript:void(0);" 
                                           class="show-password-button text-muted" 
                                           onclick="createpassword('signin-password', this)" 
                                           id="button-addon2">
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
                                        @if($errors->has('email'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>These credentials do not match our records.</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="remember" 
                                                   id="remember" 
                                                   {{ old('remember') ? 'checked' : '' }}>
                                            <label class="form-check-label text-muted fw-normal fs-12" for="remember">
                                                Remember me
                                            </label>
                                            @if (Route::has('password.request'))
                                                <a href="{{ route('password.request') }}" class="float-end link-danger op-5 fw-medium fs-12">
                                                    Forgot password?
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    Login
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center mb-0">
                            <p class="text-muted mt-3 mb-0">
                                Don't have an account? 
                                <a href="{{ route('register') }}" class="text-primary">Register here!</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

