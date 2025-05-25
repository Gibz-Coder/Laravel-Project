@extends('layouts.pages.escalation')
@section('title', 'Escalation')
@section('content')

<!-- Start::app-content -->
<div class="main-content app-content">
    <div class="container-fluid">

        <!-- Start::page-header -->
        <div class="my-4 page-header-breadcrumb d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <button class="btn btn-primary-light btn-wave me-2 waves-effect waves-light" style="cursor: pointer;">
                    <i class="ri-calendar-event-line align-middle"></i>
                    <span id="selected-date">Jan 15, 2024</span>
                </button>
                <input type="date" id="endtime" value="2024-01-15" style="width: 0; height: 0; padding: 0; border: 0; position: absolute; visibility: hidden;">
                <button class="btn btn-secondary-light btn-wave me-0 waves-effect waves-light">
                    <i class="ri-upload-cloud-line align-middle"></i> Export Report
                </button>
            </div>
        </div>
        <!-- End::page-header -->

        <!-- Start:: row-1 -->
        <div class="row">
            <div class="col-xxl-3">
                <div class="card custom-card overflow-hidden">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            Escalation per Machine Type
                        </div><div class="dropdown"> 
                            <a href="javascript:void(0);" class="p-2 fs-12 text-muted" data-bs-toggle="dropdown" aria-expanded="false"> Sort By <i class="ri-arrow-down-s-line align-middle ms-1 d-inline-block"></i> </a> 
                            <ul class="dropdown-menu" role="menu"> 
                                <li><a class="dropdown-item" href="javascript:void(0);">Today</a></li>
                                <li><a class="dropdown-item" href="javascript:void(0);">This Week</a></li> 
                                <li><a class="dropdown-item" href="javascript:void(0);">This Month</a></li> 
                            </ul> 
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div id="top-categories" class="p-4 pb-3"></div>
                        <div class="border-top">
                            <ul class="list-group list-group-flush top-categories">
                                <li class="list-group-item">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="lh-1">
                                            <div class="fw-semibold mb-1">Wintec</div>
                                            <div><span class="text-muted fs-13">Increased by <span class="text-success fw-medium ms-1 d-inline-flex align-items-center">0.64%<i class="ti ti-trending-up ms-1"></i></span></span></div>
                                        </div>
                                        <div class="lh-1 text-end">
                                            <span class="d-block fw-semibold h6 mb-0">1,754</span>
                                            <span class="d-block fs-13 text-muted">Escalation</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="lh-1">
                                            <div class="fw-semibold mb-1">TWA</div>
                                            <div><span class="text-muted fs-13">Decreased by <span class="text-danger fw-medium ms-1 d-inline-flex align-items-center">2.75%<i class="ti ti-trending-down ms-1"></i></span></span></div>
                                        </div>
                                        <div class="lh-1 text-end">
                                            <span class="d-block fw-semibold h6 mb-0">1,234</span>
                                            <span class="d-block fs-13 text-muted">Escalation</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="lh-1">
                                            <div class="fw-semibold mb-1">GMC-G1</div>
                                            <div><span class="text-muted fs-13">Increased by <span class="text-success fw-medium ms-1 d-inline-flex align-items-center">1.54%<i class="ti ti-trending-up ms-1"></i></span></span></div>
                                        </div>
                                        <div class="lh-1 text-end">
                                            <span class="d-block fw-semibold h6 mb-0">878</span>
                                            <span class="d-block fs-13 text-muted">Escalation</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="lh-1">
                                            <div class="fw-semibold mb-1">GMC-G3</div>
                                            <div><span class="text-muted fs-13">Increased by <span class="text-success fw-medium ms-1 d-inline-flex align-items-center">1.54%<i class="ti ti-trending-up ms-1"></i></span></span></div>
                                        </div>
                                        <div class="lh-1 text-end">
                                            <span class="d-block fw-semibold h6 mb-0">270</span>
                                            <span class="d-block fs-13 text-muted">Escalation</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="lh-1">
                                            <div class="fw-semibold mb-1">GMC-G20</div>
                                            <div><span class="text-muted fs-13">Decreased by <span class="text-danger fw-medium ms-1 d-inline-flex align-items-center">0.12%<i class="ti ti-trending-down ms-1"></i></span></span></div>
                                        </div>
                                        <div class="lh-1 text-end">
                                            <span class="d-block fw-semibold h6 mb-0">456</span>
                                            <span class="d-block fs-13 text-muted">Escalation</span>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-9">
                <div class="row">
                    <div class="col-xl-3">
                        <div class="card custom-card main-card-item primary">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-3 flex-wrap">
                                    <div>
                                        <span class="d-block mb-3 fw-medium">Total Escalation</span>
                                        <h3 class="fw-semibold lh-1 mb-0">1,234</h3>
                                    </div>
                                    <div class="text-end">
                                        <div class="mb-4">
                                            <span class="avatar avatar-md bg-primary svg-white avatar-rounded">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256"><rect width="256" height="256" fill="none"/><rect x="32" y="48" width="192" height="160" rx="8" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><path d="M168,88a40,40,0,0,1-80,0" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/></svg>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <a href="javascript:void(0);" class="text-muted text-decoration-underline fw-medium fs-13">View all Escalation</a>
                                    <span class="text-success fw-semibold"><i class="ti ti-arrow-narrow-up"></i>0.29%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3">
                        <div class="card custom-card main-card-item">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-3 flex-wrap">
                                    <div>
                                        <span class="d-block mb-3 fw-medium">Completed Escalation</span>
                                        <h3 class="fw-semibold lh-1 mb-0">2,145</h3>
                                    </div>
                                    <div class="text-end">
                                        <div class="mb-4">
                                            <span class="avatar avatar-md bg-secondary svg-white avatar-rounded">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256"><rect width="256" height="256" fill="none"/><line x1="128" y1="24" x2="128" y2="232" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><path d="M184,88a40,40,0,0,0-40-40H112a40,40,0,0,0,0,80h40a40,40,0,0,1,0,80H104a40,40,0,0,1-40-40" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/></svg>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <a href="javascript:void(0);" class="text-muted text-decoration-underline fw-medium fs-13">complete revenue</a>
                                    <span class="text-danger fw-semibold"><i class="ti ti-arrow-narrow-up"></i>3.45%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3">
                        <div class="card custom-card main-card-item">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-3 flex-wrap">
                                    <div>
                                        <span class="d-block mb-3 fw-medium">Pending Escalation</span>
                                        <h3 class="fw-semibold lh-1 mb-0">1,234</h3>
                                    </div>
                                    <div class="text-end">
                                        <div class="mb-4">
                                            <span class="avatar avatar-md bg-success svg-white avatar-rounded">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256"><rect width="256" height="256" fill="none"/><circle cx="84" cy="108" r="52" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><path d="M10.23,200a88,88,0,0,1,147.54,0" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><path d="M172,160a87.93,87.93,0,0,1,73.77,40" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><path d="M152.69,59.7A52,52,0,1,1,172,160" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/></svg>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <a href="javascript:void(0);" class="text-muted text-decoration-underline fw-medium fs-13">Total page views</a>
                                    <span class="text-success fw-semibold"><i class="ti ti-arrow-narrow-up"></i>11.54%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3">
                        <div class="card custom-card main-card-item">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-3 flex-wrap">
                                    <div>
                                        <span class="d-block mb-3 fw-medium">Completion Rate</span>
                                        <h3 class="fw-semibold lh-1 mb-0">85%</h3>
                                    </div>
                                    <div class="text-end">
                                        <div class="mb-4">
                                            <span class="avatar avatar-md bg-info svg-white avatar-rounded">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256"><rect width="256" height="256" fill="none"/><path d="M40,56V184a16,16,0,0,0,16,16H216a8,8,0,0,0,8-8V80a8,8,0,0,0-8-8H56A16,16,0,0,1,40,56h0A16,16,0,0,1,56,40H192" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><circle cx="180" cy="132" r="12"/></svg>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <a href="javascript:void(0);" class="text-muted text-decoration-underline fw-medium fs-13">Total profit earned</a>
                                    <span class="text-success fw-semibold"><i class="ti ti-arrow-narrow-up"></i>0.18%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-7">
                        <div class="card custom-card">
                            <div class="card-header justify-content-between">
                                <div class="card-title">
                                    Machine Escalation Trend
                                </div>
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <div class="btn-group btn-group-sm" role="group" aria-label="Basic example"> 
                                        <button type="button" class="btn btn-primary btn-wave">Daily</button> 
                                        <button type="button" class="btn btn-primary-light btn-wave">Weekly</button> 
                                        <button type="button" class="btn btn-primary-light btn-wave">Monthly</button> 
                                        <button type="button" class="btn btn-primary-light btn-wave">Yearly</button> 
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pb-0">
                                <div id="ordered-statistics"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-5">
                        <div class="card custom-card">
                            <div class="card-header justify-content-between">
                                <div class="card-title">
                                    Machine Escalition Per Line
                                </div>
                                <div class="dropdown"> 
                                    <a href="javascript:void(0);" class="p-2 fs-12 text-muted" data-bs-toggle="dropdown" aria-expanded="false"> Sort By <i class="ri-arrow-down-s-line align-middle ms-1 d-inline-block"></i> </a> 
                                    <ul class="dropdown-menu" role="menu"> 
                                        <li><a class="dropdown-item" href="javascript:void(0);">Daily</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0);">Weekly</a></li> 
                                        <li><a class="dropdown-item" href="javascript:void(0);">Monthly</a></li> 
                                    </ul> 
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row gy-3">
                                    <div class="col-xl-6">
                                        <div class="p-3 bg-light text-default rounded border border-dashed">
                                            <span class="d-block mb-1">Equipment</span>
                                            <h5 class="fw-semibold lh-1 mb-0">14,642<span class="text-success fw-semibold fs-13 ms-2 d-inline-flex align-items-center">0.64%<i class="ri-arrow-up-s-line ms-1"></i></span></h5>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="p-3 bg-light text-default rounded border border-dashed">
                                            <span class="d-block mb-1">Technical</span>
                                            <h5 class="fw-semibold lh-1 mb-0">12,326<span class="text-danger fw-semibold fs-13 ms-2 d-inline-flex align-items-center">5.31%<i class="ri-arrow-down-s-line ms-1"></i></span></h5>
                                        </div>
                                    </div>
                                </div>
                                <div id="visitors-report"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End:: row-1 -->

        <!-- Start:: row-2 -->
        <div class="row">
            <div class="col-xxl-9">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            Ongoing Escalation
                        </div>
                        <div class="d-flex flex-wrap gap-2"> 
                            <div> 
                                <input class="form-control form-control-sm" type="text" placeholder="Search Here" aria-label=".form-control-sm example"> 
                            </div> 
                            <div class="dropdown"> 
                                <a href="javascript:void(0);" class="btn btn-primary btn-sm btn-wave" data-bs-toggle="dropdown" aria-expanded="false"> Sort By<i class="ri-arrow-down-s-line align-middle ms-1 d-inline-block"></i> 
                                </a> 
                                <ul class="dropdown-menu" role="menu"> 
                                    <li><a class="dropdown-item" href="javascript:void(0);">New</a></li> 
                                    <li><a class="dropdown-item" href="javascript:void(0);">Popular</a></li> 
                                    <li><a class="dropdown-item" href="javascript:void(0);">Relevant</a></li> 
                                </ul> 
                            </div> 
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table text-nowrap">
                                <thead>
                                    <tr>
                                        <th scope="row" class="ps-4"><input class="form-check-input" type="checkbox" id="checkboxNoLabeljob1" value="" aria-label="..."></th>
                                        <th scope="col">Category</th>
                                        <th scope="col">Problem Part</th>
                                        <th scope="col">MC No</th>
                                        <th scope="col">Line</th>
                                        <th scope="col">MC Type</th>
                                        <th scope="col">Lot No</th>
                                        <th scope="col">Model</th>
                                        <th scope="col">MC Status</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">LT</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="ps-4"><input class="form-check-input" type="checkbox" id="checkboxNoLabeljob2" value="" aria-label="..."></td>
                                        <td>
                                            <div class="d-flex">
                                                <div class="ms-2">
                                                    <p class="fw-semibold mb-0 d-flex align-items-center"><a href="javascript:void(0);">Control Unit</a></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                        SSD Program
                                        </td>
                                        <td class="text-center">
                                        VI137
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div>
                                                    E
                                                </div>
                                            </div>
                                        </td>
                                        <td>GMC-G1</td>
                                        <td class="fw-semibold">DL16P3C</td>
                                        <td>21A106</td>
                                        <td>Running</td>
                                        <td>2024-05-18</td>
                                        <td>2.5 days</td>
                                        <td>
                                            <span class="badge bg-danger-transparent">Pending</span>
                                        </td>
                                        <td>
                                            <div class="btn-list">
                                                <button class="btn btn-sm btn-icon btn-primary-light btn-wave">
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                                <button class="btn btn-sm btn-icon btn-secondary-light btn-wave">
                                                    <i class="ri-edit-line"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4"><input class="form-check-input" type="checkbox" id="checkboxNoLabeljob2" value="" aria-label="..."></td>
                                        <td>
                                            <div class="d-flex">
                                                <div class="ms-2">
                                                    <p class="fw-semibold mb-0 d-flex align-items-center"><a href="javascript:void(0);">Control Unit</a></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                        SSD Program
                                        </td>
                                        <td class="text-center">
                                        VI137
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div>
                                                    E
                                                </div>
                                            </div>
                                        </td>
                                        <td>GMC-G1</td>
                                        <td class="fw-semibold">DL16P3C</td>
                                        <td>21A106</td>
                                        <td>Running</td>
                                        <td>2024-05-18</td>
                                        <td>2.5 days</td>
                                        <td>
                                            <span class="badge bg-danger-transparent">Pending</span>
                                        </td>
                                        <td>
                                            <div class="btn-list">
                                                <button class="btn btn-sm btn-icon btn-primary-light btn-wave">
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                                <button class="btn btn-sm btn-icon btn-secondary-light btn-wave">
                                                    <i class="ri-edit-line"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4"><input class="form-check-input" type="checkbox" id="checkboxNoLabeljob2" value="" aria-label="..."></td>
                                        <td>
                                            <div class="d-flex">
                                                <div class="ms-2">
                                                    <p class="fw-semibold mb-0 d-flex align-items-center"><a href="javascript:void(0);">Control Unit</a></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                        SSD Program
                                        </td>
                                        <td class="text-center">
                                        VI137
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div>
                                                    E
                                                </div>
                                            </div>
                                        </td>
                                        <td>GMC-G1</td>
                                        <td class="fw-semibold">DL16P3C</td>
                                        <td>21A106</td>
                                        <td>Running</td>
                                        <td>2024-05-18</td>
                                        <td>2.5 days</td>
                                        <td>
                                            <span class="badge bg-danger-transparent">Pending</span>
                                        </td>
                                        <td>
                                            <div class="btn-list">
                                                <button class="btn btn-sm btn-icon btn-primary-light btn-wave">
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                                <button class="btn btn-sm btn-icon btn-secondary-light btn-wave">
                                                    <i class="ri-edit-line"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4"><input class="form-check-input" type="checkbox" id="checkboxNoLabeljob2" value="" aria-label="..."></td>
                                        <td>
                                            <div class="d-flex">
                                                <div class="ms-2">
                                                    <p class="fw-semibold mb-0 d-flex align-items-center"><a href="javascript:void(0);">Control Unit</a></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                        SSD Program
                                        </td>
                                        <td class="text-center">
                                        VI137
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div>
                                                    E
                                                </div>
                                            </div>
                                        </td>
                                        <td>GMC-G1</td>
                                        <td class="fw-semibold">DL16P3C</td>
                                        <td>21A106</td>
                                        <td>Running</td>
                                        <td>2024-05-18</td>
                                        <td>2.5 days</td>
                                        <td>
                                            <span class="badge bg-danger-transparent">Pending</span>
                                        </td>
                                        <td>
                                            <div class="btn-list">
                                                <button class="btn btn-sm btn-icon btn-primary-light btn-wave">
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                                <button class="btn btn-sm btn-icon btn-secondary-light btn-wave">
                                                    <i class="ri-edit-line"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4"><input class="form-check-input" type="checkbox" id="checkboxNoLabeljob2" value="" aria-label="..."></td>
                                        <td>
                                            <div class="d-flex">
                                                <div class="ms-2">
                                                    <p class="fw-semibold mb-0 d-flex align-items-center"><a href="javascript:void(0);">Control Unit</a></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                        SSD Program
                                        </td>
                                        <td class="text-center">
                                        VI137
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div>
                                                    E
                                                </div>
                                            </div>
                                        </td>
                                        <td>GMC-G1</td>
                                        <td class="fw-semibold">DL16P3C</td>
                                        <td>21A106</td>
                                        <td>Running</td>
                                        <td>2024-05-18</td>
                                        <td>2.5 days</td>
                                        <td>
                                            <span class="badge bg-danger-transparent">Pending</span>
                                        </td>
                                        <td>
                                            <div class="btn-list">
                                                <button class="btn btn-sm btn-icon btn-primary-light btn-wave">
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                                <button class="btn btn-sm btn-icon btn-secondary-light btn-wave">
                                                    <i class="ri-edit-line"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4"><input class="form-check-input" type="checkbox" id="checkboxNoLabeljob2" value="" aria-label="..."></td>
                                        <td>
                                            <div class="d-flex">
                                                <div class="ms-2">
                                                    <p class="fw-semibold mb-0 d-flex align-items-center"><a href="javascript:void(0);">Control Unit</a></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                        SSD Program
                                        </td>
                                        <td class="text-center">
                                        VI137
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div>
                                                    E
                                                </div>
                                            </div>
                                        </td>
                                        <td>GMC-G1</td>
                                        <td class="fw-semibold">DL16P3C</td>
                                        <td>21A106</td>
                                        <td>Running</td>
                                        <td>2024-05-18</td>
                                        <td>2.5 days</td>
                                        <td>
                                            <span class="badge bg-danger-transparent">Pending</span>
                                        </td>
                                        <td>
                                            <div class="btn-list">
                                                <button class="btn btn-sm btn-icon btn-primary-light btn-wave">
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                                <button class="btn btn-sm btn-icon btn-secondary-light btn-wave">
                                                    <i class="ri-edit-line"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer"> 
                        <div class="d-flex align-items-center"> 
                            <div> Showing 5 Entries <i class="bi bi-arrow-right ms-2 fw-semibold"></i> </div>
                            <div class="ms-auto"> 
                            <nav aria-label="Page navigation" class="pagination-style-4"> 
                                <ul class="pagination mb-0"> 
                                    <li class="page-item disabled"> <a class="page-link" href="javascript:void(0);"> Prev </a> </li>
                                        <li class="page-item active"><a class="page-link" href="javascript:void(0);">1</a></li> 
                                        <li class="page-item"><a class="page-link" href="javascript:void(0);">2</a></li> 
                                        <li class="page-item"> <a class="page-link text-primary" href="javascript:void(0);"> next </a> </li> 
                                    </ul> 
                                </nav> 
                            </div> 
                        </div> 
                    </div>
                </div>
            </div>
            <div class="col-xxl-3">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            Escalation Per Category
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled visitors-browser-list">
                            <li>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded bg-light p-2 border">
                                            <img src="../assets/images/browsers/chrome.png" alt="">
                                        </span>
                                    </div>
                                    <div class="flex-fill">
                                        <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap">
                                            <span class="d-block fw-semibold d-inline-flex">Inspection Result<span class="text-muted fs-13 ms-1">(Oerkill, etc..)</span></span>
                                            <span class="d-block h6 mb-0 fw-semibold"><span class="text-success me-2 fs-13 fw-medium d-inline-flex"><i class="ti ti-arrow-narrow-up"></i>3.26%</span>13,546</span>
                                        </div>
                                        <div class="progress progress-xs" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width: 70%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded bg-light p-2 border">
                                            <img src="../assets/images/browsers/edge.png" alt="">
                                        </span>
                                    </div>
                                    <div class="flex-fill">
                                        <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap">
                                            <span class="d-block fw-semibold d-inline-flex">Ejector Unit<span class="text-muted fs-13 ms-1 fw-normal">(Bining, etc..)</span></span>
                                            <span class="d-block h6 mb-0 fw-semibold"><span class="text-danger me-2 fs-13 fw-medium d-inline-flex"><i class="ti ti-arrow-narrow-down"></i>0.96%</span>11,322</span>
                                        </div>
                                        <div class="progress progress-xs" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-secondary" style="width: 60%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded bg-light p-2 border">
                                            <img src="../assets/images/browsers/firefox.png" alt="">
                                        </span>
                                    </div>
                                    <div class="flex-fill">
                                        <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap">
                                            <span class="d-block fw-semibold d-inline-flex">Control Unit<span class="text-muted fs-13 ms-1 fw-normal">(Cpu, Motherboard, etc..)</span></span>
                                            <span class="d-block h6 mb-0 fw-semibold"><span class="text-success me-2 fs-13 fw-medium d-inline-flex"><i class="ti ti-arrow-narrow-up"></i>1.64%</span>6,236</span>
                                        </div>
                                        <div class="progress progress-xs" role="progressbar" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: 30%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded bg-light p-2 border">
                                            <img src="../assets/images/browsers/safari.png" alt="">
                                        </span>
                                    </div>
                                    <div class="flex-fill">
                                        <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap">
                                            <span class="d-block fw-semibold d-inline-flex">Inspection Unit<span class="text-muted fs-13 ms-1 fw-normal">(Camera, others, etc..)</span></span>
                                            <span class="d-block h6 mb-0 fw-semibold"><span class="text-success me-2 fs-13 fw-medium d-inline-flex"><i class="ti ti-arrow-narrow-up"></i>6.38%</span>10,235</span>
                                        </div>
                                        <div class="progress progress-xs" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" style="width: 50%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded bg-light p-2 border">
                                            <img src="../assets/images/browsers/uc.png" alt="">
                                        </span>
                                    </div>
                                    <div class="flex-fill">
                                        <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap">
                                            <span class="d-block fw-semibold d-inline-flex">Loading Unit<span class="text-muted fs-13 ms-1 fw-normal">(Pcs per Min.)</span></span>
                                            <span class="d-block h6 mb-0 fw-semibold"><span class="text-success me-2 fs-13 fw-medium d-inline-flex"><i class="ti ti-arrow-narrow-up"></i>5.18%</span>14,965</span>
                                        </div>
                                        <div class="progress progress-xs" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" style="width: 80%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded bg-light p-2 border">
                                            <img src="../assets/images/browsers/opera.png" alt="">
                                        </span>
                                    </div>
                                    <div class="flex-fill">
                                        <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap">
                                            <span class="d-block fw-semibold d-inline-flex">Lot Related<span class="text-muted fs-13 ms-1 fw-normal">(NG%, Retest%, etc..)</span></span>
                                            <span class="d-block h6 mb-0 fw-semibold"><span class="text-danger me-2 fs-13 fw-medium d-inline-flex"><i class="ti ti-arrow-narrow-down"></i>1.65%</span>8,432</span>
                                        </div>
                                        <div class="progress progress-xs" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" style="width: 40%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <div>
                                        <span class="avatar avatar-md avatar-rounded bg-light p-2 border">
                                            <img src="../assets/images/browsers/samsung-internet.png" alt="">
                                        </span>
                                    </div>
                                    <div class="flex-fill">
                                        <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap">
                                            <span class="d-block fw-semibold d-inline-flex">Accessories<span class="text-muted fs-13 ms-1 fw-normal">(KB,M, Monitor)</span></span>
                                            <span class="d-block h6 mb-0 fw-semibold"><span class="text-success me-2 fs-13 fw-medium d-inline-flex"><i class="ti ti-arrow-narrow-up"></i>0.99%</span>4,134</span>
                                        </div>
                                        <div class="progress progress-xs" role="progressbar" aria-valuenow="36" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-orange" style="width: 36%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- End:: row-2 -->
         
    </div>
</div> 
<!-- End::app-content -->
@endsection