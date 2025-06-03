/**
 * Pure JavaScript WIP Modal Handler
 * Handles WIP data processing without Livewire dependencies
 * Designed for offline local network environments
 */

(function() {
    'use strict';

    // DOM elements
    let processWipBtn, processWipDirectBtn, wipDataTextarea, wipDataHidden, wipDataHiddenAlt;
    let wipDirectForm, processWipText, processWipLoading, wipAlertContainer;
    let wipValidateBtn, wipProgressContainer, wipProgressBar, wipProgressText;
    let wipDataPreview, wipPreviewHeader, wipPreviewBody, wipTotalRows;

    // Configuration
    const CONFIG = {
        API_ENDPOINT: '/api/process-wip-data',
        MAX_PREVIEW_ROWS: 5,
        // Expected column names (exact match from your data)
        EXPECTED_COLUMNS: [
            'no', 'site', 'facility', 'major_process', 'sub_process', 'lot_status',
            'lot_id', 'model_id', 'lot_qty', 'chip_size', 'work_type', 'hold_yn',
            'tat_days', 'location', 'lot_details', 'routing_name', 'production_team',
            'chip_type', 'special_code', 'powder_type', 'work_equip', 'rack', 'facility_2'
        ],
        // Key required columns for basic validation
        REQUIRED_COLUMNS: ['lot_id', 'model_id', 'lot_qty', 'chip_size', 'work_type'],
        MIN_COLUMNS: 20 // Minimum number of columns expected (you have 23)
    };

    /**
     * Initialize the WIP modal functionality
     */
    function init() {
        // Get DOM elements
        processWipBtn = document.getElementById('process-wip-btn');
        processWipDirectBtn = document.getElementById('process-wip-direct-btn');
        wipDataTextarea = document.getElementById('wip-data-textarea');
        wipDataHidden = document.getElementById('wip-data-hidden');
        wipDataHiddenAlt = document.getElementById('wip-data-hidden-alt');
        wipDirectForm = document.getElementById('wip-direct-form');
        processWipText = document.getElementById('process-wip-text');
        processWipLoading = document.getElementById('process-wip-loading');
        wipAlertContainer = document.getElementById('wip-alert-container');
        wipValidateBtn = document.getElementById('wip-validate-btn');
        wipProgressContainer = document.getElementById('wip-progress-container');
        wipProgressBar = document.getElementById('wip-progress-bar');
        wipProgressText = document.getElementById('wip-progress-text');
        wipDataPreview = document.getElementById('wip-data-preview');
        wipPreviewHeader = document.getElementById('wip-preview-header');
        wipPreviewBody = document.getElementById('wip-preview-body');
        wipTotalRows = document.getElementById('wip-total-rows');

        // Add event listeners
        if (processWipBtn) {
            processWipBtn.addEventListener('click', handleProcessWip);
        }

        if (processWipDirectBtn) {
            processWipDirectBtn.addEventListener('click', handleDirectSubmit);
        }

        if (wipValidateBtn) {
            wipValidateBtn.addEventListener('click', handleValidateData);
        }

        if (wipDataTextarea) {
            wipDataTextarea.addEventListener('input', handleTextareaInput);
        }

        // Reset modal when it's closed
        const modal = document.getElementById('updateWipModal');
        if (modal) {
            modal.addEventListener('hidden.bs.modal', resetModal);
        }

        console.log('WIP Modal initialized successfully');
    }

    /**
     * Handle textarea input changes
     */
    function handleTextareaInput() {
        // Hide preview and alerts when user types
        hideDataPreview();
        clearAlerts();
        hideDirectSubmitButton();
    }

    /**
     * Handle data validation
     */
    function handleValidateData() {
        const wipData = wipDataTextarea.value.trim();

        if (!wipData) {
            showAlert('error', 'No data provided. Please paste WIP data into the textarea.');
            return;
        }

        try {
            const validationResult = validateWipData(wipData);

            if (validationResult.isValid) {
                let message = `Data validation successful! Found ${validationResult.dataRows} data rows with ${validationResult.columns.length} columns.`;

                // Add warnings if any
                if (validationResult.warnings && validationResult.warnings.length > 0) {
                    message += `<br><small class="text-warning">Warnings: ${validationResult.warnings.join(', ')}</small>`;
                }

                showAlert('success', message);
                showDataPreview(validationResult);
            } else {
                showAlert('error', `Data validation failed: ${validationResult.error}`);
                hideDataPreview();
            }
        } catch (error) {
            showAlert('error', `Validation error: ${error.message}`);
            hideDataPreview();
        }
    }

    /**
     * Validate WIP data format and structure
     */
    function validateWipData(wipData) {
        const rows = wipData.split('\n').map(row => row.trim()).filter(row => row.length > 0);

        if (rows.length < 2) {
            return {
                isValid: false,
                error: 'Data must contain at least a header row and one data row.'
            };
        }

        // Detect delimiter (tab or other)
        const headerRow = rows[0];
        let delimiter = '\t';
        if (!headerRow.includes('\t')) {
            // Try other common delimiters
            if (headerRow.includes('|')) delimiter = '|';
            else if (headerRow.includes(';')) delimiter = ';';
            else if (headerRow.includes(',')) delimiter = ',';
            else delimiter = /\s{2,}/; // Multiple spaces (2 or more)
        }

        // Parse header - keep original column names for display
        const originalColumns = headerRow.split(delimiter).map(col => col.trim());
        const normalizedColumns = originalColumns.map(col => col.toLowerCase().replace(/[^a-z0-9_]/g, ''));

        if (originalColumns.length < CONFIG.MIN_COLUMNS) {
            return {
                isValid: false,
                error: `Insufficient columns found. Expected at least ${CONFIG.MIN_COLUMNS} columns, found ${originalColumns.length}.`
            };
        }

        // Check for required columns
        const missingColumns = [];
        CONFIG.REQUIRED_COLUMNS.forEach(required => {
            const found = normalizedColumns.includes(required) ||
                         originalColumns.some(col => col.toLowerCase() === required);
            if (!found) {
                missingColumns.push(required);
            }
        });

        if (missingColumns.length > 0) {
            return {
                isValid: false,
                error: `Missing required columns: ${missingColumns.join(', ')}`
            };
        }

        // Check if columns match expected format
        let warnings = [];
        const unexpectedColumns = normalizedColumns.filter(col =>
            !CONFIG.EXPECTED_COLUMNS.includes(col) && col.length > 0
        );

        if (unexpectedColumns.length > 0) {
            warnings.push(`Unexpected columns found: ${unexpectedColumns.join(', ')}`);
        }

        // Parse a few data rows for preview
        const dataRows = rows.slice(1, Math.min(6, rows.length));
        const parsedRows = dataRows.map(row => {
            const cells = row.split(delimiter).map(cell => cell.trim());
            // Ensure we have the same number of cells as headers
            while (cells.length < originalColumns.length) {
                cells.push('');
            }
            return cells.slice(0, originalColumns.length);
        });

        return {
            isValid: true,
            columns: originalColumns,
            normalizedColumns: normalizedColumns,
            dataRows: rows.length - 1,
            previewRows: parsedRows,
            delimiter: delimiter,
            warnings: warnings
        };
    }

    /**
     * Show data preview
     */
    function showDataPreview(validationResult) {
        // Clear previous preview
        wipPreviewHeader.innerHTML = '';
        wipPreviewBody.innerHTML = '';

        // Calculate column width based on number of columns
        const columnCount = validationResult.columns.length;
        const columnWidth = Math.max(60, Math.min(120, 800 / columnCount));

        // Create header
        const headerRow = document.createElement('tr');
        validationResult.columns.forEach((col, index) => {
            const th = document.createElement('th');
            th.textContent = col.replace(/_/g, ' ').toUpperCase();
            th.style.width = `${columnWidth}px`;
            th.style.minWidth = `${columnWidth}px`;
            th.style.maxWidth = `${columnWidth}px`;
            th.style.fontSize = '0.65rem';
            th.style.padding = '0.2rem';
            th.style.position = 'sticky';
            th.style.top = '0';
            th.style.backgroundColor = '#f8f9fa';
            th.style.zIndex = '10';
            th.style.borderBottom = '2px solid #dee2e6';
            th.style.whiteSpace = 'nowrap';
            th.style.overflow = 'hidden';
            th.style.textOverflow = 'ellipsis';
            th.title = col.replace(/_/g, ' ').toUpperCase();
            headerRow.appendChild(th);
        });
        wipPreviewHeader.appendChild(headerRow);

        // Create preview rows
        validationResult.previewRows.forEach(row => {
            const tr = document.createElement('tr');
            row.forEach((cell, index) => {
                const td = document.createElement('td');
                td.textContent = cell || '';
                td.style.width = `${columnWidth}px`;
                td.style.minWidth = `${columnWidth}px`;
                td.style.maxWidth = `${columnWidth}px`;
                td.style.whiteSpace = 'nowrap';
                td.style.overflow = 'hidden';
                td.style.textOverflow = 'ellipsis';
                td.style.fontSize = '0.65rem';
                td.style.padding = '0.2rem';
                td.title = cell || ''; // Show full text on hover
                tr.appendChild(td);
            });
            wipPreviewBody.appendChild(tr);
        });

        // Update total rows count
        wipTotalRows.textContent = validationResult.dataRows;

        // Show preview
        wipDataPreview.style.display = 'block';
    }

    /**
     * Hide data preview
     */
    function hideDataPreview() {
        if (wipDataPreview) {
            wipDataPreview.style.display = 'none';
        }
    }

    /**
     * Handle WIP data processing
     */
    function handleProcessWip() {
        const wipData = wipDataTextarea.value.trim();

        if (!wipData) {
            showAlert('error', 'No data provided. Please paste WIP data into the textarea.');
            return;
        }

        // Validate data first
        try {
            const validationResult = validateWipData(wipData);
            if (!validationResult.isValid) {
                showAlert('error', `Data validation failed: ${validationResult.error}`);
                return;
            }
        } catch (error) {
            showAlert('error', `Validation error: ${error.message}`);
            return;
        }

        // Show loading state
        setLoadingState(true);
        showProgress(0, 'Preparing data...');

        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (!csrfToken) {
            showAlert('error', 'CSRF token not found. Please refresh the page and try again.');
            setLoadingState(false);
            return;
        }

        // Prepare request
        const requestBody = {
            wipData: wipData,
            wip_data: wipData
        };

        console.log('Sending WIP data:', wipData.substring(0, 100) + '...');

        // Send data to server
        fetch(CONFIG.API_ENDPOINT, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(requestBody)
        })
        .then(response => {
            showProgress(50, 'Processing response...');
            return response.json();
        })
        .then(data => {
            setLoadingState(false);
            hideProgress();

            if (data.success) {
                showAlert('success', `WIP data processed successfully! ${data.saved_count || 0} records imported.`);
                wipDataTextarea.value = '';
                hideDataPreview();

                // Refresh dashboard after delay
                setTimeout(() => {
                    showLoadingOverlay();
                    window.location.reload();
                }, 2000);
            } else {
                handleProcessingError(data);
            }
        })
        .catch(error => {
            setLoadingState(false);
            hideProgress();
            console.error('WIP processing error:', error);
            
            showAlert('error', `Network error occurred: ${error.message}. You can try the direct submit option.`);
            showDirectSubmitButton();
        });
    }

    /**
     * Handle processing errors
     */
    function handleProcessingError(data) {
        let errorMessage = data.message || 'An error occurred while processing the WIP data.';

        if (data.errors && data.errors.length > 0) {
            errorMessage += '<br><strong>Details:</strong><ul>';
            data.errors.forEach(error => {
                errorMessage += `<li>Row ${error.index + 1}: ${error.message}</li>`;
            });
            errorMessage += '</ul>';
        }

        showAlert('error', errorMessage);
        showDirectSubmitButton();
    }

    /**
     * Handle direct form submission (fallback)
     */
    function handleDirectSubmit() {
        const wipData = wipDataTextarea.value.trim();

        if (!wipData) {
            showAlert('error', 'No data provided. Please paste WIP data into the textarea.');
            return;
        }

        // Set hidden input values
        wipDataHidden.value = wipData;
        wipDataHiddenAlt.value = wipData;

        // Submit form
        wipDirectForm.submit();
    }

    /**
     * Set loading state
     */
    function setLoadingState(isLoading) {
        if (processWipBtn) {
            processWipBtn.disabled = isLoading;
        }
        
        if (processWipText && processWipLoading) {
            processWipText.style.display = isLoading ? 'none' : 'inline';
            processWipLoading.style.display = isLoading ? 'inline-block' : 'none';
        }
    }

    /**
     * Show progress
     */
    function showProgress(percentage, text) {
        if (wipProgressContainer) {
            wipProgressContainer.style.display = 'block';
            wipProgressBar.style.width = percentage + '%';
            wipProgressText.textContent = text;
        }
    }

    /**
     * Hide progress
     */
    function hideProgress() {
        if (wipProgressContainer) {
            wipProgressContainer.style.display = 'none';
        }
    }

    /**
     * Show direct submit button
     */
    function showDirectSubmitButton() {
        if (processWipDirectBtn) {
            processWipDirectBtn.style.display = 'inline-block';
        }
    }

    /**
     * Hide direct submit button
     */
    function hideDirectSubmitButton() {
        if (processWipDirectBtn) {
            processWipDirectBtn.style.display = 'none';
        }
    }

    /**
     * Show alert message
     */
    function showAlert(type, message) {
        if (!wipAlertContainer) return;

        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const iconClass = type === 'success' ? 'ri-check-line' : 'ri-error-warning-line';

        wipAlertContainer.innerHTML = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="${iconClass} me-1"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
    }

    /**
     * Clear alerts
     */
    function clearAlerts() {
        if (wipAlertContainer) {
            wipAlertContainer.innerHTML = '';
        }
    }

    /**
     * Reset modal to initial state
     */
    function resetModal() {
        if (wipDataTextarea) {
            wipDataTextarea.value = '';
        }
        clearAlerts();
        hideDataPreview();
        hideProgress();
        hideDirectSubmitButton();
        setLoadingState(false);
    }

    /**
     * Show loading overlay (if available)
     */
    function showLoadingOverlay() {
        if (typeof window.showLoadingOverlay === 'function') {
            window.showLoadingOverlay();
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Also initialize when Livewire updates DOM (if Livewire is present)
    if (typeof window.Livewire !== 'undefined') {
        document.addEventListener('livewire:initialized', init);
        document.addEventListener('livewire:load', init);
    }

})();
