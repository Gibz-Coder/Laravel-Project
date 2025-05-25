<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr"><!-- Direction always set to LTR -->
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Title -->
    <title>@yield('title', config('app.name', 'Centralized Dashboard'))</title>

        <!-- FAVICON -->
        <link rel="icon" href="{{ asset('images/icons/favicon.ico') }}" type="image/x-icon">

        <!-- BOOTSTRAP CSS -->
	    <link  id="style" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

        <!-- STYLES CSS -->
        <link href="{{ asset('css/styles.css') }}" rel="stylesheet">

        <!-- ICONS CSS -->
        <link href="{{ asset('icon-fonts/icons.css') }}" rel="stylesheet">


        <!-- NODE WAVES CSS -->
        <link href="{{ asset('vendor/node-waves/waves.min.css') }}" rel="stylesheet">

        <!-- SIMPLEBAR CSS -->
        <link rel="stylesheet" href="{{ asset('vendor/simplebar/simplebar.min.css') }}">

        <!-- PICKER CSS -->
        <link rel="stylesheet" href="{{ asset('vendor/flatpickr/flatpickr.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/@simonwep/pickr/themes/nano.min.css') }}">

        <!-- AUTO COMPLETE CSS -->
        <link rel="stylesheet" href="{{ asset('vendor/@tarekraafat/autocomplete.js/css/autoComplete.css') }}">

        <!-- CHOICES CSS -->
        <link rel="stylesheet" href="{{ asset('vendor/choices.js/public/assets/styles/choices.min.css') }}">

        <!-- Script to prevent back button after login -->
        <script type="text/javascript">
            window.history.forward();
            function noBack() {
                window.history.forward();
            }
        </script>

        <!-- CHOICES JS -->
        <script src="{{ asset('vendor/choices.js/public/assets/scripts/choices.min.js') }}"></script>

        <!-- MAIN JS -->
        <script src="{{ asset('js/main.js') }}"></script>


        <!-- Shepherd Css -->
        <link rel="stylesheet" href="{{ asset('vendor/shepherd.js/css/shepherd.css') }}">

        <!-- SweetAlert2 CSS -->
        <link rel="stylesheet" href="{{ asset('vendor/sweetalert2/sweetalert2.min.css') }}">

        <!-- SweetAlert2 Dark Mode CSS -->
        <link rel="stylesheet" href="{{ asset('css/sweetalert-dark.css') }}">

	</head>

    <body>

        <!-- SWITCHER -->

            <div class="offcanvas offcanvas-end" tabindex="-1" id="switcher-canvas" aria-labelledby="offcanvasRightLabel">
                <div class="offcanvas-header border-bottom d-block p-0">
                    <div class="d-flex align-items-center justify-content-between p-3">
                        <h5 class="offcanvas-title text-default" id="offcanvasRightLabel">Switcher</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <nav class="border-top border-block-start-dashed">
                        <div class="nav nav-tabs nav-justified" id="switcher-main-tab" role="tablist">
                            <button class="nav-link active" id="switcher-home-tab" data-bs-toggle="tab"
                                data-bs-target="#switcher-home" type="button" role="tab" aria-controls="switcher-home"
                                aria-selected="true">Theme Styles</button>
                            <button class="nav-link" id="switcher-profile-tab" data-bs-toggle="tab"
                                data-bs-target="#switcher-profile" type="button" role="tab" aria-controls="switcher-profile"
                                aria-selected="false">Theme Colors</button>
                        </div>
                    </nav>
                </div>
                <div class="offcanvas-body">
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active border-0" id="switcher-home" role="tabpanel"
                            aria-labelledby="switcher-home-tab" tabindex="0">
                            <div class="">
                                <p class="switcher-style-head">Theme Color Mode:</p>
                                <div class="row switcher-style gx-0">
                                    <div class="col-4">
                                        <div class="form-check switch-select">
                                            <label class="form-check-label" for="switcher-light-theme">
                                                Light
                                            </label>
                                            <input class="form-check-input" type="radio" name="theme-style"
                                                id="switcher-light-theme" checked>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-check switch-select">
                                            <label class="form-check-label" for="switcher-dark-theme">
                                                Dark
                                            </label>
                                            <input class="form-check-input" type="radio" name="theme-style"
                                                id="switcher-dark-theme">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="">
                                <p class="switcher-style-head">Navigation Styles:</p>
                                <div class="row switcher-style gx-0">
                                    <div class="col-4">
                                        <div class="form-check switch-select">
                                            <label class="form-check-label" for="switcher-vertical">
                                                Vertical
                                            </label>
                                            <input class="form-check-input" type="radio" name="navigation-style"
                                                id="switcher-vertical" checked>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-check switch-select">
                                            <label class="form-check-label" for="switcher-horizontal">
                                                Horizontal
                                            </label>
                                            <input class="form-check-input" type="radio" name="navigation-style"
                                                id="switcher-horizontal">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="navigation-menu-styles">
                                <p class="switcher-style-head">Vertical & Horizontal Menu Styles:</p>
                                <div class="row switcher-style gx-0 pb-2 gy-2">
                                    <div class="col-4">
                                        <div class="form-check switch-select">
                                            <label class="form-check-label" for="switcher-menu-click">
                                                Menu Click
                                            </label>
                                            <input class="form-check-input" type="radio" name="navigation-menu-styles"
                                                id="switcher-menu-click">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-check switch-select">
                                            <label class="form-check-label" for="switcher-menu-hover">
                                                Menu Hover
                                            </label>
                                            <input class="form-check-input" type="radio" name="navigation-menu-styles"
                                                id="switcher-menu-hover">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-check switch-select">
                                            <label class="form-check-label" for="switcher-icon-click">
                                                Icon Click
                                            </label>
                                            <input class="form-check-input" type="radio" name="navigation-menu-styles"
                                                id="switcher-icon-click">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-check switch-select">
                                            <label class="form-check-label" for="switcher-icon-hover">
                                                Icon Hover
                                            </label>
                                            <input class="form-check-input" type="radio" name="navigation-menu-styles"
                                                id="switcher-icon-hover">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="sidemenu-layout-styles">
                                <p class="switcher-style-head">Sidemenu Layout Styles:</p>
                                <div class="row switcher-style gx-0 pb-2 gy-2">
                                    <div class="col-sm-6">
                                        <div class="form-check switch-select">
                                            <label class="form-check-label" for="switcher-default-menu">
                                                Default Menu
                                            </label>
                                            <input class="form-check-input" type="radio" name="sidemenu-layout-styles"
                                                id="switcher-default-menu" checked>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-check switch-select">
                                            <label class="form-check-label" for="switcher-closed-menu">
                                                Closed Menu
                                            </label>
                                            <input class="form-check-input" type="radio" name="sidemenu-layout-styles"
                                                id="switcher-closed-menu">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-check switch-select">
                                            <label class="form-check-label" for="switcher-icontext-menu">
                                                Icon Text
                                            </label>
                                            <input class="form-check-input" type="radio" name="sidemenu-layout-styles"
                                                id="switcher-icontext-menu">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-check switch-select">
                                            <label class="form-check-label" for="switcher-icon-overlay">
                                                Icon Overlay
                                            </label>
                                            <input class="form-check-input" type="radio" name="sidemenu-layout-styles"
                                                id="switcher-icon-overlay">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-check switch-select">
                                            <label class="form-check-label" for="switcher-detached">
                                                Detached
                                            </label>
                                            <input class="form-check-input" type="radio" name="sidemenu-layout-styles"
                                                id="switcher-detached">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-check switch-select">
                                            <label class="form-check-label" for="switcher-double-menu">
                                                Double Menu
                                            </label>
                                            <input class="form-check-input" type="radio" name="sidemenu-layout-styles"
                                                id="switcher-double-menu">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="">
                                <p class="switcher-style-head">Page Styles:</p>
                                <div class="row switcher-style gx-0">
                                    <div class="col-4">
                                        <div class="form-check switch-select">
                                            <label class="form-check-label" for="switcher-regular">
                                                Regular
                                            </label>
                                            <input class="form-check-input" type="radio" name="page-styles" id="switcher-regular"
                                                checked>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-check switch-select">
                                            <label class="form-check-label" for="switcher-classic">
                                                Classic
                                            </label>
                                            <input class="form-check-input" type="radio" name="page-styles" id="switcher-classic">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-check switch-select">
                                            <label class="form-check-label" for="switcher-modern">
                                                Modern
                                            </label>
                                            <input class="form-check-input" type="radio" name="page-styles" id="switcher-modern">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="">
                                <p class="switcher-style-head">Layout Width Styles:</p>
                                <div class="row switcher-style gx-0">
                                    <div class="col-4">
                                        <div class="form-check switch-select">
                                            <label class="form-check-label" for="switcher-default-width">
                                                compact
                                            </label>
                                            <input class="form-check-input" type="radio" name="layout-width"
                                                id="switcher-default-width">
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <div class="form-check switch-select">
                                            <label class="form-check-label" for="switcher-full-width">
                                                Full Width
                                            </label>
                                            <input class="form-check-input" type="radio" name="layout-width"
                                                id="switcher-full-width" checked>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-check switch-select">
                                            <label class="form-check-label" for="switcher-boxed">
                                                Boxed
                                            </label>
                                            <input class="form-check-input" type="radio" name="layout-width" id="switcher-boxed">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="">
                                <p class="switcher-style-head">Menu Positions:</p>
                                <div class="row switcher-style gx-0">
                                    <div class="col-4">
                                        <div class="form-check switch-select">
                                            <label class="form-check-label" for="switcher-menu-fixed">
                                                Fixed
                                            </label>
                                            <input class="form-check-input" type="radio" name="menu-positions"
                                                id="switcher-menu-fixed" checked>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-check switch-select">
                                            <label class="form-check-label" for="switcher-menu-scroll">
                                                Scrollable
                                            </label>
                                            <input class="form-check-input" type="radio" name="menu-positions"
                                                id="switcher-menu-scroll">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="">
                                <p class="switcher-style-head">Header Positions:</p>
                                <div class="row switcher-style gx-0">
                                    <div class="col-4">
                                        <div class="form-check switch-select">
                                            <label class="form-check-label" for="switcher-header-fixed">
                                                Fixed
                                            </label>
                                            <input class="form-check-input" type="radio" name="header-positions"
                                                id="switcher-header-fixed" checked>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-check switch-select">
                                            <label class="form-check-label" for="switcher-header-scroll">
                                                Scrollable
                                            </label>
                                            <input class="form-check-input" type="radio" name="header-positions"
                                                id="switcher-header-scroll">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="">
                                <p class="switcher-style-head">Loader:</p>
                                <div class="row switcher-style gx-0">
                                    <div class="col-4">
                                        <div class="form-check switch-select">
                                            <label class="form-check-label" for="switcher-loader-enable">
                                                Enable
                                            </label>
                                            <input class="form-check-input" type="radio" name="page-loader"
                                                id="switcher-loader-enable">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-check switch-select">
                                            <label class="form-check-label" for="switcher-loader-disable">
                                                Disable
                                            </label>
                                            <input class="form-check-input" type="radio" name="page-loader"
                                                id="switcher-loader-disable" checked>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade border-0" id="switcher-profile" role="tabpanel"
                            aria-labelledby="switcher-profile-tab" tabindex="0">
                            <div>
                                <div class="theme-colors">
                                    <p class="switcher-style-head">Menu Colors:</p>
                                    <div class="d-flex switcher-style pb-2">
                                        <div class="form-check switch-select me-3">
                                            <input class="form-check-input color-input color-white" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Light Menu" type="radio" name="menu-colors"
                                                id="switcher-menu-light" checked>
                                        </div>
                                        <div class="form-check switch-select me-3">
                                            <input class="form-check-input color-input color-dark" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Dark Menu" type="radio" name="menu-colors"
                                                id="switcher-menu-dark">
                                        </div>
                                        <div class="form-check switch-select me-3">
                                            <input class="form-check-input color-input color-primary" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Color Menu" type="radio" name="menu-colors"
                                                id="switcher-menu-primary">
                                        </div>
                                        <div class="form-check switch-select me-3">
                                            <input class="form-check-input color-input color-gradient" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Gradient Menu" type="radio" name="menu-colors"
                                                id="switcher-menu-gradient">
                                        </div>
                                        <div class="form-check switch-select me-3">
                                            <input class="form-check-input color-input color-transparent" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Transparent Menu" type="radio" name="menu-colors"
                                                id="switcher-menu-transparent">
                                        </div>
                                    </div>
                                    <div class="px-4 pb-3 text-muted fs-11">Note:If you want to change color Menu dynamically change
                                        from below Theme Primary color picker</div>
                                </div>
                                <div class="theme-colors">
                                    <p class="switcher-style-head">Header Colors:</p>
                                    <div class="d-flex switcher-style pb-2">
                                        <div class="form-check switch-select me-3">
                                            <input class="form-check-input color-input color-white" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Light Header" type="radio" name="header-colors"
                                                id="switcher-header-light" checked>
                                        </div>
                                        <div class="form-check switch-select me-3">
                                            <input class="form-check-input color-input color-dark" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Dark Header" type="radio" name="header-colors"
                                                id="switcher-header-dark">
                                        </div>
                                        <div class="form-check switch-select me-3">
                                            <input class="form-check-input color-input color-primary" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Color Header" type="radio" name="header-colors"
                                                id="switcher-header-primary">
                                        </div>
                                        <div class="form-check switch-select me-3">
                                            <input class="form-check-input color-input color-gradient" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Gradient Header" type="radio" name="header-colors"
                                                id="switcher-header-gradient">
                                        </div>
                                        <div class="form-check switch-select me-3">
                                            <input class="form-check-input color-input color-transparent" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Transparent Header" type="radio" name="header-colors"
                                                id="switcher-header-transparent">
                                        </div>
                                    </div>
                                    <div class="px-4 pb-3 text-muted fs-11">Note:If you want to change color Header dynamically
                                        change from below Theme Primary color picker</div>
                                </div>
                                <div class="theme-colors">
                                    <p class="switcher-style-head">Theme Primary:</p>
                                    <div class="d-flex flex-wrap align-items-center switcher-style">
                                        <div class="form-check switch-select me-3">
                                            <input class="form-check-input color-input color-primary-1" type="radio"
                                                name="theme-primary" id="switcher-primary">
                                        </div>
                                        <div class="form-check switch-select me-3">
                                            <input class="form-check-input color-input color-primary-2" type="radio"
                                                name="theme-primary" id="switcher-primary1">
                                        </div>
                                        <div class="form-check switch-select me-3">
                                            <input class="form-check-input color-input color-primary-3" type="radio"
                                                name="theme-primary" id="switcher-primary2">
                                        </div>
                                        <div class="form-check switch-select me-3">
                                            <input class="form-check-input color-input color-primary-4" type="radio"
                                                name="theme-primary" id="switcher-primary3">
                                        </div>
                                        <div class="form-check switch-select me-3">
                                            <input class="form-check-input color-input color-primary-5" type="radio"
                                                name="theme-primary" id="switcher-primary4">
                                        </div>
                                        <div class="form-check switch-select ps-0 mt-1 color-primary-light">
                                            <div class="theme-container-primary"></div>
                                            <div class="pickr-container-primary" onchange="updateChartColor(this.value)"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="theme-colors">
                                    <p class="switcher-style-head">Theme Background:</p>
                                    <div class="d-flex flex-wrap align-items-center switcher-style">
                                        <div class="form-check switch-select me-3">
                                            <input class="form-check-input color-input color-bg-1" type="radio"
                                                name="theme-background" id="switcher-background">
                                        </div>
                                        <div class="form-check switch-select me-3">
                                            <input class="form-check-input color-input color-bg-2" type="radio"
                                                name="theme-background" id="switcher-background1">
                                        </div>
                                        <div class="form-check switch-select me-3">
                                            <input class="form-check-input color-input color-bg-3" type="radio"
                                                name="theme-background" id="switcher-background2">
                                        </div>
                                        <div class="form-check switch-select me-3">
                                            <input class="form-check-input color-input color-bg-4" type="radio"
                                                name="theme-background" id="switcher-background3">
                                        </div>
                                        <div class="form-check switch-select me-3">
                                            <input class="form-check-input color-input color-bg-5" type="radio"
                                                name="theme-background" id="switcher-background4">
                                        </div>
                                        <div class="form-check switch-select ps-0 mt-1 tooltip-static-demo color-bg-transparent">
                                            <div class="theme-container-background"></div>
                                            <div class="pickr-container-background"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="menu-image mb-3">
                                    <p class="switcher-style-head">Menu With Background Image:</p>
                                    <div class="d-flex flex-wrap align-items-center switcher-style">
                                        <div class="form-check switch-select m-2">
                                            <input class="form-check-input bgimage-input bg-img1" type="radio"
                                                name="menu-background" id="switcher-bg-img">
                                        </div>
                                        <div class="form-check switch-select m-2">
                                            <input class="form-check-input bgimage-input bg-img2" type="radio"
                                                name="menu-background" id="switcher-bg-img1">
                                        </div>
                                        <div class="form-check switch-select m-2">
                                            <input class="form-check-input bgimage-input bg-img3" type="radio"
                                                name="menu-background" id="switcher-bg-img2">
                                        </div>
                                        <div class="form-check switch-select m-2">
                                            <input class="form-check-input bgimage-input bg-img4" type="radio"
                                                name="menu-background" id="switcher-bg-img3">
                                        </div>
                                        <div class="form-check switch-select m-2">
                                            <input class="form-check-input bgimage-input bg-img5" type="radio"
                                                name="menu-background" id="switcher-bg-img4">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between canvas-footer flex-nowrap gap-2">
                            <a href="javascript:void(0);" class="btn btn-primary text-nowrap">Save</a>
                            <a href="javascript:void(0);" id="reset-all" class="btn btn-danger text-nowrap">Reset All</a>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="offcanvas">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        <!-- END SWITCHER -->

        <!-- LOADER -->
        <div id="loader">
            <img src="{{ asset('images/banners/loader.svg') }}" alt="">
        </div>
        <!-- END LOADER -->

        <!-- PAGE -->
        <div class="page">

            <!-- HEADER -->

            <header class="app-header sticky" id="header">

                <!-- Start::main-header-container -->
                <div class="main-header-container container-fluid">

                    <!-- Start::header-content-left -->
                    <div class="header-content-left">

                        <!-- Start::header-element -->
                        <div class="header-element">
                            <div class="horizontal-logo">
                                <a href="{{ url('/home') }}" class="header-logo">
                                    <img src="{{ asset('images/logos/desktop-logo.png') }}" alt="logo" class="desktop-logo">
                                    <img src="{{ asset('images/logos/toggle-dark.png') }}" alt="logo" class="toggle-dark">
                                    <img src="{{ asset('images/logos/desktop-dark.png') }}" alt="logo" class="desktop-dark">
                                    <img src="{{ asset('images/logos/toggle-logo.png') }}" alt="logo" class="toggle-logo">
                                </a>
                            </div>
                        </div>
                        <!-- End::header-element -->

                        <!-- Start::header-element -->
                        <div class="header-element">
                            <!-- Start::header-link -->
                            <a aria-label="Hide Sidebar" class="sidemenu-toggle header-link" data-bs-toggle="sidebar"
                                href="javascript:void(0);">
                                <svg xmlns="http://www.w3.org/2000/svg" class="header-link-icon menu-btn" width="32" height="32"
                                    fill="#000000" viewBox="0 0 256 256">
                                    <path
                                        d="M224,128a8,8,0,0,1-8,8H40a8,8,0,0,1,0-16H216A8,8,0,0,1,224,128ZM40,72H216a8,8,0,0,0,0-16H40a8,8,0,0,0,0,16ZM216,184H40a8,8,0,0,0,0,16H216a8,8,0,0,0,0-16Z">
                                    </path>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="header-link-icon menu-btn-close" width="32"
                                    height="32" fill="#000000" viewBox="0 0 256 256">
                                    <path
                                        d="M205.66,194.34a8,8,0,0,1-11.32,11.32L128,139.31,61.66,205.66a8,8,0,0,1-11.32-11.32L116.69,128,50.34,61.66A8,8,0,0,1,61.66,50.34L128,116.69l66.34-66.35a8,8,0,0,1,11.32,11.32L139.31,128Z">
                                    </path>
                                </svg>
                            </a>
                            <!-- End::header-link -->
                        </div>
                        <!-- End::header-element -->
                    </div>
                    <!-- End::header-content-left -->

                    <!-- Start::header-content-right -->
                    <ul class="header-content-right">

                        <!-- Start::header-element -->
                        <li class="header-element d-md-none d-block">
                            <a href="javascript:void(0);" class="header-link" data-bs-toggle="modal"
                                data-bs-target="#header-responsive-search">
                                <!-- Start::header-link-icon -->
                                <i class="bi bi-search header-link-icon"></i>
                                <!-- End::header-link-icon -->
                            </a>
                        </li>
                        <!-- End::header-element -->

                        <!-- Start::header-element -->
                        <li class="header-element header-theme-mode">
                            <!-- Start::header-link|layout-setting -->
                            <a href="javascript:void(0);" class="header-link layout-setting">
                                <span class="light-layout">
                                    <!-- Start::header-link-icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="header-link-icon" viewBox="0 0 256 256">
                                        <rect width="256" height="256" fill="none" />
                                        <path d="M108.11,28.11A96.09,96.09,0,0,0,227.89,147.89,96,96,0,1,1,108.11,28.11Z"
                                            fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="16" />
                                    </svg>
                                    <!-- End::header-link-icon -->
                                </span>
                                <span class="dark-layout">
                                    <!-- Start::header-link-icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="header-link-icon" viewBox="0 0 256 256">
                                        <rect width="256" height="256" fill="none" />
                                        <line x1="128" y1="40" x2="128" y2="32" fill="none" stroke="currentColor"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="16" />
                                        <circle cx="128" cy="128" r="56" fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="16" />
                                        <line x1="64" y1="64" x2="56" y2="56" fill="none" stroke="currentColor"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="16" />
                                        <line x1="64" y1="192" x2="56" y2="200" fill="none" stroke="currentColor"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="16" />
                                        <line x1="192" y1="64" x2="200" y2="56" fill="none" stroke="currentColor"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="16" />
                                        <line x1="192" y1="192" x2="200" y2="200" fill="none" stroke="currentColor"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="16" />
                                        <line x1="40" y1="128" x2="32" y2="128" fill="none" stroke="currentColor"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="16" />
                                        <line x1="128" y1="216" x2="128" y2="224" fill="none" stroke="currentColor"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="16" />
                                        <line x1="216" y1="128" x2="224" y2="128" fill="none" stroke="currentColor"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="16" />
                                    </svg>
                                    <!-- End::header-link-icon -->
                                </span>
                            </a>
                            <!-- End::header-link|layout-setting -->
                        </li>
                        <!-- End::header-element -->

                        <!-- Start::header-element -->
                        <li class="header-element header-fullscreen">
                            <!-- Start::header-link -->
                            <a href="javascript:void(0);" class="header-link" id="fullscreen-toggle">
                                <i class="bi bi-fullscreen fs-18 header-link-icon fullscreen-open"></i>
                                <i class="bi bi-fullscreen-exit fs-18 header-link-icon fullscreen-close d-none"></i>
                            </a>
                            <!-- End::header-link -->
                        </li>
                        <!-- End::header-element -->

                    <!-- Start::header-element -->
                    <li class="header-element dropdown">
                        <!-- Start::header-link|dropdown-toggle -->
                        <a href="javascript:void(0);" class="header-link dropdown-toggle" id="mainHeaderProfile"
                                data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                <div class="d-flex align-items-center">
                                    <div class="me-xl-2 me-0">
                                        <img src="{{ auth()->user()->profile_image ? asset('storage/' . auth()->user()->profile_image) : (strtolower(auth()->user()->gender) == 'male' ? asset('images/faces/14.jpg') : asset('images/faces/2.jpg')) }}" alt="img" class="avatar avatar-sm avatar-rounded">
                                    </div>
                                    <div class="d-xl-block d-none lh-1">
                                        <span class="fw-medium lh-1">{{ auth()->user()->nick_name }}</span>
                                    </div>
                                </div>
                            </a>
                            <!-- End::header-link|dropdown-toggle -->
                            <ul class="main-header-dropdown dropdown-menu pt-0 overflow-hidden header-profile-dropdown dropdown-menu-end"
                                aria-labelledby="mainHeaderProfile">
                                <li>
                                    <div class="py-2 px-3 text-center"> <span class="fw-semibold">{{ auth()->user()->full_name }}</span> <span
                                            class="d-block fs-12 text-muted">{{ auth()->user()->position }}</span> </div>
                                </li>
                                <li><a class="dropdown-item d-flex align-items-center" href="javascript:void(0);"><i
                                            class="ti ti-user text-primary me-2 fs-16"></i>Profile</a>
                                </li>
                                <li><a class="dropdown-item d-flex align-items-center" href="javascript:void(0);"><i
                                            class="ti ti-settings text-info me-2 fs-16"></i>Settings</a>
                                </li>
                                <li><a class="dropdown-item d-flex align-items-center" href="javascript:void(0);"><i
                                            class="ti ti-headset text-warning me-2 fs-16"></i>Support</a>
                                </li>
                                <li class="py-2 px-3">
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm w-100">Log Out</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                        <!-- End::header-element -->

                        <!-- Start::header-element -->
                        <li class="header-element">
                            <!-- Start::header-link|switcher-icon -->
                            <a href="javascript:void(0);" class="header-link switcher-icon" data-bs-toggle="offcanvas"
                                data-bs-target="#switcher-canvas">
                                <svg xmlns="http://www.w3.org/2000/svg" class="header-link-icon" viewBox="0 0 256 256">
                                    <rect width="256" height="256" fill="none" />
                                    <circle cx="128" cy="128" r="40" fill="none" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round" stroke-width="16" />
                                    <path
                                        d="M41.43,178.09A99.14,99.14,0,0,1,31.36,153.8l16.78-21a81.59,81.59,0,0,1,0-9.64l-16.77-21a99.43,99.43,0,0,1,10.05-24.3l26.71-3a81,81,0,0,1,6.81-6.81l3-26.7A99.14,99.14,0,0,1,102.2,31.36l21,16.78a81.59,81.59,0,0,1,9.64,0l21-16.77a99.43,99.43,0,0,1,24.3,10.05l3,26.71a81,81,0,0,1,6.81,6.81l26.7,3a99.14,99.14,0,0,1,10.07,24.29l-16.78,21a81.59,81.59,0,0,1,0,9.64l16.77,21a99.43,99.43,0,0,1-10,24.3l-26.71,3a81,81,0,0,1-6.81,6.81l-3,26.7a99.14,99.14,0,0,1-24.29,10.07l-21-16.78a81.59,81.59,0,0,1-9.64,0l-21,16.77a99.43,99.43,0,0,1-24.3-10l-3-26.71a81,81,0,0,1-6.81-6.81Z"
                                        fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="16" />
                                </svg>
                            </a>
                            <!-- End::header-link|switcher-icon -->
                        </li>
                        <!-- End::header-element -->

                    </ul>
                    <!-- End::header-content-right -->

                </div>
                <!-- End::main-header-container -->

            </header>

            <div class="modal fade" id="header-responsive-search" tabindex="-1" aria-labelledby="header-responsive-search" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="input-group">
                                <input type="text" class="form-control border-end-0" placeholder="Search Anything ..."
                                    aria-label="Search Anything ..." aria-describedby="button-addon2">
                                <button class="btn btn-primary" type="button"
                                    id="button-addon2"><i class="bi bi-search"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END HEADER -->

            <!-- SIDEBAR -->
            <aside class="app-sidebar sticky" id="sidebar">

                <!-- Start::main-sidebar-header -->
                <div class="main-sidebar-header">
                    <a href="{{ url('/home') }}" class="header-logo">
                        <img src="{{ asset('images/logos/desktop-logo.png') }}" alt="logo" class="desktop-logo">
                        <img src="{{ asset('images/logos/toggle-dark.png') }}" alt="logo" class="toggle-dark">
                        <img src="{{ asset('images/logos/desktop-dark.png') }}" alt="logo" class="desktop-dark">
                        <img src="{{ asset('images/logos/toggle-logo.png') }}" alt="logo" class="toggle-logo">
                    </a>
                </div>
                <!-- End::main-sidebar-header -->

                <!-- Start::main-sidebar -->
                <div class="main-sidebar" id="sidebar-scroll">

                    <!-- Start::nav -->
                    <nav class="main-menu-container nav nav-pills flex-column sub-open">
                        <div class="slide-left" id="slide-left">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24"> <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path> </svg>
                        </div>
                        <ul class="main-menu">
                            <!-- Start::slide__category -->
                            <li class="slide__category"><span class="category-name">PRODUCTION</span></li>
                            <!-- End::slide__category -->

                            <!-- Start::slide -->
                            <li class="slide has-sub">
                                <a href="javascript:void(0);" class="side-menu__item">
                                    <i class="ri-arrow-right-s-line side-menu__angle"></i>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 256 256"><rect width="256" height="256" fill="none"/><path d="M104,216V152h48v64h64V120a8,8,0,0,0-2.34-5.66l-80-80a8,8,0,0,0-11.32,0l-80,80A8,8,0,0,0,40,120v96Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/></svg>
                                    <span class="side-menu__label">Dashboards</span>
                                </a>
                                <ul class="slide-menu child1">
                                    <li class="slide side-menu__label1">
                                        <a href="javascript:void(0)">PRODUCTION</a>
                                    </li>
                                    <li class="slide">
                                        <a href="{{ route('endtime') }}" class="side-menu__item">Endtime & Submitted</a>
                                    </li>
                                    <li class="slide">
                                        <a href="javascript:void(0)" class="side-menu__item">Machine Allocation</a>
                                    </li>
                                    <li class="slide">
                                        <a href="javascript:void(0)" class="side-menu__item">Endline Lot Monitoring</a>
                                    </li>
                                    <li class="slide">
                                        <a href="javascript:void(0)" class="side-menu__item">Stagnation Monitoring | TAT</a>
                                    </li>
                                    <li class="slide">
                                        <a href="javascript:void(0)" class="side-menu__item">Machine Efficiency | OEE</a>
                                    </li>
                                </ul>
                            </li>
                            <!-- End::slide -->

                            <!-- Start::slide__category -->
                            <li class="slide__category"><span class="category-name">EQUIPMENT</span></li>
                            <!-- End::slide__category -->

                            <!-- Start::slide -->
                            <li class="slide has-sub">
                                <a href="javascript:void(0);" class="side-menu__item">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 256 256"><rect width="256" height="256" fill="none"/><rect x="48" y="48" width="64" height="64" rx="8" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><rect x="144" y="48" width="64" height="64" rx="8" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><rect x="48" y="144" width="64" height="64" rx="8" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><rect x="144" y="144" width="64" height="64" rx="8" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/></svg>
                                    <span class="side-menu__label">EQUIPMENT</span>
                                    <i class="ri-arrow-right-s-line side-menu__angle"></i>
                                </a>
                                <ul class="slide-menu child1">
                                    <li class="slide side-menu__label1">
                                        <a href="javascript:void(0)">EQUIPMENT</a>
                                    </li>
                                    <li class="slide">
                                        <a href="{{ route('escalation') }}" class="side-menu__item">Machine Escalation</a>
                                    </li>
                                    <li class="slide">
                                        <a href="javascript:void(0)" class="side-menu__item">Machine MTBI</a>
                                    </li>
                                </ul>
                            </li>
                            <!-- End::slide -->

                            <!-- Start::slide__category -->
                            <li class="slide__category"><span class="category-name">TECHNICAL</span></li>
                            <!-- End::slide__category -->

                            <!-- Start::slide -->
                            <li class="slide has-sub">
                                <a href="javascript:void(0);" class="side-menu__item">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 256 256"><rect width="256" height="256" fill="none"/><polyline points="224 208 32 208 32 48" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><polyline points="200 72 128 144 96 112 32 176" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><polyline points="200 112 200 72 160 72" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/></svg>
                                    <span class="side-menu__label">TECHNICAL</span>
                                    <i class="ri-arrow-right-s-line side-menu__angle"></i>
                                </a>
                                <ul class="slide-menu child1">
                                    <li class="slide side-menu__label1">
                                        <a href="javascript:void(0)">TECHNICAL</a>
                                    </li>
                                    <li class="slide">
                                        <a href="javascript:void(0);" class="side-menu__item">QC Passing Rate | LRR</a>
                                    </li>
                                    <li class="slide">
                                        <a href="javascript:void(0);" class="side-menu__item">Yield Rate</a>
                                    </li>
                                    <li class="slide">
                                        <a href="javascript:void(0);" class="side-menu__item">Visual Machine Defect | VMD</a>
                                    </li>
                                </ul>
                            </li>
                            <!-- End::slide -->

                            <!-- Start::slide__category -->
                            <li class="slide__category"><span class="category-name">OTHERS</span></li>
                            <!-- End::slide__category -->

                            <!-- Start::slide -->
                            <li class="slide has-sub">
                                <a href="javascript:void(0);" class="side-menu__item">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 256 256"><rect width="256" height="256" fill="none"/><rect x="40" y="88" width="176" height="128" rx="8" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><circle cx="128" cy="152" r="12"/><path d="M88,88V56a40,40,0,0,1,80,0V88" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/></svg>
                                    <span class="side-menu__label">Profile</span>
                                    <i class="ri-arrow-right-s-line side-menu__angle"></i>
                                </a>
                                <ul class="slide-menu child1">
                                    <li class="slide side-menu__label1">
                                        <a href="javascript:void(0)">Authentication</a>
                                    </li>
                                    <li class="slide">
                                        <a href="javascript:void(0);" class="side-menu__item">Setting</a>
                                    </li>
                                </ul>
                            </li>
                            <!-- End::slide -->

                        </ul>
                        <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24"> <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path> </svg></div>
                    </nav>
                    <!-- End::nav -->

                </div>
                <!-- End::main-sidebar -->

            </aside>
            <!-- END SIDEBAR -->

            <!-- MAIN-CONTENT -->

            <!-- Start::app-content -->

            @yield('content')

            <!-- End::app-content -->

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

        </div>
        <!-- END PAGE-->

        <!-- SCRIPTS -->

        <!-- SCROLL-TO-TOP -->
        <div class="scrollToTop">
                <span class="arrow lh-1"><i class="ti ti-arrow-big-up fs-16"></i></span>
        </div>
        <div id="responsive-overlay"></div>

        <!-- POPPER JS -->
        <script src="{{ asset('vendor/@popperjs/core/umd/popper.min.js') }}"></script>

        <!-- BOOTSTRAP JS -->
        <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

        <!-- NODE WAVES JS -->
        <script src="{{ asset('vendor/node-waves/waves.min.js') }}"></script>

        <!-- SIMPLEBAR JS -->
        <script src="{{ asset('vendor/simplebar/simplebar.min.js') }}"></script>
        <script src="{{ asset('js/simplebar.js') }}"></script>


        <!-- PICKER JS -->
        <script src="{{ asset('vendor/flatpickr/flatpickr.min.js') }}"></script>
        <script src="{{ asset('vendor/@simonwep/pickr/pickr.es5.min.js') }}"></script>

        <!-- AUTO COMPLETE JS -->
        <script src="{{ asset('vendor/@tarekraafat/autocomplete.js/autoComplete.min.js') }}"></script>


        <!-- Shepherd JS -->
        <script src="{{ asset('vendor/shepherd.js/js/shepherd.min.js') }}"></script>

        <!-- Internal Tour JS -->
        {{-- <script src="{{ asset('js/tour.js') }}"></script> --}}


        <!-- STICKY JS -->
        <script src="{{ asset('js/sticky.js') }}"></script>

        <!-- DEFAULTMENU JS -->
        <script src="{{ asset('js/defaultmenu.js') }}"></script>

        <!-- CUSTOM JS -->
        <script src="{{ asset('js/custom.js') }}"></script>

        <!-- CUSTOM-SWITCHER JS -->
        <script src="{{ asset('js/custom-switcher.js') }}"></script>

        <!-- END SCRIPTS -->

    </body>

</html>