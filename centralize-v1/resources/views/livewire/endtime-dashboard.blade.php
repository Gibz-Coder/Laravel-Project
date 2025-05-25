<div>
    <!-- Page Loading Overlay -->
    <div id="page-loading-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
        <div style="text-align: center; color: white;">
            <img src="{{ asset('images/banners/loader.svg') }}" alt="Loading...">
            <p class="mt-3 fw-bold">Loading Dashboard...</p>
        </div>
    </div>

    <!-- Start::app-content -->
    <div class="main-content app-content">
        <!-- Start::page-header -->
        <div class="d-flex align-items-center justify-content-between page-header-breadcrumb flex-wrap gap-2">
            <div class="d-flex align-items-center my-4 gap-2 flex-wrap">
                <!-- Auto Refresh Toggle - with JavaScript implementation -->
                <div class="custom-toggle-switch d-flex align-items-center mb-0">
                    <input id="toggleswitchSuccess" name="toggleswitch001" type="checkbox"
                           {{ $autoRefresh ? 'checked' : '' }}>
                    <label for="toggleswitchSuccess" class="label-success"></label>
                    <span class="ms-3" id="autoRefreshStatus">Auto {{ $autoRefresh ? 'ON' : 'OFF' }}</span>
                </div>

                <!-- Manual Refresh Button -->
                <div class="div">
                    <button class="btn btn-success-light btn shadow-sm btn-wave" onclick="window.location.reload()">
                        <i class="ri-refresh-line me-1"></i>Reload Page
                    </button>
                </div>

                <!-- Date Picker with Manila timezone -->
                <div class="form-group">
                    <div style="display: inline-block; position: relative;">
                        <button class="btn btn-primary-light btn-wave me-2 waves-effect waves-light" onclick="document.getElementById('date-picker-input').showPicker()" style="cursor: pointer;">
                            <i class="ri-calendar-event-line align-middle"></i>
                            <span>{{ $displayDate }}</span>
                        </button>
                        <form id="date-form" action="{{ route('endtime') }}" method="GET">
                            <input type="hidden" name="cutoff" value="{{ $cutoff }}">
                            <input type="hidden" name="worktype" value="{{ $worktype }}">
                            <input type="hidden" name="lottype" value="{{ $lottype }}">
                            <input type="date" id="date-picker-input" name="date" value="{{ $date }}" onchange="document.getElementById('date-form').submit(); document.getElementById('page-loading-overlay').style.display = 'flex';" style="width: 0; height: 0; padding: 0; border: 0; position: absolute; visibility: hidden;">
                        </form>
                    </div>
                </div>

                <!-- Worktype Dropdown - Default set to Normal -->
                <div class="dropdown">
                    <button class="btn btn-info-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span>{{ $worktype == 'all' ? 'Worktype - all' : $worktype }}</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('endtime', ['worktype' => 'all', 'date' => $date, 'cutoff' => $cutoff, 'lottype' => $lottype]) }}">Worktype - all</a></li>
                        <li><a class="dropdown-item" href="{{ route('endtime', ['worktype' => 'Normal', 'date' => $date, 'cutoff' => $cutoff, 'lottype' => $lottype]) }}">Normal</a></li>
                        <li><a class="dropdown-item" href="{{ route('endtime', ['worktype' => 'Process Rework', 'date' => $date, 'cutoff' => $cutoff, 'lottype' => $lottype]) }}">Process Rework</a></li>
                        <li><a class="dropdown-item" href="{{ route('endtime', ['worktype' => 'Warehouse', 'date' => $date, 'cutoff' => $cutoff, 'lottype' => $lottype]) }}">Warehouse</a></li>
                        <li><a class="dropdown-item" href="{{ route('endtime', ['worktype' => 'Outgoing NG', 'date' => $date, 'cutoff' => $cutoff, 'lottype' => $lottype]) }}">Outgoing NG</a></li>
                    </ul>
                </div>

                <!-- Lottype Dropdown - Default set to MAIN -->
                <div class="dropdown">
                    <button class="btn btn-info-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span>{{ $lottype == 'all' ? 'Lottype - all' : $lottype }}</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('endtime', ['lottype' => 'all', 'date' => $date, 'cutoff' => $cutoff, 'worktype' => $worktype]) }}">Lottype - all</a></li>
                        <li><a class="dropdown-item" href="{{ route('endtime', ['lottype' => 'MAIN', 'date' => $date, 'cutoff' => $cutoff, 'worktype' => $worktype]) }}">MAIN</a></li>
                        <li><a class="dropdown-item" href="{{ route('endtime', ['lottype' => 'RL', 'date' => $date, 'cutoff' => $cutoff, 'worktype' => $worktype]) }}">RL</a></li>
                        <li><a class="dropdown-item" href="{{ route('endtime', ['lottype' => 'LY', 'date' => $date, 'cutoff' => $cutoff, 'worktype' => $worktype]) }}">LY</a></li>
                        <li><a class="dropdown-item" href="{{ route('endtime', ['lottype' => 'ADV', 'date' => $date, 'cutoff' => $cutoff, 'worktype' => $worktype]) }}">ADV</a></li>
                    </ul>
                </div>

                <!-- Cutoff Selection Buttons - Using Regular Links -->
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <div class="btn-group btn-group" role="group" aria-label="cutoffSelection">
                        <a href="{{ route('endtime', ['cutoff' => '00:00~04:00', 'date' => $date, 'worktype' => $worktype, 'lottype' => $lottype]) }}" class="btn {{ $cutoff == '00:00~04:00' ? 'btn-primary' : 'btn-primary-light' }} btn-wave" data-time="00:00~04:00">4 AM</a>
                        <a href="{{ route('endtime', ['cutoff' => '04:00~07:00', 'date' => $date, 'worktype' => $worktype, 'lottype' => $lottype]) }}" class="btn {{ $cutoff == '04:00~07:00' ? 'btn-primary' : 'btn-primary-light' }} btn-wave" data-time="04:00~07:00">7 AM</a>
                        <a href="{{ route('endtime', ['cutoff' => '07:00~12:00', 'date' => $date, 'worktype' => $worktype, 'lottype' => $lottype]) }}" class="btn {{ $cutoff == '07:00~12:00' ? 'btn-primary' : 'btn-primary-light' }} btn-wave" data-time="07:00~12:00">12 NN</a>
                        <a href="{{ route('endtime', ['cutoff' => '12:00~16:00', 'date' => $date, 'worktype' => $worktype, 'lottype' => $lottype]) }}" class="btn {{ $cutoff == '12:00~16:00' ? 'btn-primary' : 'btn-primary-light' }} btn-wave" data-time="12:00~16:00">4 PM</a>
                        <a href="{{ route('endtime', ['cutoff' => '16:00~19:00', 'date' => $date, 'worktype' => $worktype, 'lottype' => $lottype]) }}" class="btn {{ $cutoff == '16:00~19:00' ? 'btn-primary' : 'btn-primary-light' }} btn-wave" data-time="16:00~19:00">7 PM</a>
                        <a href="{{ route('endtime', ['cutoff' => '19:00~00:00', 'date' => $date, 'worktype' => $worktype, 'lottype' => $lottype]) }}" class="btn {{ $cutoff == '19:00~00:00' ? 'btn-primary' : 'btn-primary-light' }} btn-wave" data-time="19:00~00:00">12 MN</a>
                        <a href="{{ route('endtime', ['cutoff' => 'day', 'date' => $date, 'worktype' => $worktype, 'lottype' => $lottype]) }}" class="btn {{ $cutoff == 'day' ? 'btn-primary' : 'btn-primary-light' }} btn-wave" data-time="day">Day</a>
                        <a href="{{ route('endtime', ['cutoff' => 'night', 'date' => $date, 'worktype' => $worktype, 'lottype' => $lottype]) }}" class="btn {{ $cutoff == 'night' ? 'btn-primary' : 'btn-primary-light' }} btn-wave" data-time="night">Night</a>
                        <a href="{{ route('endtime', ['cutoff' => 'all', 'date' => $date, 'worktype' => $worktype, 'lottype' => $lottype]) }}" class="btn {{ $cutoff == 'all' ? 'btn-primary' : 'btn-primary-light' }} btn-wave" data-time="all">All</a>
                    </div>
                </div>

                <!-- Add Endtime Button -->
                <div class="div">
                    <button class="btn btn-primary btn-wave me-2" data-bs-toggle="modal" data-bs-target="#addEndtimeModal">
                        <i class="ri-add-line me-1"></i>Add Endtime
                    </button>
                </div>

                <!-- Add Submitted Lot Button -->
                <div class="div">
                    <button class="btn btn-success btn-wave me-2" data-bs-toggle="modal" data-bs-target="#addSubmittedLotModal">
                        <i class="ri-add-line me-1"></i>Add Submitted
                    </button>
                </div>

                <!-- Update WIP Modal Button -->
                <div class="div">
                    <button class="btn btn-warning-light btn-wave me-3" data-bs-toggle="modal" data-bs-target="#updateWipModal">Update wip!</button>
                    <!-- Update WIP Modal -->
                    <div class="modal fade" id="updateWipModal" tabindex="-1" aria-labelledby="updateWipModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="updateWipModalLabel">Update WIP Data</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Alert container for displaying messages -->
                                    <div id="wip-alert-container"></div>

                                    <!-- Instructions for WIP data format -->
                                    <div class="alert alert-info mb-3">
                                        <h6 class="alert-heading fw-bold"><i class="ri-information-line me-1"></i> Instructions:</h6>
                                        <p class="mb-1">1. <a href="{{ asset('files/update_wip.xlsx') }}" class="text-primary fw-bold" download><i class="ri-file-excel-2-line me-1"></i>Download this Excel file</a></p>
                                        <p class="mb-1">2. Copy raw data from MES (40281) Grid (simple) and paste it into the Excel file</p>
                                        <p class="mb-1">3. Copy data from Excel columns including the header row</p>
                                        <p class="mb-1">4. Paste the data into the text area below</p>
                                        <p class="mb-1">5. Click "Update WIP" to process the data</p>
                                        <p class="mb-0"><strong>Note:</strong> This will replace ALL existing WIP data in the system.</p>
                                    </div>

                                    <!-- Data format example -->
                                    <!-- <div class="alert alert-light mb-3">
                                        <h6 class="alert-heading fw-bold"><i class="ri-file-list-line me-1"></i> Expected Data Format:</h6>
                                        <p class="mb-0 text-muted">Required columns: no, site, facility, major_process, sub_process, lot_status, lot_id, model_id, lot_qty, lot_size (maps to chip_size), work_type, hold_yn, tat_days, location, lot_details, routing_name, production_team_type, chip_type, special_code, powder_type, work_equip, rack, facility_2</p>
                                    </div> -->

                                    <!-- Sample data format -->
                                    <!-- <div class="alert alert-secondary mb-3">
                                        <h6 class="alert-heading fw-bold"><i class="ri-file-code-line me-1"></i> Sample Data Format:</h6>
                                        <p class="mb-1 text-muted" style="font-family: monospace; font-size: 0.8rem; white-space: nowrap; overflow-x: auto;">
                                            no&nbsp;&nbsp;site&nbsp;&nbsp;facility&nbsp;&nbsp;major_process&nbsp;&nbsp;sub_process&nbsp;&nbsp;lot_status&nbsp;&nbsp;lot_id&nbsp;&nbsp;model_id&nbsp;&nbsp;lot_qty&nbsp;&nbsp;lot_size&nbsp;&nbsp;work_type&nbsp;&nbsp;hold_yn&nbsp;&nbsp;tat_days&nbsp;&nbsp;location&nbsp;&nbsp;lot_details&nbsp;&nbsp;routing_name&nbsp;&nbsp;production_team_type&nbsp;&nbsp;chip_type&nbsp;&nbsp;special_code&nbsp;&nbsp;powder_type&nbsp;&nbsp;work_equip&nbsp;&nbsp;rack&nbsp;&nbsp;facility_2<br>
                                            1&nbsp;&nbsp;SEMPHIL&nbsp;&nbsp;SEMPHIL Production#5&nbsp;&nbsp;Visual&nbsp;&nbsp;Receive in Visual&nbsp;&nbsp;Wait&nbsp;&nbsp;AKCBP4Y&nbsp;&nbsp;CL21A226MAYNNNB&nbsp;&nbsp;958648&nbsp;&nbsp;21&nbsp;&nbsp;Normal&nbsp;&nbsp;N&nbsp;&nbsp;0&nbsp;&nbsp;SO_OST_02&nbsp;&nbsp;MP&nbsp;&nbsp;Visual_Normal-Newlot_1st Insp.&nbsp;&nbsp;IT team&nbsp;&nbsp;R : In-house chip ( R )&nbsp;&nbsp;A,R,SSBT-02(NB342AT01)&nbsp;&nbsp;A,R,SSBT-02(NB342AT01)&nbsp;&nbsp;SO_OST_02&nbsp;&nbsp;SO_OST_02&nbsp;&nbsp;SO_OST_02
                                        </p>
                                    </div> -->

                                    <!-- Form for WIP data with both JavaScript and direct form submission -->
                                    <div class="mb-3">
                                        <textarea id="wip-data-textarea" class="form-control" rows="15" style="font-family: monospace;min-height: 300px;" placeholder="Paste your raw WIP data here including the header row..."></textarea>
                                    </div>

                                    <!-- Hidden form for direct submission as fallback -->
                                    <form id="wip-direct-form" action="/api/process-wip-data" method="POST" style="display: none;">
                                        @csrf
                                        <input type="hidden" name="wipData" id="wip-data-hidden">
                                        <input type="hidden" name="wip_data" id="wip-data-hidden-alt">
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" id="process-wip-btn" class="btn btn-primary">
                                        <span id="process-wip-text">Update wip!</span>
                                        <span id="process-wip-loading" style="display: none;">
                                            <i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i>
                                            Processing...
                                        </span>
                                    </button>
                                    <!-- Direct form submit button as fallback -->
                                    <button type="button" id="process-wip-direct-btn" class="btn btn-warning" style="display: none;">
                                        <span>Try Direct Submit</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Export Button with Date Range Picker -->
                @livewire('endtime-dashboard.export-date-range')
            </div>
        </div>

        <!-- Auto-refresh polling is now handled by JavaScript -->
        <!-- End::page-header -->

        <!-- Start:: row-1 -->
        <div class="row">
            <div class="col-xxl-9">
                <div class="row">
                    <div class="col-xl-3">
                        @livewire('endtime-dashboard.target-card')
                    </div>
                    <div class="col-xl-3">
                        @livewire('endtime-dashboard.endtime-card')
                    </div>
                    <div class="col-xl-3">
                        @livewire('endtime-dashboard.submitted-card')
                    </div>
                    <div class="col-xl-3">
                        @livewire('endtime-dashboard.remaining-card')
                    </div>
                    <div class="col-xl-3">
                        @livewire('endtime-dashboard.performance-stats')
                    </div>
                    <div class="col-xl-9">
                        @livewire('endtime-dashboard.line-performance-chart')
                    </div>
                </div>
            </div>
            <div class="col-xxl-3">
                @livewire('endtime-dashboard.progress-chart')
            </div>
        </div>
        <!-- End:: row-1 -->

        <!-- Start:: row-2 -->
        <div class="row">
            <div class="col-xxl-8">
                @livewire('endtime-dashboard.line-achievement-table')
            </div>
            <div class="col-xxl-4">
                @livewire('endtime-dashboard.size-achievement-table')
            </div>
        </div>
        <!-- End:: row-2 -->

        <!-- Start:: row-3 -->
        <div class="row">
            <div class="col-xxl-3">
                @livewire('endtime-dashboard.submitted-per-line-table')
            </div>
            <div class="col-xxl-9">
                @livewire('endtime-dashboard.submitted-per-cutoff-table')
            </div>
        </div>
        <!-- End:: row-3 -->
    </div>
    <!-- End::app-content -->

    <!-- Modals -->
    @livewire('endtime-dashboard.lot-list-modal')

    <!-- Add Endtime Modal -->
    <div class="modal fade" id="addEndtimeModal" tabindex="-1" aria-labelledby="addEndtimeModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEndtimeModalLabel">Add Endtime Forecasted Lots</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Display session messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Multiple Endtime Entries Form -->
                    <style>
                        /* Custom styles for the Add Multiple Endtime Entries modal */
                        .compact-table tr {
                            line-height: 0.9; /* Reduced line height */
                        }
                        .compact-table td, .compact-table th {
                            padding: 0.3rem 0.4rem; /* Reduced padding */
                        }
                        .lottype-select {
                            min-width: 120px;
                        }
                        .model-id-input {
                            min-width: 150px; /* Wider Model ID field */
                        }
                        .area-input {
                            max-width: 80px; /* Narrower Area field */
                        }
                        /* Fix for dropdown menus */
                        .dropdown-menu {
                            z-index: 1050 !important;
                        }
                        .table-responsive {
                            overflow: visible !important;
                        }
                        /* Style for active dropdown item */
                        .dropdown-item.active {
                            background-color: var(--primary-rgb, #6366f1);
                            color: #fff;
                            font-weight: bold;
                        }
                        /* Style for dropdown items */
                        .date-cutoff-options .dropdown-item {
                            padding: 0.5rem 1rem;
                            border-bottom: 1px solid rgba(0,0,0,0.05);
                        }
                        .date-cutoff-options .dropdown-item:last-child {
                            border-bottom: none;
                        }
                        /* Loading state for input fields */
                        .is-loading {
                            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'%3E%3Cpath fill='%236366f1' d='M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z' opacity='.25'/%3E%3Cpath fill='%236366f1' d='M12,4a8,8,0,0,1,7.89,6.7A1.53,1.53,0,0,0,21.38,12h0a1.5,1.5,0,0,0,1.48-1.75,11,11,0,0,0-21.72,0A1.5,1.5,0,0,0,2.62,12h0a1.53,1.53,0,0,0,1.49-1.3A8,8,0,0,1,12,4Z'%3E%3CanimateTransform attributeName='transform' dur='0.75s' repeatCount='indefinite' type='rotate' values='0 12 12;360 12 12'/%3E%3C/animateTransform%3E%3C/path%3E%3C/svg%3E");
                            background-repeat: no-repeat;
                            background-position: right 0.5rem center;
                            background-size: 1.25rem;
                        }
                    </style>
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle compact-table">
                            <thead>
                                <tr>
                                    <th>Lot No</th>
                                    <th>MC No</th>
                                    <th>Date & Cutoff</th>
                                    <th>Lot Type</th>
                                    <th>Model ID</th>
                                    <th>Qty</th>
                                    <th>Area</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="endtime-entries">
                                <tr class="endtime-entry">
                                    <td>
                                        <input type="text" class="form-control form-control-sm lot-no-input" placeholder="Lot No">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" placeholder="MC No">
                                    </td>
                                    <td>
                                        <div class="dropdown date-cutoff-dropdown">
                                            <button class="btn btn-outline-light btn-sm dropdown-toggle w-100 text-start" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                {{ date('Y-m-d') }} | 12NN
                                            </button>
                                            <ul class="dropdown-menu w-100 date-cutoff-options">
                                                <!-- Dropdown items will be populated by JavaScript -->
                                            </ul>
                                        </div>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm lottype-select">
                                            <option value="MAIN">MAIN</option>
                                            <option value="RL">RL</option>
                                            <option value="LY">LY</option>
                                            <option value="ADV">ADV</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm model-id-input" placeholder="Model ID" readonly>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm qty-input" placeholder="Qty" readonly>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm area-input" placeholder="Area" readonly>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm delete-entry">Delete</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-2">
                        <button type="button" class="btn btn-success btn-sm" id="add-endtime-entry">
                            <i class="ri-add-line me-1"></i>Add Another Lot
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save Endtime</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Submitted Lot Modal -->
    <div class="modal fade" id="addSubmittedLotModal" tabindex="-1" aria-labelledby="addSubmittedLotModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSubmittedLotModalLabel">Add Submitted Lots</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Display session messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Multiple Submitted Lot Entries Form -->
                    <style>
                        /* Custom styles for the Add Multiple Endtime Entries modal */
                        .compact-table tr {
                            line-height: 0.9; /* Reduced line height */
                        }
                        .compact-table td, .compact-table th {
                            padding: 0.3rem 0.4rem; /* Reduced padding */
                        }
                        .lottype-select {
                            min-width: 120px;
                        }
                        .model-id-input {
                            min-width: 150px; /* Wider Model ID field */
                        }
                        .area-input {
                            max-width: 80px; /* Narrower Area field */
                        }
                        .mc-no-input {
                            max-width: 120px; /* Reduced MC No field to match Date & Cutoff */
                        }
                        .date-cutoff-container {
                            width: 140px; /* Fixed width for Date & Cutoff container */
                        }
                        /* Fix for dropdown menus */
                        .dropdown-menu {
                            z-index: 1050 !important;
                        }
                        .table-responsive {
                            overflow: visible !important;
                        }
                        /* Style for active dropdown item */
                        .dropdown-item.active {
                            background-color: var(--primary-rgb, #6366f1);
                            color: #fff;
                            font-weight: bold;
                        }
                        /* Style for dropdown items */
                        .date-cutoff-options .dropdown-item {
                            padding: 0.5rem 1rem;
                            border-bottom: 1px solid rgba(0,0,0,0.05);
                        }
                        .date-cutoff-options .dropdown-item:last-child {
                            border-bottom: none;
                        }
                        /* Loading state for input fields */
                        .is-loading {
                            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'%3E%3Cpath fill='%236366f1' d='M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z' opacity='.25'/%3E%3Cpath fill='%236366f1' d='M12,4a8,8,0,0,1,7.89,6.7A1.53,1.53,0,0,0,21.38,12h0a1.5,1.5,0,0,0,1.48-1.75,11,11,0,0,0-21.72,0A1.5,1.5,0,0,0,2.62,12h0a1.53,1.53,0,0,0,1.49-1.3A8,8,0,0,1,12,4Z'%3E%3CanimateTransform attributeName='transform' dur='0.75s' repeatCount='indefinite' type='rotate' values='0 12 12;360 12 12'/%3E%3C/animateTransform%3E%3C/path%3E%3C/svg%3E");
                            background-repeat: no-repeat;
                            background-position: right 0.5rem center;
                            background-size: 1.25rem;
                        }
                    </style>
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle compact-table">
                            <thead>
                                <tr>
                                    <th>Lot No</th>
                                    <th>MC No</th>
                                    <th>Date & Cutoff</th>
                                    <th>Lot Type</th>
                                    <th>Model ID</th>
                                    <th>Qty</th>
                                    <th>Area</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="submitted-lot-entries">
                                <tr class="submitted-lot-entry">
                                    <td>
                                        <input type="text" class="form-control form-control-sm lot-no-input" placeholder="Lot No">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm mc-no-input" placeholder="MC No">
                                    </td>
                                    <td>
                                        <div class="dropdown date-cutoff-dropdown">
                                            <button class="btn btn-outline-light btn-sm dropdown-toggle w-100 text-start" type="button" disabled>
                                                {{ date('Y-m-d') }} | <span class="current-cutoff-display">12NN</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm lottype-select">
                                            <option value="MAIN">MAIN</option>
                                            <option value="RL">RL</option>
                                            <option value="LY">LY</option>
                                            <option value="ADV">ADV</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm model-id-input" placeholder="Model ID" readonly>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm qty-input" placeholder="Qty" readonly>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm area-input" placeholder="Area" readonly>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm delete-entry">Delete</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-2">
                        <button type="button" class="btn btn-success btn-sm" id="add-submitted-lot-entry">
                            <i class="ri-add-line me-1"></i>Add Another Lot
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save Submitted</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for handling modal events, page loading, and auto-refresh -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Listen for the Livewire event to close the modal
            window.addEventListener('closeModal', event => {
                const modalId = event.detail.modalId;
                const modalElement = document.getElementById(modalId);
                if (modalElement) {
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                }
            });

            // Show loading overlay when page is about to reload
            const showLoadingOverlay = () => {
                const overlay = document.getElementById('page-loading-overlay');
                if (overlay) {
                    overlay.style.display = 'flex';
                }
            };

            // Add event listeners for page reload
            const reloadButton = document.querySelector('button.btn-success-light');
            if (reloadButton) {
                reloadButton.addEventListener('click', showLoadingOverlay);
            }

            // Add event listeners for filter changes
            const filterLinks = document.querySelectorAll('a[href*="endtime"]');
            filterLinks.forEach(link => {
                link.addEventListener('click', showLoadingOverlay);
            });

            // Auto-refresh functionality
            let autoRefreshTimer = null;
            const refreshInterval = 300000; // 5 minutes in milliseconds

            // Function to start auto-refresh timer
            const startAutoRefresh = () => {
                // Clear any existing timer
                if (autoRefreshTimer) {
                    clearInterval(autoRefreshTimer);
                }

                // Set new timer
                autoRefreshTimer = setInterval(() => {
                    console.log('Auto-refreshing page...');
                    showLoadingOverlay();
                    window.location.reload();
                }, refreshInterval);

                console.log('Auto-refresh enabled - will refresh every 5 minutes');
            };

            // Function to stop auto-refresh timer
            const stopAutoRefresh = () => {
                if (autoRefreshTimer) {
                    clearInterval(autoRefreshTimer);
                    autoRefreshTimer = null;
                    console.log('Auto-refresh disabled');
                }
            };

            // Initialize auto-refresh based on URL parameter
            const autoRefreshToggle = document.getElementById('toggleswitchSuccess');
            const autoRefreshStatus = document.getElementById('autoRefreshStatus');

            // Get auto-refresh state from URL
            const urlParams = new URLSearchParams(window.location.search);
            const autoRefreshParam = urlParams.get('autoRefresh');

            // Ensure toggle state matches URL parameter
            if (autoRefreshToggle) {
                // If autoRefresh=0 is explicitly set in URL, make sure toggle is OFF
                if (autoRefreshParam === '0') {
                    autoRefreshToggle.checked = false;
                    if (autoRefreshStatus) {
                        autoRefreshStatus.textContent = 'Auto OFF';
                    }
                    console.log('Auto-refresh initialized to OFF based on URL parameter');
                }
                // If autoRefresh=1 is explicitly set in URL, make sure toggle is ON
                else if (autoRefreshParam === '1') {
                    autoRefreshToggle.checked = true;
                    if (autoRefreshStatus) {
                        autoRefreshStatus.textContent = 'Auto ON';
                    }
                    startAutoRefresh();
                    console.log('Auto-refresh initialized to ON based on URL parameter');
                }
                // Otherwise, use the checked state from the toggle
                else if (autoRefreshToggle.checked) {
                    startAutoRefresh();
                    console.log('Auto-refresh initialized to ON based on toggle state');
                } else {
                    console.log('Auto-refresh initialized to OFF based on toggle state');
                }
            }

            // Add event listener for auto-refresh toggle
            if (autoRefreshToggle) {
                autoRefreshToggle.addEventListener('change', function() {
                    const isChecked = this.checked;

                    // Update status text
                    if (autoRefreshStatus) {
                        autoRefreshStatus.textContent = `Auto ${isChecked ? 'ON' : 'OFF'}`;
                    }

                    // Handle auto-refresh state
                    if (isChecked) {
                        // Only reload page when turning ON
                        startAutoRefresh();
                        showLoadingOverlay();

                        // Get CSRF token
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                        if (!csrfToken) {
                            console.error('CSRF token not found');
                            showLoadingOverlay();
                            window.location.reload();
                            return;
                        }

                        // Get current cutoff from URL or default to 'all'
                        const urlParams = new URLSearchParams(window.location.search);
                        const currentCutoff = urlParams.get('cutoff') || 'all';

                        // Save state to server using fetch API
                        fetch('/api/save-auto-refresh-state', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                autoRefresh: true,
                                cutoff: currentCutoff
                            })
                        }).then(response => response.json())
                          .then(data => {
                            console.log('Auto-refresh enabled, filters reset to default values:', data);
                            // Reload with the updated filters from the server
                            showLoadingOverlay();
                            window.location.href = `/endtime?date=${data.date}&cutoff=${data.cutoff}&worktype=${data.worktype}&lottype=${data.lottype}&autoRefresh=1`;
                        }).catch(error => {
                            console.error('Error saving auto-refresh state:', error);
                            showLoadingOverlay();
                            window.location.reload();
                        });
                    } else {
                        // Stop the timer when turning OFF
                        stopAutoRefresh();

                        // Get CSRF token
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                        if (!csrfToken) {
                            console.error('CSRF token not found');
                            return;
                        }

                        // Get current URL parameters
                        const urlParams = new URLSearchParams(window.location.search);
                        const currentCutoff = urlParams.get('cutoff') || 'all';
                        const currentDate = urlParams.get('date') || '{{ date("Y-m-d") }}';
                        const currentWorktype = urlParams.get('worktype') || 'all';
                        const currentLottype = urlParams.get('lottype') || 'all';

                        // Save state to server using fetch API
                        fetch('/api/save-auto-refresh-state', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                autoRefresh: false,
                                cutoff: currentCutoff
                            })
                        }).then(response => response.json())
                          .then(data => {
                            console.log('Auto-refresh disabled, keeping current filters:', data);

                            // Reload the page with autoRefresh=0 to ensure URL state matches toggle state
                            showLoadingOverlay();
                            window.location.href = `/endtime?date=${currentDate}&cutoff=${currentCutoff}&worktype=${currentWorktype}&lottype=${currentLottype}&autoRefresh=0`;
                        }).catch(error => {
                            console.error('Error saving auto-refresh state:', error);

                            // Even on error, reload the page to ensure UI state matches
                            showLoadingOverlay();
                            window.location.href = `/endtime?date=${currentDate}&cutoff=${currentCutoff}&worktype=${currentWorktype}&lottype=${currentLottype}&autoRefresh=0`;
                        });
                    }
                });
            }

            // Show loading overlay before unload
            window.addEventListener('beforeunload', function() {
                showLoadingOverlay();
            });

            // WIP Data Processing
            const processWipBtn = document.getElementById('process-wip-btn');
            const processWipDirectBtn = document.getElementById('process-wip-direct-btn');
            const wipDataTextarea = document.getElementById('wip-data-textarea');
            const wipDataHidden = document.getElementById('wip-data-hidden');
            const wipDataHiddenAlt = document.getElementById('wip-data-hidden-alt');
            const wipDirectForm = document.getElementById('wip-direct-form');
            const processWipText = document.getElementById('process-wip-text');
            const processWipLoading = document.getElementById('process-wip-loading');
            const wipAlertContainer = document.getElementById('wip-alert-container');

            // Function to show the direct submit button after a fetch error
            function showDirectSubmitButton() {
                if (processWipDirectBtn) {
                    processWipDirectBtn.style.display = 'inline-block';
                }
            }

            // Function to handle direct form submission
            function handleDirectSubmit() {
                // Get the WIP data from the textarea
                const wipData = wipDataTextarea.value.trim();

                // Validate the data
                if (!wipData) {
                    showWipAlert('error', 'No data provided. Please paste WIP data into the textarea.');
                    return;
                }

                // Set the hidden input values
                wipDataHidden.value = wipData;
                wipDataHiddenAlt.value = wipData;

                // Submit the form
                wipDirectForm.submit();
            }

            // Add event listener for direct submit button
            if (processWipDirectBtn) {
                processWipDirectBtn.addEventListener('click', handleDirectSubmit);
            }

            if (processWipBtn && wipDataTextarea) {
                processWipBtn.addEventListener('click', function() {
                    // Get the WIP data from the textarea
                    const wipData = wipDataTextarea.value.trim();

                    // Validate the data
                    if (!wipData) {
                        showWipAlert('error', 'No data provided. Please paste WIP data into the textarea.');
                        return;
                    }

                    // Show loading state
                    processWipText.style.display = 'none';
                    processWipLoading.style.display = 'inline-block';
                    processWipBtn.disabled = true;

                    // Get CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                    if (!csrfToken) {
                        showWipAlert('error', 'CSRF token not found. Please refresh the page and try again.');
                        resetWipButton();
                        return;
                    }

                    // Log what we're sending for debugging
                    console.log('Sending WIP data:', wipData.substring(0, 100) + '...');

                    // Create the request body with both field names
                    const requestBody = {
                        wipData: wipData,
                        wip_data: wipData
                    };

                    console.log('Request body:', requestBody);

                    // Send the data to the server
                    fetch('/api/process-wip-data', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(requestBody)
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Reset button state
                        resetWipButton();

                        if (data.success) {
                            // Show success message
                            showWipAlert('success', `WIP data processed successfully. ${data.saved_count} records imported.`);

                            // Clear the textarea
                            wipDataTextarea.value = '';

                            // Refresh the dashboard data after a short delay
                            setTimeout(() => {
                                showLoadingOverlay();
                                window.location.reload();
                            }, 2000);
                        } else {
                            // Show error message
                            let errorMessage = data.message || 'An error occurred while processing the WIP data.';

                            // If there are specific errors, show them
                            if (data.errors && data.errors.length > 0) {
                                errorMessage += '<ul>';
                                data.errors.forEach(error => {
                                    errorMessage += `<li>Row ${error.index + 1}: ${error.message}</li>`;
                                });
                                errorMessage += '</ul>';
                            }

                            showWipAlert('error', errorMessage);
                        }
                    })
                    .catch(error => {
                        console.error('Error processing WIP data:', error);
                        resetWipButton();
                        showWipAlert('error', 'An unexpected error occurred. Please try the direct submit method below.');

                        // Show the direct submit button as a fallback
                        showDirectSubmitButton();
                    });
                });
            }

            // Function to show alert in the WIP modal
            function showWipAlert(type, message) {
                if (wipAlertContainer) {
                    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                    const icon = type === 'success' ? 'ri-check-line' : 'ri-error-warning-line';

                    wipAlertContainer.innerHTML = `
                        <div class="alert ${alertClass} alert-dismissible fade show">
                            <i class="${icon} me-2"></i>${message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;

                    // Scroll to the top of the modal
                    wipAlertContainer.scrollIntoView({ behavior: 'smooth' });
                }
            }

            // Function to reset the WIP button state
            function resetWipButton() {
                if (processWipText && processWipLoading && processWipBtn) {
                    processWipText.style.display = 'inline';
                    processWipLoading.style.display = 'none';
                    processWipBtn.disabled = false;
                }
            }
        });
    </script>
</div>