@extends('layouts.auth.welcome')

@section('title', 'Visual Dashboard')

@section('content')
<div class="d-flex align-items-center justify-content-center authentication">
    <div class="col-xl-9 col-md-6 col-11">
        <div class="row authentication-cover-main mx-0 border rounded bg-white">
            <div class="col-xxl-6 col-xl-5 col-lg-12 d-xl-block d-none px-0">
                <div class="authentication-cover overflow-hidden">
                    <div class="authentication-cover-logo">
                        <a href="{{ url('/') }}">
                            <img src="{{ asset('images/logos/desktop-dark.png') }}" alt="" class="authentication-brand desktop-dark">
                        </a>
                    </div>
                    <div class="aunthentication-cover-content d-flex align-items-center justify-content-center">
                        <div class="row justify-content-center align-items-center">
                            <div class="col-xl-10">
                                <div class="rounded bg-white-transparent authentication-sub-content">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <img src="{{ asset('images/banners/welcome-01.png') }}" alt="img">
                                    </div>
                                    <h2 class="fs-5 mt-3 text-fixed-white fw-semibold text-left">“What new technology does is create new opportunities to do a job that customers want done.”</h2>
                                    <h2 class="fs-4 mt-3 text-fixed-white fw-semibold d-flex justify-content-end">- Tim O'Reilly</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-6 col-xl-7">
                    <div class="text-center mt-3 d-flex gap-5 justify-content-end">
                        <a href="{{ route('login') }}" class="btn btn-success btn-sm">
                            <i class="ri-login-box-line me-1"></i>Login
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">
                            <i class="ri-user-add-line me-1"></i>Register
                        </a>
                    </div>
                <div class="row justify-content-center align-items-center h-100">
                    <!-- image carosel -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
