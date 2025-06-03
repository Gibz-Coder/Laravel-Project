/**
 * Pure JavaScript Lot List Modal Handler
 * Handles lot list display and filtering without Livewire dependencies
 * Designed for offline local network environments
 */

(function() {
    'use strict';

    // DOM elements
    let modal, modalTitle, alertContainer, loadingIndicator, tableContainer, tbody;
    let noDataMessage, paginationInfo, paginationControls, lotCountSummary;
    let lineFilter, workTypeFilter, lotTypeFilter, sizeFilter, searchQuery;
    let applyFiltersBtn, refreshBtn;

    // State
    let currentParams = {};
    let currentPage = 1;
    let currentSortField = 'lot_id';
    let currentSortDirection = 'asc';
    let isLoading = false;

    // Configuration
    const CONFIG = {
        API_ENDPOINT: '/api/lot-list',
        FILTER_OPTIONS_ENDPOINT: '/api/lot-list/filter-options',
        PER_PAGE: 15,
        DEBOUNCE_DELAY: 500
    };

    /**
     * Initialize the lot list modal functionality
     */
    function init() {
        // Get DOM elements
        modal = document.getElementById('lotListModal');
        modalTitle = document.getElementById('lot-list-title');
        alertContainer = document.getElementById('lot-list-alert-container');
        loadingIndicator = document.getElementById('lot-list-loading');
        tableContainer = document.getElementById('lot-list-table-container');
        tbody = document.getElementById('lot-list-tbody');
        noDataMessage = document.getElementById('lot-list-no-data');
        paginationInfo = document.getElementById('pagination-info');
        paginationControls = document.getElementById('pagination-controls');
        lotCountSummary = document.getElementById('lot-count-summary');
        
        lineFilter = document.getElementById('lineFilter');
        workTypeFilter = document.getElementById('workTypeFilter');
        lotTypeFilter = document.getElementById('lotTypeFilter');
        sizeFilter = document.getElementById('sizeFilter');
        searchQuery = document.getElementById('searchQuery');
        applyFiltersBtn = document.getElementById('applyFiltersBtn');
        refreshBtn = document.getElementById('refreshLotListBtn');

        if (!modal) {
            console.warn('Lot list modal not found');
            return;
        }

        // Add event listeners
        setupEventListeners();

        console.log('Lot List Modal initialized successfully');
    }

    /**
     * Setup event listeners
     */
    function setupEventListeners() {
        // Filter controls
        if (applyFiltersBtn) {
            applyFiltersBtn.addEventListener('click', applyFilters);
        }

        if (refreshBtn) {
            refreshBtn.addEventListener('click', refreshLotList);
        }

        // Search input with debounce
        if (searchQuery) {
            let searchTimeout;
            searchQuery.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(applyFilters, CONFIG.DEBOUNCE_DELAY);
            });
        }

        // Filter dropdowns
        [lineFilter, workTypeFilter, lotTypeFilter, sizeFilter].forEach(filter => {
            if (filter) {
                filter.addEventListener('change', applyFilters);
            }
        });

        // Sortable headers
        document.querySelectorAll('.sortable').forEach(header => {
            header.addEventListener('click', function() {
                const field = this.dataset.field;
                handleSort(field);
            });
            header.style.cursor = 'pointer';
        });

        // Modal events
        if (modal) {
            modal.addEventListener('hidden.bs.modal', resetModal);
        }
    }

    /**
     * Show lot list modal with parameters
     */
    function showLotList(params) {
        currentParams = {
            date: params.date || new Date().toISOString().split('T')[0],
            cutoff: params.cutoff || 'all',
            worktype: params.worktype || 'all',
            lottype: params.lottype || 'all',
            type: params.type || 'all',
            line: params.line || null,
            title: params.title || 'Endtime Lots'
        };

        // Set modal title
        if (modalTitle) {
            modalTitle.textContent = currentParams.title;
        }

        // Reset state
        currentPage = 1;
        currentSortField = 'lot_id';
        currentSortDirection = 'asc';

        // Load filter options first, then load data
        loadFilterOptions().then(() => {
            loadLotList();
        });

        // Show modal
        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();
    }

    /**
     * Load filter options
     */
    async function loadFilterOptions() {
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            const response = await fetch(CONFIG.FILTER_OPTIONS_ENDPOINT, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    date: currentParams.date,
                    cutoff: currentParams.cutoff
                })
            });

            const data = await response.json();

            if (data.success) {
                populateFilterOptions(data.filter_options);
            } else {
                console.error('Failed to load filter options:', data.message);
                showAlert('error', 'Failed to load filter options: ' + data.message);
            }
        } catch (error) {
            console.error('Error loading filter options:', error);
            showAlert('error', 'Error loading filter options: ' + error.message);
        }
    }

    /**
     * Populate filter dropdown options
     */
    function populateFilterOptions(options) {
        // Populate line filter
        if (lineFilter && options.lines) {
            lineFilter.innerHTML = '';
            options.lines.forEach(line => {
                const option = document.createElement('option');
                option.value = line;
                option.textContent = line === 'ALL' ? 'All Lines' : line;
                lineFilter.appendChild(option);
            });
            
            // Set initial value if specified
            if (currentParams.line && currentParams.line !== 'ALL') {
                lineFilter.value = currentParams.line;
            }
        }

        // Populate work type filter
        if (workTypeFilter && options.work_types) {
            workTypeFilter.innerHTML = '';
            options.work_types.forEach(type => {
                const option = document.createElement('option');
                option.value = type;
                option.textContent = type === 'ALL' ? 'All Types' : type;
                workTypeFilter.appendChild(option);
            });
        }

        // Populate lot type filter
        if (lotTypeFilter && options.lot_types) {
            lotTypeFilter.innerHTML = '';
            options.lot_types.forEach(type => {
                const option = document.createElement('option');
                option.value = type;
                option.textContent = type === 'ALL' ? 'All Types' : type;
                lotTypeFilter.appendChild(option);
            });
        }

        // Populate size filter
        if (sizeFilter && options.sizes) {
            sizeFilter.innerHTML = '';
            options.sizes.forEach(size => {
                const option = document.createElement('option');
                option.value = size;
                option.textContent = size === 'ALL' ? 'All Sizes' : size;
                sizeFilter.appendChild(option);
            });
        }
    }

    /**
     * Load lot list data
     */
    async function loadLotList() {
        if (isLoading) return;

        setLoadingState(true);
        clearAlerts();

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            const requestData = {
                ...currentParams,
                page: currentPage,
                per_page: CONFIG.PER_PAGE,
                sort_field: currentSortField,
                sort_direction: currentSortDirection,
                search_query: searchQuery?.value || '',
                selected_line: lineFilter?.value || 'ALL',
                selected_worktype: workTypeFilter?.value || 'ALL',
                selected_lottype: lotTypeFilter?.value || 'ALL',
                selected_size: sizeFilter?.value || 'ALL'
            };

            const response = await fetch(CONFIG.API_ENDPOINT, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(requestData)
            });

            const data = await response.json();

            if (data.success) {
                displayLotList(data.data);
                updatePagination(data.pagination);
                updateLotCount(data.pagination.total);
            } else {
                showAlert('error', 'Failed to load lot list: ' + data.message);
                showNoData();
            }
        } catch (error) {
            console.error('Error loading lot list:', error);
            showAlert('error', 'Error loading lot list: ' + error.message);
            showNoData();
        } finally {
            setLoadingState(false);
        }
    }

    /**
     * Display lot list in table
     */
    function displayLotList(lots) {
        if (!tbody) return;

        tbody.innerHTML = '';

        if (lots.length === 0) {
            showNoData();
            return;
        }

        lots.forEach(lot => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${escapeHtml(lot.lot_id || '')}</td>
                <td>${escapeHtml(lot.model_id || '')}</td>
                <td class="text-end">${formatNumber(lot.lot_qty || 0)}</td>
                <td>${escapeHtml(lot.line || '')}</td>
                <td>${escapeHtml(lot.area || '')}</td>
                <td>${escapeHtml(lot.mc_no || '')}</td>
                <td>${escapeHtml(lot.chip_size || '')}</td>
                <td>${escapeHtml(lot.work_type || '')}</td>
                <td>${escapeHtml(lot.lot_type || '')}</td>
                <td>
                    <span class="badge bg-${getStatusColor(lot.status)}">${escapeHtml(lot.status || 'Unknown')}</span>
                </td>
            `;
            tbody.appendChild(row);
        });

        showTable();
    }

    /**
     * Get status badge color
     */
    function getStatusColor(status) {
        switch (status?.toLowerCase()) {
            case 'submitted': return 'success';
            case 'pending': return 'warning';
            case 'processing': return 'info';
            case 'completed': return 'primary';
            case 'failed': return 'danger';
            default: return 'secondary';
        }
    }

    /**
     * Update pagination controls
     */
    function updatePagination(pagination) {
        if (!paginationInfo || !paginationControls) return;

        // Update pagination info
        paginationInfo.textContent = `Showing ${pagination.from} to ${pagination.to} of ${pagination.total} entries`;

        // Update pagination controls
        paginationControls.innerHTML = '';

        // Previous button
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${!pagination.has_prev_page ? 'disabled' : ''}`;
        prevLi.innerHTML = `<a class="page-link" href="#" data-page="${pagination.current_page - 1}">Previous</a>`;
        paginationControls.appendChild(prevLi);

        // Page numbers
        const startPage = Math.max(1, pagination.current_page - 2);
        const endPage = Math.min(pagination.total_pages, pagination.current_page + 2);

        for (let i = startPage; i <= endPage; i++) {
            const pageLi = document.createElement('li');
            pageLi.className = `page-item ${i === pagination.current_page ? 'active' : ''}`;
            pageLi.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
            paginationControls.appendChild(pageLi);
        }

        // Next button
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${!pagination.has_next_page ? 'disabled' : ''}`;
        nextLi.innerHTML = `<a class="page-link" href="#" data-page="${pagination.current_page + 1}">Next</a>`;
        paginationControls.appendChild(nextLi);

        // Add click handlers
        paginationControls.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = parseInt(this.dataset.page);
                if (page && page !== currentPage && page >= 1 && page <= pagination.total_pages) {
                    currentPage = page;
                    loadLotList();
                }
            });
        });
    }

    /**
     * Update lot count summary
     */
    function updateLotCount(total) {
        if (lotCountSummary) {
            lotCountSummary.textContent = `Total: ${formatNumber(total)} lots`;
        }
    }

    /**
     * Handle sorting
     */
    function handleSort(field) {
        if (currentSortField === field) {
            currentSortDirection = currentSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            currentSortField = field;
            currentSortDirection = 'asc';
        }

        // Update sort indicators
        updateSortIndicators();

        // Reset to first page and reload
        currentPage = 1;
        loadLotList();
    }

    /**
     * Update sort indicators in headers
     */
    function updateSortIndicators() {
        document.querySelectorAll('.sortable').forEach(header => {
            const icon = header.querySelector('i');
            if (header.dataset.field === currentSortField) {
                icon.className = currentSortDirection === 'asc' ? 'ri-arrow-up-line' : 'ri-arrow-down-line';
            } else {
                icon.className = 'ri-arrow-up-down-line';
            }
        });
    }

    /**
     * Apply filters
     */
    function applyFilters() {
        currentPage = 1;
        loadLotList();
    }

    /**
     * Refresh lot list
     */
    function refreshLotList() {
        loadLotList();
    }

    /**
     * Set loading state
     */
    function setLoadingState(loading) {
        isLoading = loading;
        
        if (loadingIndicator) {
            loadingIndicator.style.display = loading ? 'block' : 'none';
        }
        
        if (tableContainer) {
            tableContainer.style.display = loading ? 'none' : 'block';
        }
        
        if (noDataMessage) {
            noDataMessage.style.display = 'none';
        }
    }

    /**
     * Show table
     */
    function showTable() {
        if (tableContainer) tableContainer.style.display = 'block';
        if (noDataMessage) noDataMessage.style.display = 'none';
    }

    /**
     * Show no data message
     */
    function showNoData() {
        if (tableContainer) tableContainer.style.display = 'none';
        if (noDataMessage) noDataMessage.style.display = 'block';
        updateLotCount(0);
    }

    /**
     * Show alert message
     */
    function showAlert(type, message) {
        if (!alertContainer) return;

        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const iconClass = type === 'success' ? 'ri-check-line' : 'ri-error-warning-line';

        alertContainer.innerHTML = `
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
        if (alertContainer) {
            alertContainer.innerHTML = '';
        }
    }

    /**
     * Reset modal to initial state
     */
    function resetModal() {
        clearAlerts();
        currentPage = 1;
        currentSortField = 'lot_id';
        currentSortDirection = 'asc';
        
        if (searchQuery) searchQuery.value = '';
        if (tbody) tbody.innerHTML = '';
        
        setLoadingState(false);
        showNoData();
    }

    /**
     * Utility functions
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatNumber(num) {
        return new Intl.NumberFormat().format(num);
    }

    // Expose showLotList function globally
    window.showLotList = showLotList;

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
