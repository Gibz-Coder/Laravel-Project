<!-- Pure JavaScript WIP Modal -->
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

                <!-- Form for WIP data -->
                <div class="mb-3">
                    <label for="wip-data-textarea" class="form-label">WIP Data</label>
                    <textarea 
                        id="wip-data-textarea" 
                        class="form-control" 
                        rows="15" 
                        style="font-family: monospace; min-height: 250px; resize: vertical;" 
                        placeholder="Paste your raw WIP data here including the header row..."></textarea>
                </div>

                <!-- Progress bar -->
                <div id="wip-progress-container" class="mb-3" style="display: none;">
                    <div class="progress">
                        <div id="wip-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                    </div>
                    <small id="wip-progress-text" class="text-muted">Processing...</small>
                </div>

                <!-- Data preview section -->
                <div id="wip-data-preview" class="mb-3" style="display: none;">
                    <h6 class="fw-bold">Data Preview:</h6>
                    <div style="height: 150px; width: 100%; overflow: auto; border: 1px solid #dee2e6; border-radius: 0.375rem; background: white;">
                        <table class="table table-sm table-bordered mb-0" style="font-size: 0.7rem; table-layout: fixed; width: 100%; min-width: 800px;">
                            <thead id="wip-preview-header" class="table-light"></thead>
                            <tbody id="wip-preview-body"></tbody>
                        </table>
                    </div>
                    <small class="text-muted mt-2 d-block">Showing first 5 rows. Total rows: <span id="wip-total-rows">0</span></small>
                </div>

                <!-- Hidden form for fallback submission -->
                <form id="wip-direct-form" action="/api/process-wip-data" method="POST" style="display: none;">
                    @csrf
                    <input type="hidden" name="wipData" id="wip-data-hidden">
                    <input type="hidden" name="wip_data" id="wip-data-hidden-alt">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="wip-validate-btn" class="btn btn-info">
                    <i class="ri-search-line me-1"></i>Validate Data
                </button>
                <button type="button" id="process-wip-btn" class="btn btn-primary">
                    <span id="process-wip-text">
                        <i class="ri-upload-cloud-line me-1"></i>Update WIP
                    </span>
                    <span id="process-wip-loading" style="display: none;">
                        <i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i>
                        Processing...
                    </span>
                </button>
                <!-- Direct form submit button as fallback -->
                <button type="button" id="process-wip-direct-btn" class="btn btn-warning" style="display: none;">
                    <i class="ri-send-plane-line me-1"></i>Try Direct Submit
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Include the WIP Modal JavaScript -->
<script src="{{ asset('js/wip-modal.js') }}"></script>
