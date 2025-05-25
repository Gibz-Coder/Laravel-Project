<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Welcome Page">

    <!-- Title -->
    <title>@yield('title', config('app.name', 'Laravel'))</title>

        <!-- FAVICON -->
        <link rel="icon" href="{{ asset('images/icons/favicon.ico') }}" type="image/x-icon">

        <!-- BOOTSTRAP CSS -->
	    <link  id="style" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

        <!-- STYLES CSS -->
        <link href="{{ asset('css/styles.css') }}" rel="stylesheet">

        <!-- ICONS CSS -->
        <link href="{{ asset('icon-fonts/icons.css') }}" rel="stylesheet">

        <!-- MAIN JS -->
        <script src="{{ asset('js/authentication-main.js') }}"></script>


        <!-- Script to prevent back button after login -->
        <script type="text/javascript">
            window.history.forward();
            function noBack() {
                window.history.forward();
            }
        </script>

	</head>

    <body class="authentication-background authenticationcover-background position-relative" id="particles-js">


        @yield('content')

        <!-- FOOTER -->
        @section('footer')
        <footer class="footer mt-auto py-3 bg-white text-center">
            <div class="container">
                <span class="text-muted"> Copyright © <span id="year"></span> <a
                        href="javascript:void(0);" class="text-dark fw-medium">Visual Centralize Dashboard</a>.
                    Designed with <span class="bi bi-heart-fill text-danger"></span> by <a href="javascript:void(0);">
                        <span class="fw-medium text-primary">Visual Dev Team</span>
                    </a> All
                    rights
                    reserved
                </span>
            </div>
        </footer>
        @show
        <!-- END FOOTER -->


        <!-- SCRIPTS -->

        <!-- BOOTSTRAP JS -->
        <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

        <!-- Particles JS -->
        <script src="{{ asset('vendor/particles.js/particles.js') }}"></script>

        <!-- Basic Password JS -->
        <script src="{{ asset('js/basic-password.js') }}"></script>

        <!-- Show Password JS -->
        <script src="{{ asset('js/show-password.js') }}"></script>

        <!-- Capslock Detection JS -->
        <script src="{{ asset('js/capslock-detection.js') }}"></script>


        <!-- END SCRIPTS -->
        @yield('scripts')

	</body>
</html>
