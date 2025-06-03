<!-- Pure JavaScript Lot List Modal -->
<div class="modal fade" id="lotListModal" tabindex="-1" aria-labelledby="lotListModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="lotListModalLabel">
                    <span id="lot-list-title">Endtime Lots</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Alert container for displaying messages -->
                <div id="lot-list-alert-container"></div>

                <!-- Filter Controls in a single row -->
                <div class="row g-3 mb-4 align-items-end">
                    <!-- Line Filter -->
                    <div class="col-md-2">
                        <label for="lineFilter" class="form-label">Line</label>
                        <select class="form-select form-select-sm" id="lineFilter">
                            <option value="ALL">All Lines</option>
                        </select>
                    </div>

                    <!-- Work Type Filter -->
                    <div class="col-md-2">
                        <label for="workTypeFilter" class="form-label">Work Type</label>
                        <select class="form-select form-select-sm" id="workTypeFilter">
                            <option value="ALL">All Types</option>
                        </select>
                    </div>

                    <!-- Lot Type Filter -->
                    <div class="col-md-2">
                        <label for="lotTypeFilter" class="form-label">Lot Type</label>
                        <select class="form-select form-select-sm" id="lotTypeFilter">
                            <option value="ALL">All Types</option>
                        </select>
                    </div>

                    <!-- Size Filter -->
                    <div class="col-md-2">
                        <label for="sizeFilter" class="form-label">Chip Size</label>
                        <select class="form-select form-select-sm" id="sizeFilter">
                            <option value="ALL">All Sizes</option>
                        </select>
                    </div>

                    <!-- Search -->
                    <div class="col-md-3">
                        <label for="searchQuery" class="form-label">Search</label>
                        <input type="text" class="form-control form-control-sm" id="searchQuery" placeholder="Search lots...">
                    </div>

                    <!-- Filter Button -->
                    <div class="col-md-1">
                        <button type="button" class="btn btn-primary btn-sm w-100" id="applyFiltersBtn">
                            <i class="ri-search-line"></i>
                        </button>
                    </div>
                </div>

                <!-- Loading indicator -->
                <div id="lot-list-loading" class="text-center py-4" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading lots...</p>
                </div>

                <!-- Lot List Table -->
                <div id="lot-list-table-container" class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th class="sortable" data-field="lot_id">
                                    Lot ID <i class="ri-arrow-up-down-line"></i>
                                </th>
                                <th class="sortable" data-field="model_id">
                                    Model ID <i class="ri-arrow-up-down-line"></i>
                                </th>
                                <th class="sortable" data-field="lot_qty">
                                    Qty <i class="ri-arrow-up-down-line"></i>
                                </th>
                                <th class="sortable" data-field="line">
                                    Line <i class="ri-arrow-up-down-line"></i>
                                </th>
                                <th class="sortable" data-field="area">
                                    Area <i class="ri-arrow-up-down-line"></i>
                                </th>
                                <th class="sortable" data-field="mc_no">
                                    MC No <i class="ri-arrow-up-down-line"></i>
                                </th>
                                <th class="sortable" data-field="chip_size">
                                    Size <i class="ri-arrow-up-down-line"></i>
                                </th>
                                <th class="sortable" data-field="work_type">
                                    Work Type <i class="ri-arrow-up-down-line"></i>
                                </th>
                                <th class="sortable" data-field="lot_type">
                                    Lot Type <i class="ri-arrow-up-down-line"></i>
                                </th>
                                <th class="sortable" data-field="status">
                                    Status <i class="ri-arrow-up-down-line"></i>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="lot-list-tbody">
                            <!-- Lot data will be populated here -->
                        </tbody>
                    </table>
                </div>

                <!-- No data message -->
                <div id="lot-list-no-data" class="text-center py-4" style="display: none;">
                    <i class="ri-inbox-line fs-1 text-muted"></i>
                    <p class="text-muted mt-2">No lots found matching your criteria.</p>
                </div>

                <!-- Pagination -->
                <div id="lot-list-pagination" class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        <small id="pagination-info">Showing 0 to 0 of 0 entries</small>
                    </div>
                    <nav aria-label="Lot list pagination">
                        <ul class="pagination pagination-sm mb-0" id="pagination-controls">
                            <!-- Pagination controls will be populated here -->
                        </ul>
                    </nav>
                </div>
            </div>
            <div class="modal-footer">
                <div class="d-flex justify-content-between w-100">
                    <div class="text-muted">
                        <small id="lot-count-summary">Total: 0 lots</small>
                    </div>
                    <div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="refreshLotListBtn">
                            <i class="ri-refresh-line me-1"></i>Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include the Lot List Modal JavaScript -->
<script src="{{ asset('js/lot-list-modal.js') }}"></script>
