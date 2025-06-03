<div>
    <div class="card custom-card">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div class="card-title">{{ $cutoffDisplay }} Submitted</div>
                <div wire:loading class="spinner-border spinner-border-sm text-primary ms-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-cutoff mb-0 text-nowrap" id="submitted-per-line-table">
                    <thead>
                        <tr class="fw-bold bg-primary-light text-primary">
                            <th class="fw-bold bg-primary-light text-primary sortable-header" data-column="0" data-type="text" style="cursor: pointer;">
                                Line <i class="ri-arrow-up-down-line sort-icon ms-1" style="opacity: 0.3;"></i>
                            </th>
                            <th class="fw-bold bg-primary-light text-primary sortable-header" data-column="1" data-type="number" style="cursor: pointer;">
                                Tgt <i class="ri-arrow-up-down-line sort-icon ms-1" style="opacity: 0.3;"></i>
                            </th>
                            <th class="cutoff-border-start bg-primary-light text-primary sortable-header" data-column="2" data-type="number" style="cursor: pointer;">
                                Res <i class="ri-arrow-up-down-line sort-icon ms-1" style="opacity: 0.3;"></i>
                            </th>
                            <th class="fw-bold bg-primary-light text-primary sortable-header" data-column="3" data-type="percentage" style="cursor: pointer;">
                                % <i class="ri-arrow-up-down-line sort-icon ms-1" style="opacity: 0.3;"></i>
                            </th>
                            <th class="cutoff-border-start bg-primary-light text-primary sortable-header" data-column="4" data-type="number" style="cursor: pointer;">
                                Short <i class="ri-arrow-up-down-line sort-icon ms-1" style="opacity: 0.3;"></i>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $lineCodes = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'VMI'];
                        @endphp

                        @foreach($lineCodes as $index => $lineCode)
                        <tr class="hover-row">
                            <td>{{ $lineCode }}</td>
                            <td>{{ number_format(($targetData[$index] ?? 0) / 1000000, 2) }}M</td>
                            <td class="cutoff-border-start">{{ number_format(($submittedData[$index] ?? 0) / 1000000, 2) }}M</td>
                            <td class="{{ ($percentages[$index] ?? 0) >= 100 ? 'text-success' : (($percentages[$index] ?? 0) >= 90 ? 'text-secondary' : 'text-danger') }}">
                                {{ $percentages[$index] ?? 0 }}%
                            </td>
                            <td class="cutoff-border-start text-danger">{{ number_format(($shortages[$index] ?? 0) / 1000000, 2) }}M</td>
                        </tr>
                        @endforeach

                        @php
                            $totalTarget = array_sum($targetData);
                            $totalSubmitted = array_sum($submittedData);
                            $totalShortage = array_sum($shortages);
                            $totalPercentage = $totalTarget > 0 ? round(($totalSubmitted / $totalTarget) * 100, 1) : 0;
                        @endphp
                        <tr class="hover-row fw-bold">
                            <td>Total</td>
                            <td>{{ number_format($totalTarget / 1000000, 2) }}M</td>
                            <td class="cutoff-border-start">{{ number_format($totalSubmitted / 1000000, 2) }}M</td>
                            <td class="{{ $totalPercentage >= 100 ? 'text-success' : ($totalPercentage >= 90 ? 'text-secondary' : 'text-danger') }}">
                                {{ $totalPercentage }}%
                            </td>
                            <td class="cutoff-border-start text-danger">{{ number_format($totalShortage / 1000000, 2) }}M</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        .hover-row:hover {
            background-color: var(--bs-secondary-bg) !important;
        }
        .cutoff-border-start {
            border-left: 1px solid #6c757d !important;
        }
        .cutoff-border-end {
            border-right: 1px solid #6c757d !important;
        }
        .table-cutoff {
            border-collapse: separate;
            border-spacing: 0;
        }
        .table-cutoff th, .table-cutoff td {
            border: 1px solid #495057;
        }

        /* Sort functionality styles */
        .sortable-header:hover {
            background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
        }
        .sort-icon {
            transition: opacity 0.2s ease;
        }
        .sortable-header:hover .sort-icon {
            opacity: 0.7 !important;
        }
        .sort-icon.active {
            opacity: 1 !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initializeTableSorting();
        });

        // Re-initialize sorting after Livewire updates
        document.addEventListener('livewire:navigated', function() {
            initializeTableSorting();
        });

        function initializeTableSorting() {
            const table = document.getElementById('submitted-per-line-table');
            if (!table) return;

            const headers = table.querySelectorAll('.sortable-header');
            let currentSort = { column: -1, direction: 'asc' };

            headers.forEach(header => {
                header.addEventListener('click', function() {
                    const column = parseInt(this.dataset.column);
                    const type = this.dataset.type;

                    // Determine sort direction
                    if (currentSort.column === column) {
                        currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
                    } else {
                        currentSort.direction = 'asc';
                    }
                    currentSort.column = column;

                    // Update sort icons
                    updateSortIcons(headers, column, currentSort.direction);

                    // Sort the table
                    sortTable(table, column, type, currentSort.direction);
                });
            });
        }

        function updateSortIcons(headers, activeColumn, direction) {
            headers.forEach((header, index) => {
                const icon = header.querySelector('.sort-icon');
                const columnIndex = parseInt(header.dataset.column);

                if (columnIndex === activeColumn) {
                    icon.className = direction === 'asc' ? 'ri-arrow-up-line sort-icon ms-1 active' : 'ri-arrow-down-line sort-icon ms-1 active';
                    icon.style.opacity = '1';
                } else {
                    icon.className = 'ri-arrow-up-down-line sort-icon ms-1';
                    icon.style.opacity = '0.3';
                }
            });
        }

        function sortTable(table, column, type, direction) {
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));

            // Separate the total row (last row) from data rows
            const totalRow = rows[rows.length - 1];
            const dataRows = rows.slice(0, -1);

            // Sort data rows
            dataRows.sort((a, b) => {
                const aCell = a.cells[column];
                const bCell = b.cells[column];

                let aValue = getCellValue(aCell, type);
                let bValue = getCellValue(bCell, type);

                let comparison = 0;
                if (type === 'text') {
                    comparison = aValue.localeCompare(bValue);
                } else {
                    comparison = aValue - bValue;
                }

                return direction === 'asc' ? comparison : -comparison;
            });

            // Clear tbody and re-append sorted rows + total row
            tbody.innerHTML = '';
            dataRows.forEach(row => tbody.appendChild(row));
            tbody.appendChild(totalRow);
        }

        function getCellValue(cell, type) {
            const text = cell.textContent.trim();

            switch (type) {
                case 'number':
                    // Extract number from text like "123.45M"
                    const numberMatch = text.match(/[\d,]+\.?\d*/);
                    return numberMatch ? parseFloat(numberMatch[0].replace(/,/g, '')) : 0;

                case 'percentage':
                    // Extract number from text like "95.5%"
                    const percentMatch = text.match(/[\d.]+/);
                    return percentMatch ? parseFloat(percentMatch[0]) : 0;

                case 'text':
                default:
                    return text;
            }
        }
    </script>
</div>
