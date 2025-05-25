<div>
    <!-- Lot List Modal -->
    <div class="modal fade" id="lotListModal" tabindex="-1" aria-labelledby="lotListModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="lotListModalLabel">
                        {{ $title ?? 'Endtime Lots' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="close"></button>
                </div>
                <div class="modal-body">
                    <!-- Filter Controls in a single row -->
                    <div class="row g-3 mb-4 align-items-end">
                        <!-- Line Filter -->
                        <div class="col-md-2">
                            <label for="lineFilter" class="form-label">Line</label>
                            <select class="form-select form-select-sm" id="lineFilter" wire:model="selectedLine" wire:change="updateLineFilter($event.target.value)">
                                @foreach($availableLines as $line)
                                    <option value="{{ $line }}">{{ $line }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Work Type Filter -->
                        <div class="col-md-2">
                            <label for="workTypeFilter" class="form-label">WorkType</label>
                            <select class="form-select form-select-sm" id="workTypeFilter" wire:model="selectedWorkType" wire:change="updateWorkTypeFilter($event.target.value)">
                                @foreach($availableWorkTypes as $workType)
                                    <option value="{{ $workType }}">{{ $workType }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Lot Type Filter -->
                        <div class="col-md-2">
                            <label for="lotTypeFilter" class="form-label">LotType</label>
                            <select class="form-select form-select-sm" id="lotTypeFilter" wire:model="selectedLotType" wire:change="updateLotTypeFilter($event.target.value)">
                                @foreach($availableLotTypes as $lotType)
                                    <option value="{{ $lotType }}">{{ $lotType }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Size Filter -->
                        <div class="col-md-2">
                            <label for="sizeFilter" class="form-label">Size</label>
                            <select class="form-select form-select-sm" id="sizeFilter" wire:model="selectedSize" wire:change="updateSizeFilter($event.target.value)">
                                @foreach($availableSizes as $size)
                                    <option value="{{ $size }}">{{ $size }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Search Field -->
                        <div class="col-md-4">
                            <label for="searchField" class="form-label">Search</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" id="searchField"
                                       placeholder="Search..."
                                       wire:model.live.debounce.300ms="searchQuery">
                                <button class="btn btn-warning" wire:click="resetFilters">Reset</button>
                            </div>
                        </div>
                    </div>

                    <!-- Results Table -->
                    <div class="table-responsive">
                        <style>
                            .compact-header th {
                                white-space: nowrap;
                                padding: 0.5rem 0.5rem;
                                font-size: 0.875rem;
                                vertical-align: middle;
                            }
                            .compact-header .sort-icon {
                                margin-left: 2px;
                                font-size: 12px;
                                display: inline-block;
                            }
                            .compact-table td {
                                padding: 0.4rem 0.5rem;
                                font-size: 0.875rem;
                                vertical-align: middle;
                            }
                            .compact-table .badge {
                                font-size: 0.75rem;
                                padding: 0.25em 0.5em;
                            }
                        </style>
                        <table class="table table-bordered compact-table">
                            <thead>
                                <tr class="bg-light compact-header">
                                    <th wire:click="sortBy('lot_id')" style="cursor: pointer; width: 80px;">
                                        Lot No
                                        @if($sortField === 'lot_id')
                                            <i class="ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line sort-icon"></i>
                                        @else
                                            <i class="ri-arrow-up-down-line sort-icon" style="opacity: 0.3;"></i>
                                        @endif
                                    </th>
                                    <th wire:click="sortBy('model_id')" style="cursor: pointer; width: 140px;">
                                        Model
                                        @if($sortField === 'model_id')
                                            <i class="ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line sort-icon"></i>
                                        @else
                                            <i class="ri-arrow-up-down-line sort-icon" style="opacity: 0.3;"></i>
                                        @endif
                                    </th>
                                    <th wire:click="sortBy('lot_qty')" style="cursor: pointer; width: 80px;">
                                        Quantity
                                        @if($sortField === 'lot_qty')
                                            <i class="ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line sort-icon"></i>
                                        @else
                                            <i class="ri-arrow-up-down-line sort-icon" style="opacity: 0.3;"></i>
                                        @endif
                                    </th>
                                    <th wire:click="sortBy('area')" style="cursor: pointer; width: 50px;">
                                        Area
                                        @if($sortField === 'area')
                                            <i class="ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line sort-icon"></i>
                                        @else
                                            <i class="ri-arrow-up-down-line sort-icon" style="opacity: 0.3;"></i>
                                        @endif
                                    </th>
                                    <th wire:click="sortBy('mc_no')" style="cursor: pointer; width: 60px;">
                                        MC No
                                        @if($sortField === 'mc_no')
                                            <i class="ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line sort-icon"></i>
                                        @else
                                            <i class="ri-arrow-up-down-line sort-icon" style="opacity: 0.3;"></i>
                                        @endif
                                    </th>
                                    <th wire:click="sortBy('chip_size')" style="cursor: pointer; width: 50px;">
                                        Size
                                        @if($sortField === 'chip_size')
                                            <i class="ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line sort-icon"></i>
                                        @else
                                            <i class="ri-arrow-up-down-line sort-icon" style="opacity: 0.3;"></i>
                                        @endif
                                    </th>
                                    <th wire:click="sortBy('work_type')" style="cursor: pointer; width: 90px;">
                                        WorkType
                                        @if($sortField === 'work_type')
                                            <i class="ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line sort-icon"></i>
                                        @else
                                            <i class="ri-arrow-up-down-line sort-icon" style="opacity: 0.3;"></i>
                                        @endif
                                    </th>
                                    <th wire:click="sortBy('lot_type')" style="cursor: pointer; width: 70px;">
                                        LotType
                                        @if($sortField === 'lot_type')
                                            <i class="ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line sort-icon"></i>
                                        @else
                                            <i class="ri-arrow-up-down-line sort-icon" style="opacity: 0.3;"></i>
                                        @endif
                                    </th>
                                    <th wire:click="sortBy('status')" style="cursor: pointer; width: 80px;">
                                        Status
                                        @if($sortField === 'status')
                                            <i class="ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line sort-icon"></i>
                                        @else
                                            <i class="ri-arrow-up-down-line sort-icon" style="opacity: 0.3;"></i>
                                        @endif
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paginatedLots['data'] as $lot)
                                <tr>
                                    <td>{{ $lot['lot_id'] }}</td>
                                    <td>{{ $lot['model_id'] }}</td>
                                    <td>{{ number_format($lot['lot_qty']) }}</td>
                                    <td>{{ $lot['area'] }}</td>
                                    <td>{{ $lot['mc_no'] }}</td>
                                    <td>{{ $lot['chip_size'] }}</td>
                                    <td>{{ $lot['work_type'] }}</td>
                                    <td>{{ $lot['lot_type'] }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ strtoupper($lot['status']) == 'SUBMITTED' ? 'bg-success' : 'bg-warning' }}">
                                            {{ $lot['status'] }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">No lots found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            @if($paginatedLots['total'] > 0)
                                <nav>
                                    <ul class="pagination">
                                        <!-- Previous Page Link -->
                                        <li class="page-item {{ $paginatedLots['current_page'] == 1 ? 'disabled' : '' }}">
                                            <a class="page-link" href="#" wire:click.prevent="previousPage">&laquo;</a>
                                        </li>

                                        <!-- Page Number Links -->
                                        @php
                                            $startPage = max(1, $paginatedLots['current_page'] - 2);
                                            $endPage = min($paginatedLots['last_page'], $startPage + 4);

                                            if ($endPage - $startPage < 4 && $startPage > 1) {
                                                $startPage = max(1, $endPage - 4);
                                            }
                                        @endphp

                                        @if($startPage > 1)
                                            <li class="page-item">
                                                <a class="page-link" href="#" wire:click.prevent="gotoPage(1)">1</a>
                                            </li>
                                            @if($startPage > 2)
                                                <li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                </li>
                                            @endif
                                        @endif

                                        @for($i = $startPage; $i <= $endPage; $i++)
                                            <li class="page-item {{ $paginatedLots['current_page'] == $i ? 'active' : '' }}">
                                                <a class="page-link" href="#" wire:click.prevent="gotoPage({{ $i }})">{{ $i }}</a>
                                            </li>
                                        @endfor

                                        @if($endPage < $paginatedLots['last_page'])
                                            @if($endPage < $paginatedLots['last_page'] - 1)
                                                <li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                </li>
                                            @endif
                                            <li class="page-item">
                                                <a class="page-link" href="#" wire:click.prevent="gotoPage({{ $paginatedLots['last_page'] }})">{{ $paginatedLots['last_page'] }}</a>
                                            </li>
                                        @endif

                                        <!-- Next Page Link -->
                                        <li class="page-item {{ $paginatedLots['current_page'] == $paginatedLots['last_page'] ? 'disabled' : '' }}">
                                            <a class="page-link" href="#" wire:click.prevent="nextPage">&raquo;</a>
                                        </li>
                                    </ul>
                                </nav>
                                <div class="text-muted small">
                                    Showing {{ ($paginatedLots['current_page'] - 1) * $paginatedLots['per_page'] + 1 }}
                                    to {{ min($paginatedLots['current_page'] * $paginatedLots['per_page'], $paginatedLots['total']) }}
                                    of {{ $paginatedLots['total'] }} results
                                </div>
                            @endif
                        </div>
                        <div>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal" wire:click="close">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript to open the modal -->
    <script>
        document.addEventListener('livewire:initialized', function () {
            Livewire.on('showLotList', () => {
                const modal = new bootstrap.Modal(document.getElementById('lotListModal'));
                modal.show();
            });
        });
    </script>
</div>
