<div>
    <!-- Export Button with Date Range Picker -->
    <div class="dropdown export-dropdown" id="exportDropdown">
        <button class="btn btn-danger-light dropdown-toggle" type="button" id="exportDropdownButton" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="ri-file-download-line align-middle me-1"></i> Export
        </button>
        <ul class="dropdown-menu p-3" style="min-width: 300px;">
            <li onclick="event.stopPropagation();">
                <h6 class="dropdown-header">Export Data</h6>
                <form wire:submit.prevent="export">
                    <div class="mb-3">
                        <label for="export-start-date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="export-start-date" wire:model="startDate">
                    </div>
                    <div class="mb-3">
                        <label for="export-end-date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="export-end-date" wire:model="endDate">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Export Format</label>
                        <div class="btn-group w-100 export-format-group" role="group">
                            <input type="radio" class="btn-check export-format-radio" name="exportFormat" id="exportExcel" wire:model="exportFormat" value="excel" checked>
                            <label class="btn btn-outline-primary export-format-label" for="exportExcel" onclick="event.stopPropagation();">CSV</label>

                            <input type="radio" class="btn-check export-format-radio" name="exportFormat" id="exportCSV" wire:model="exportFormat" value="csv">
                            <label class="btn btn-outline-primary export-format-label" for="exportCSV" onclick="event.stopPropagation();">Excel</label>

                            <input type="radio" class="btn-check export-format-radio" name="exportFormat" id="exportPDF" wire:model="exportFormat" value="pdf">
                            <label class="btn btn-outline-primary export-format-label" for="exportPDF" onclick="event.stopPropagation();">PDF</label>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-danger" id="exportDataButton" wire:loading.attr="disabled" wire:target="export">
                            <span wire:loading.remove wire:target="export">
                                <i class="ri-file-download-line me-1"></i> Export Data
                            </span>
                            <span wire:loading wire:target="export">
                                <i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i>
                                Exporting...
                            </span>
                        </button>
                    </div>
                </form>
            </li>
        </ul>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get the dropdown element
            const dropdown = document.getElementById('exportDropdown');

            // Add click event listener to the entire dropdown menu to prevent closing
            dropdown.addEventListener('click', function(e) {
                // Only prevent propagation if the click is not on the Export Data button
                if (!e.target.closest('#exportDataButton')) {
                    e.stopPropagation();
                }
            });

            // Add click event listener to the Export Data button to close the dropdown
            const exportButton = document.getElementById('exportDataButton');
            if (exportButton) {
                exportButton.addEventListener('click', function() {
                    // Close the dropdown after clicking Export Data
                    const dropdownInstance = bootstrap.Dropdown.getInstance(document.getElementById('exportDropdownButton'));
                    if (dropdownInstance) {
                        dropdownInstance.hide();
                    }
                });
            }

            // Listen for the triggerDownload event
            Livewire.on('triggerDownload', params => {
                if (params.url) {
                    // Create a hidden link and trigger the download
                    const link = document.createElement('a');
                    link.href = params.url;
                    link.download = '';
                    link.style.display = 'none';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            });
        });

        // Make sure the event listeners are added when Livewire updates the DOM
        document.addEventListener('livewire:initialized', function() {
            // Get the dropdown element
            const dropdown = document.getElementById('exportDropdown');

            // Add click event listener to the entire dropdown menu to prevent closing
            dropdown.addEventListener('click', function(e) {
                // Only prevent propagation if the click is not on the Export Data button
                if (!e.target.closest('#exportDataButton')) {
                    e.stopPropagation();
                }
            });

            // Add click event listener to the Export Data button to close the dropdown
            const exportButton = document.getElementById('exportDataButton');
            if (exportButton) {
                exportButton.addEventListener('click', function() {
                    // Close the dropdown after clicking Export Data
                    const dropdownInstance = bootstrap.Dropdown.getInstance(document.getElementById('exportDropdownButton'));
                    if (dropdownInstance) {
                        dropdownInstance.hide();
                    }
                });
            }
        });
    </script>
</div>
