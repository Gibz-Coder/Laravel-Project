@extends('layouts.auth.home')
@section('title', 'Home')


@section('content')

<div class="main-content app-content">
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="my-2 page-header-breadcrumb d-flex align-items-center justify-content-between flex-wrap gap-2">
        </div>
        <!-- Page Header Close -->

        <!-- Start:: row-1 -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-body p-5 text-center">
                        <div class="row justify-content-center">
                            <div class="col-xl-7 mb-5">
                                <div class="logo-container p-2 bg-light rounded" style="display: inline-block;">
                                    <div class="p-2 bg-white rounded">
                                        <img src="{{ asset('images/logos/samsung-logo.png') }}" alt="Samsung Logo" id="step-1" style="max-width: 180px; height: auto;">
                                    </div>
                                </div>
                                <h5 class="fw-semibold mt-2">Welcome to Visual Centralize Dashboard</h5>
                                <span class="text-muted">MLCC VISUAL INSPECTION</span>
                            </div>
                        </div>
                        <div class="row justify-content-center gap-4">
                            <div class="col-xl-3">
                                <div class="p-3 border mb-4 rounded">
                                    <div class="card custom-card border shadow-none team-member primary mb-0">
                                        <a href="{{ url('/endtime') }}" style="text-decoration: none;">
                                            <div class="card-body p-0 text-center position-relative" style="min-height: 150px; background-size: cover; background-position: center;" id="endtime-card">
                                                <div style="position: absolute; bottom: 0; left: 0; width: 100%; padding: 1rem; z-index: 1;">
                                                    <h6 class="fw-bold mb-2" id="endtime-text" style="text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);">Machine Endtime and Submitted</h6>
                                                </div>
                                            </div>
                                        </a>
                                        <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                // Set initial background based on theme
                                                updateCardBackground();

                                                // Add listener for theme changes
                                                document.querySelector('.layout-setting').addEventListener('click', function() {
                                                    // Wait a bit for the theme to change
                                                    setTimeout(updateCardBackground, 100);
                                                });

                                                function updateCardBackground() {
                                                    const card = document.getElementById('endtime-card');
                                                    const textElement = document.getElementById('endtime-text');
                                                    const isDarkMode = document.documentElement.getAttribute('data-theme-mode') === 'dark';

                                                    if (isDarkMode) {
                                                        card.style.backgroundImage = "url('{{ asset('images/home/endtime-dark.jpg') }}')";
                                                        textElement.style.color = '#ffffff'; // White text for dark mode
                                                    } else {
                                                        card.style.backgroundImage = "url('{{ asset('images/home/endtime-light.jpg') }}')";
                                                        textElement.style.color = '#000000'; // Black text for light mode
                                                    }
                                                }
                                            });
                                        </script>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3">
                                <div class="p-3 border mb-4  rounded">
                                    <div class="card custom-card border p-0 mb-0 team-member secondary">
                                        <div class="card-body p-4 bg-light text-center">
                                            <div class="mb-3">
                                                <span class="avatar avatar-xl bg-secondary-transparent" id="step-3">
                                                    <span class="avatar avatar-lg bg-secondary svg-white">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256"><rect width="256" height="256" fill="none"/><path d="M48,208H16a8,8,0,0,1-8-8V160a8,8,0,0,1,8-8H48Z" opacity="0.2"/><path d="M204,56a28,28,0,0,0-12,2.71h0A28,28,0,1,0,176,85.29h0A28,28,0,1,0,204,56Z" opacity="0.2"/><circle cx="204" cy="84" r="28" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><path d="M48,208H16a8,8,0,0,1-8-8V160a8,8,0,0,1,8-8H48" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><path d="M112,160h32l67-15.41a16.61,16.61,0,0,1,21,16h0a16.59,16.59,0,0,1-9.18,14.85L184,192l-64,16H48V152l25-25a24,24,0,0,1,17-7H140a20,20,0,0,1,20,20h0a20,20,0,0,1-20,20Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><path d="M176,85.29A28,28,0,1,1,192,58.71" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/></svg>
                                                </span>
                                            </span>
                                            </div>
                                            <div>
                                            <h6 class="fw-meidum mb-2">Machine Escalation</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3">
                                <div class="p-3 border mb-4  rounded">
                                    <div class="card custom-card border p-0 mb-0 team-member success">
                                        <div class="card-body p-4 bg-light text-center">
                                            <div class="mb-3">
                                                <span class="avatar avatar-xl  bg-success-transparent" id="step-4">
                                                    <span class="avatar avatar-lg  bg-success svg-white">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256"><rect width="256" height="256" fill="none"/><path d="M88,224l24-24V176l24-24,48,72,24-24-32-88,33-31A24,24,0,0,0,175,47L144,80,56,48,32,72l72,48L80,144H56L32,168l40,16Z" opacity="0.2"/><path d="M88,224l24-24V176l24-24,48,72,24-24-32-88,33-31A24,24,0,0,0,175,47L144,80,56,48,32,72l72,48L80,144H56L32,168l40,16Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/></svg>
                                                </span>
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="fw-meidum mb-2">Technical Dashboard</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3">
                                <div class="p-3 border mb-4  rounded">
                                    <div class="card custom-card border p-0 mb-0 team-member warning">
                                        <div class="card-body p-4 bg-light text-center">
                                            <div class="mb-3">
                                                <span class="avatar avatar-xl  bg-warning-transparent" id="step-5">
                                                    <span class="avatar avatar-lg  bg-warning svg-white">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256"><rect width="256" height="256" fill="none"/><path d="M60.06,195.91a96,96,0,0,1-.12-135.65h0a95.7,95.7,0,0,1,28,67.76,95.74,95.74,0,0,1-28,67.77Z" opacity="0.2"/><path d="M196.06,195.91a96,96,0,0,1-.12-135.65h0a96,96,0,0,1,0,135.53Z" opacity="0.2"/><circle cx="128" cy="128" r="96" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><path d="M60,60.24A95.7,95.7,0,0,1,88,128a95.7,95.7,0,0,1-28,67.76" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><path d="M196,60.24a96,96,0,0,0,0,135.52" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><line x1="32" y1="128" x2="224" y2="128" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><line x1="128" y1="32" x2="128" y2="224" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/></svg>
                                                </span>
                                            </span>
                                            </div>
                                            <div>
                                                <h6 class="fw-meidum mb-2">Lot Requst</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3">
                                <div class="p-3 border mb-4  rounded">
                                    <div class="card custom-card border p-0 mb-0 team-member info">
                                        <div class="card-body p-4 bg-light text-center">
                                            <div class="mb-3">
                                                <span class="avatar avatar-xl bg-info-transparent" id="step-6">
                                                    <span class="avatar avatar-lg bg-info svg-white">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256"><rect width="256" height="256" fill="none"/><path d="M60.06,195.91a96,96,0,0,1-.12-135.65h0a95.7,95.7,0,0,1,28,67.76,95.74,95.74,0,0,1-28,67.77Z" opacity="0.2"/><path d="M196.06,195.91a96,96,0,0,1-.12-135.65h0a96,96,0,0,1,0,135.53Z" opacity="0.2"/><circle cx="128" cy="128" r="96" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><path d="M60,60.24A95.7,95.7,0,0,1,88,128a95.7,95.7,0,0,1-28,67.76" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><path d="M196,60.24a96,96,0,0,0,0,135.52" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><line x1="32" y1="128" x2="224" y2="128" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><line x1="128" y1="32" x2="128" y2="224" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/></svg>
                                                </span>
                                            </span>
                                            </div>
                                            <div>
                                                <h6 class="fw-meidum mb-2">Endline Lot Monitoring</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3">
                                <div class="p-3 border mb-4  rounded">
                                    <div class="card custom-card border p-0 mb-0 team-member success">
                                        <div class="card-body p-4 bg-light text-center">
                                            <div class="mb-3">
                                                <span class="avatar avatar-xl  bg-danger-transparent" id="step-4">
                                                    <span class="avatar avatar-lg  bg-danger svg-white">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256"><rect width="256" height="256" fill="none"/><path d="M88,224l24-24V176l24-24,48,72,24-24-32-88,33-31A24,24,0,0,0,175,47L144,80,56,48,32,72l72,48L80,144H56L32,168l40,16Z" opacity="0.2"/><path d="M88,224l24-24V176l24-24,48,72,24-24-32-88,33-31A24,24,0,0,0,175,47L144,80,56,48,32,72l72,48L80,144H56L32,168l40,16Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/></svg>
                                                </span>
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="fw-meidum mb-2">Machine Allocation Dashboard</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End:: row-1 -->

    </div>
</div>

@endsection

