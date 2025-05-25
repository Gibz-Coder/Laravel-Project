<?php

namespace App\Livewire\EndtimeDashboard;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LotListModal extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Pagination properties
    public $perPage = 15;
    public $currentPage = 1;

    public $isOpen = false;
    public $line;
    public $date;
    public $cutoff;
    public $worktype;
    public $lottype;
    public $type;
    public $title;
    public $lots = [];

    // Sorting properties
    public $sortField = 'lot_id';
    public $sortDirection = 'asc';

    // Filter properties
    public $selectedLine = 'ALL';
    public $selectedWorkType = 'ALL';
    public $selectedLotType = 'ALL';
    public $selectedSize = 'ALL';
    public $searchQuery = '';

    // Available options for filters
    public $availableLines = ['ALL'];
    public $availableWorkTypes = ['ALL'];
    public $availableLotTypes = ['ALL'];
    public $availableSizes = ['ALL'];

    protected $listeners = [
        'showLotList' => 'showLotList'
    ];

    /**
     * Load available filter options from the database
     */
    private function loadFilterOptions()
    {
        try {
            // Set predefined work types to match main page dropdown
            $this->availableWorkTypes = ['ALL', 'Normal', 'Process Rework', 'Warehouse'];

            // Set predefined lot types to match main page dropdown
            $this->availableLotTypes = ['ALL', 'MAIN', 'RL', 'LY', 'ADV'];

            try {
                // Get available lines
                $lines = DB::table('vi_prod_endtime_submitted')
                    ->where('endtime_date', $this->date)
                    ->when($this->cutoff !== 'all', function($query) {
                        if ($this->cutoff === 'day') {
                            return $query->whereIn('cutoff_time', ['07:00~12:00', '12:00~16:00', '16:00~19:00']);
                        } elseif ($this->cutoff === 'night') {
                            return $query->whereIn('cutoff_time', ['19:00~00:00', '00:00~04:00', '04:00~07:00']);
                        } else {
                            return $query->where('cutoff_time', $this->cutoff);
                        }
                    })
                    ->select('line')
                    ->distinct()
                    ->orderBy('line')
                    ->pluck('line')
                    ->toArray();

                $this->availableLines = array_merge(['ALL'], $lines);

                // Get available chip sizes
                $chipSizes = DB::table('vi_prod_endtime_submitted')
                    ->where('endtime_date', $this->date)
                    ->when($this->cutoff !== 'all', function($query) {
                        if ($this->cutoff === 'day') {
                            return $query->whereIn('cutoff_time', ['07:00~12:00', '12:00~16:00', '16:00~19:00']);
                        } elseif ($this->cutoff === 'night') {
                            return $query->whereIn('cutoff_time', ['19:00~00:00', '00:00~04:00', '04:00~07:00']);
                        } else {
                            return $query->where('cutoff_time', $this->cutoff);
                        }
                    })
                    ->select('chip_size')
                    ->distinct()
                    ->orderBy('chip_size')
                    ->pluck('chip_size')
                    ->toArray();

                $this->availableSizes = array_merge(['ALL'], $chipSizes);
            } catch (\Exception $dbException) {
                // If database query fails, use default values
                Log::warning("Database query failed in LotListModal loadFilterOptions, using defaults: " . $dbException->getMessage());
                $this->availableLines = ['ALL', 'L1', 'L2', 'L3', 'L4', 'L5'];
                $this->availableSizes = ['ALL', '1.0', '1.5', '2.0', '2.5', '3.0'];
            }

            Log::info("LotListModal loadFilterOptions - Loaded filter options", [
                'lines' => count($this->availableLines) - 1,
                'workTypes' => count($this->availableWorkTypes) - 1,
                'lotTypes' => count($this->availableLotTypes) - 1,
                'sizes' => count($this->availableSizes) - 1
            ]);
        } catch (\Exception $e) {
            Log::error("Error in LotListModal loadFilterOptions: " . $e->getMessage(), [
                'exception' => $e
            ]);

            // Set default values in case of error
            $this->availableLines = ['ALL', 'L1', 'L2', 'L3', 'L4', 'L5'];
            $this->availableWorkTypes = ['ALL', 'Normal', 'Process Rework', 'Warehouse'];
            $this->availableLotTypes = ['ALL', 'MAIN', 'RL', 'LY', 'ADV'];
            $this->availableSizes = ['ALL', '1.0', '1.5', '2.0', '2.5', '3.0'];
        }
    }

    public function showLotList($params)
    {
        // Set properties from parameters
        $this->line = $params['line'] ?? null;
        $this->date = $params['date'] ?? date('Y-m-d');
        $this->cutoff = $params['cutoff'] ?? 'all';
        $this->worktype = $params['worktype'] ?? 'all';
        $this->lottype = $params['lottype'] ?? 'all';
        $this->type = $params['type'] ?? 'all';
        $this->title = $params['title'] ?? 'Lot List';

        // Set initial filter values based on parameters
        $this->selectedLine = $this->line ?? 'ALL';
        $this->selectedWorkType = $this->worktype !== 'all' ? strtoupper($this->worktype) : 'ALL';
        $this->selectedLotType = $this->lottype !== 'all' ? strtoupper($this->lottype) : 'ALL';
        $this->selectedSize = 'ALL';
        $this->searchQuery = '';

        // Load filter options from the database
        $this->loadFilterOptions();

        // Log the action
        Log::info("LotListModal showLotList - Showing lot list modal", [
            'date' => $this->date,
            'cutoff' => $this->cutoff,
            'worktype' => $this->worktype,
            'lottype' => $this->lottype,
            'type' => $this->type,
            'selectedLine' => $this->selectedLine,
            'selectedWorkType' => $this->selectedWorkType,
            'selectedLotType' => $this->selectedLotType
        ]);

        // Fetch lots based on the type
        $this->fetchLots();

        // Reset pagination
        $this->resetPage();

        // Open the modal
        $this->isOpen = true;
    }

    /**
     * Sort the table by the given field
     */
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
        $this->fetchLots();
    }

    /**
     * Fetch lots based on the current filters and type
     */
    private function fetchLots()
    {
        try {
            // Start building the query
            $query = DB::table('vi_prod_endtime_submitted')
                ->where('endtime_date', $this->date);

            // Apply cutoff filter if not 'all'
            if ($this->cutoff !== 'all') {
                if ($this->cutoff === 'day') {
                    $query->whereIn('cutoff_time', ['07:00~12:00', '12:00~16:00', '16:00~19:00']);
                } elseif ($this->cutoff === 'night') {
                    $query->whereIn('cutoff_time', ['19:00~00:00', '00:00~04:00', '04:00~07:00']);
                } else {
                    $query->where('cutoff_time', $this->cutoff);
                }
            }

            // Apply type-specific filters
            switch ($this->type) {
                case 'endtime':
                    // All lots (both pending and submitted)
                    // No additional filter needed
                    break;

                case 'submitted':
                    // Only submitted lots
                    $query->where('status', 'Submitted');
                    break;

                case 'remaining':
                    // Only pending lots
                    $query->where('status', 'Pending');
                    break;

                default:
                    // Default case - show all lots
                    break;
            }

            // Apply UI filters
            if ($this->selectedLine !== 'ALL') {
                $query->where('line', $this->selectedLine);
            }

            if ($this->selectedWorkType !== 'ALL') {
                $query->where('work_type', $this->selectedWorkType);
            }

            if ($this->selectedLotType !== 'ALL') {
                $query->where('lot_type', $this->selectedLotType);
            }

            if ($this->selectedSize !== 'ALL') {
                $query->where('chip_size', $this->selectedSize);
            }

            if (!empty($this->searchQuery)) {
                $searchTerm = '%' . $this->searchQuery . '%';
                $query->where(function($q) use ($searchTerm) {
                    $q->where('lot_id', 'like', $searchTerm)
                      ->orWhere('model_id', 'like', $searchTerm)
                      ->orWhere('line', 'like', $searchTerm)
                      ->orWhere('area', 'like', $searchTerm)
                      ->orWhere('mc_no', 'like', $searchTerm)
                      ->orWhere('chip_size', 'like', $searchTerm);
                });
            }

            // Apply sorting
            $query->orderBy($this->sortField, $this->sortDirection);

            // Get all results first
            $allResults = $query->get();

            // Transform the results to match our expected format
            $transformedResults = $allResults->map(function($item) {
                return [
                    'lot_id' => $item->lot_id,
                    'model_id' => $item->model_id,
                    'lot_qty' => $item->lot_qty,
                    'line' => $item->line,
                    'area' => $item->area,
                    'mc_no' => $item->mc_no,
                    'chip_size' => $item->chip_size,
                    'work_type' => $item->work_type,
                    'lot_type' => $item->lot_type,
                    'status' => $item->status
                ];
            })->toArray();

            // Store the results as an array
            $this->lots = $transformedResults;

            Log::info("LotListModal fetchLots - Fetched " . count($this->lots) . " lots from database", [
                'date' => $this->date,
                'cutoff' => $this->cutoff,
                'type' => $this->type,
                'selectedLine' => $this->selectedLine,
                'selectedWorkType' => $this->selectedWorkType,
                'selectedLotType' => $this->selectedLotType,
                'searchQuery' => $this->searchQuery,
                'sortField' => $this->sortField,
                'sortDirection' => $this->sortDirection
            ]);

        } catch (\Exception $e) {
            Log::error("Error in LotListModal fetchLots: " . $e->getMessage(), [
                'exception' => $e
            ]);

            // Create sample data for offline mode
            $this->lots = $this->getSampleLotsData();

            Log::info("LotListModal fetchLots - Using sample data due to database error");
        }
    }

    /**
     * Get sample lot data for offline mode
     */
    private function getSampleLotsData()
    {
        // Create sample data with different statuses
        $sampleLots = [
            [
                'lot_id' => 'LOT001',
                'model_id' => 'MODEL-A',
                'lot_qty' => 1000,
                'line' => 'L1',
                'area' => 'A1',
                'mc_no' => 'MC01',
                'chip_size' => '1.0',
                'work_type' => 'Normal',
                'lot_type' => 'MAIN',
                'status' => 'Submitted'
            ],
            [
                'lot_id' => 'LOT002',
                'model_id' => 'MODEL-B',
                'lot_qty' => 2000,
                'line' => 'L2',
                'area' => 'A2',
                'mc_no' => 'MC02',
                'chip_size' => '1.5',
                'work_type' => 'Process Rework',
                'lot_type' => 'RL',
                'status' => 'Pending'
            ],
            [
                'lot_id' => 'LOT003',
                'model_id' => 'MODEL-C',
                'lot_qty' => 1500,
                'line' => 'L3',
                'area' => 'A3',
                'mc_no' => 'MC03',
                'chip_size' => '2.0',
                'work_type' => 'Warehouse',
                'lot_type' => 'LY',
                'status' => 'Submitted'
            ]
        ];

        // Filter the sample data based on the current filters
        return array_filter($sampleLots, function($lot) {
            // Apply line filter
            if ($this->selectedLine !== 'ALL' && $lot['line'] !== $this->selectedLine) {
                return false;
            }

            // Apply work type filter
            if ($this->selectedWorkType !== 'ALL' && $lot['work_type'] !== $this->selectedWorkType) {
                return false;
            }

            // Apply lot type filter
            if ($this->selectedLotType !== 'ALL' && $lot['lot_type'] !== $this->selectedLotType) {
                return false;
            }

            // Apply size filter
            if ($this->selectedSize !== 'ALL' && $lot['chip_size'] !== $this->selectedSize) {
                return false;
            }

            // Apply search filter
            if (!empty($this->searchQuery)) {
                $searchTerm = strtolower($this->searchQuery);
                $found = false;

                foreach (['lot_id', 'model_id', 'line', 'area', 'mc_no', 'chip_size'] as $field) {
                    if (strpos(strtolower($lot[$field]), $searchTerm) !== false) {
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    return false;
                }
            }

            // Apply type filter
            if ($this->type === 'submitted' && $lot['status'] !== 'Submitted') {
                return false;
            } else if ($this->type === 'remaining' && $lot['status'] !== 'Pending') {
                return false;
            }

            return true;
        });
    }

    // We no longer need this method as filtering is done directly in the database query
    // Keeping this as a placeholder in case we need to add additional filtering logic later
    private function getFilteredLots($lots)
    {
        return $lots;
    }

    /**
     * Update the line filter
     */
    public function updateLineFilter($line)
    {
        $this->selectedLine = $line;
        $this->resetPage();
        $this->fetchLots();
    }

    /**
     * Update the work type filter
     */
    public function updateWorkTypeFilter($workType)
    {
        $this->selectedWorkType = $workType;
        $this->resetPage();
        $this->fetchLots();
    }

    /**
     * Update the lot type filter
     */
    public function updateLotTypeFilter($lotType)
    {
        $this->selectedLotType = $lotType;
        $this->resetPage();
        $this->fetchLots();
    }

    /**
     * Update the size filter
     */
    public function updateSizeFilter($size)
    {
        $this->selectedSize = $size;
        $this->resetPage();
        $this->fetchLots();
    }

    /**
     * Handle search query updates
     */
    public function updatedSearchQuery()
    {
        $this->resetPage();
        $this->fetchLots();

        // Log search query for debugging
        Log::info("LotListModal updatedSearchQuery - Search query updated", [
            'searchQuery' => $this->searchQuery
        ]);
    }

    /**
     * Reset filters to default values
     */
    public function resetFilters()
    {
        $this->selectedLine = 'ALL';
        $this->selectedWorkType = 'ALL';
        $this->selectedLotType = 'ALL';
        $this->selectedSize = 'ALL';
        $this->searchQuery = '';
        $this->resetPage();

        // Refresh the lots based on the reset filters
        $this->fetchLots();
    }

    public function close()
    {
        $this->isOpen = false;
        $this->resetPage();
        $this->resetFilters();
    }

    /**
     * Get the paginated lots
     */
    public function getPaginatedLots()
    {
        if (empty($this->lots)) {
            return [
                'data' => [],
                'total' => 0,
                'per_page' => $this->perPage,
                'current_page' => $this->currentPage,
                'last_page' => 1
            ];
        }

        $collection = collect($this->lots);

        // Calculate pagination
        $total = $collection->count();
        $startIndex = ($this->currentPage - 1) * $this->perPage;
        $paginatedItems = $collection->slice($startIndex, $this->perPage)->values();

        return [
            'data' => $paginatedItems,
            'total' => $total,
            'per_page' => $this->perPage,
            'current_page' => $this->currentPage,
            'last_page' => max(1, ceil($total / $this->perPage))
        ];
    }

    /**
     * Go to next page
     */
    public function nextPage()
    {
        $pagination = $this->getPaginatedLots();
        if ($this->currentPage < $pagination['last_page']) {
            $this->currentPage++;
        }
    }

    /**
     * Go to previous page
     */
    public function previousPage()
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
        }
    }

    /**
     * Go to a specific page
     */
    public function gotoPage($page)
    {
        $pagination = $this->getPaginatedLots();
        if ($page >= 1 && $page <= $pagination['last_page']) {
            $this->currentPage = $page;
        }
    }

    /**
     * Reset pagination to first page
     */
    public function resetPage()
    {
        $this->currentPage = 1;
    }

    public function render()
    {
        // Get paginated lots for the view
        $paginatedLots = $this->getPaginatedLots();

        return view('livewire.endtime-dashboard.lot-list-modal', [
            'paginatedLots' => $paginatedLots
        ]);
    }
}
