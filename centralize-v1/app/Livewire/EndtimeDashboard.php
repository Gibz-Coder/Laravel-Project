<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\ViProdWipRealtime;

class EndtimeDashboard extends Component
{
    public $autoRefresh = false;
    public $refreshInterval = 300; // 5 minutes in seconds
    public $date;
    public $displayDate;
    public $cutoff = 'all';
    public $worktype = 'all';
    public $lottype = 'all';
    public $rawWipData;

    public function mount()
    {
        // Initialize auto-refresh from session or default to false
        $this->autoRefresh = Session::get('autoRefresh', false);

        // Initialize date from session or default to current date in Manila timezone
        $now = now()->setTimezone('Asia/Manila');
        $this->date = Session::get('date', $now->format('Y-m-d'));
        $this->displayDate = date('M j, Y', strtotime($this->date));

        // Initialize filters from session or defaults
        $this->worktype = Session::get('worktype', 'all');
        $this->lottype = Session::get('lottype', 'all');

        // Get cutoff from session
        $this->cutoff = Session::get('cutoff', 'all');

        // Only override cutoff with current time-based cutoff if auto-refresh is enabled
        // and there's no specific cutoff in the request or session
        if ($this->autoRefresh) {
            // Check if the cutoff was explicitly set to 'all' by the user
            $explicitlySetToAll = request()->has('cutoff') && request()->input('cutoff') === 'all';

            // Only update if not explicitly set to 'all'
            if (!$explicitlySetToAll) {
                $this->cutoff = $this->getCurrentCutoff();
                // Update session with the new cutoff
                Session::put('cutoff', $this->cutoff);
                Log::info("EndtimeDashboard mount - Auto-refresh is ON, updated cutoff to: {$this->cutoff}");
            } else {
                Log::info("EndtimeDashboard mount - Auto-refresh is ON but cutoff explicitly set to 'all', respecting user selection");
            }
        } else {
            Log::info("EndtimeDashboard mount - Using cutoff from session: {$this->cutoff}");
        }

        // Store current values in session
        $this->storeFiltersInSession();

        // Log the initialization
        Log::info("EndtimeDashboard mount - Initialized with date: {$this->date}, cutoff: {$this->cutoff}, worktype: {$this->worktype}, lottype: {$this->lottype}");

        // Dispatch events to initialize child components
        $this->dispatchInitialEvents();
    }

    /**
     * Called after the component is hydrated but before it's rendered
     */
    public function hydrate()
    {
        Log::info("EndtimeDashboard hydrate - Component hydrated after page refresh");

        // Ensure session values are up to date
        $this->storeFiltersInSession();

        // Dispatch events to refresh child components
        $this->dispatchInitialEvents();
    }

    /**
     * Store current filter values in session
     */
    private function storeFiltersInSession()
    {
        Session::put('date', $this->date);
        Session::put('cutoff', $this->cutoff);
        Session::put('worktype', $this->worktype);
        Session::put('lottype', $this->lottype);

        Log::info("EndtimeDashboard - Stored filters in session: date={$this->date}, cutoff={$this->cutoff}, worktype={$this->worktype}, lottype={$this->lottype}");
    }

    /**
     * Dispatch events to initialize/refresh child components
     */
    private function dispatchInitialEvents()
    {
        $this->dispatch('dateChanged', ['date' => $this->date]);
        $this->dispatch('cutoffChanged', ['cutoff' => $this->cutoff]);
        $this->dispatch('worktypeChanged', ['worktype' => $this->worktype]);
        $this->dispatch('lottypeChanged', ['lottype' => $this->lottype]);

        Log::info("EndtimeDashboard - Dispatched initial events");
    }

    public function updateAutoRefreshState()
    {
        // Get previous state before updating
        $previousState = Session::get('autoRefresh', false);
        $currentState = $this->autoRefresh;

        // Save state to session
        Session::put('autoRefresh', $currentState);

        // Log the change
        Log::info('Auto refresh ' . ($currentState ? 'enabled' : 'disabled'));

        // If turned ON (from OFF), update to current date/time and cutoff and reload page
        if ($currentState && !$previousState) {
            $now = now()->setTimezone('Asia/Manila');
            $this->date = $now->format('Y-m-d');
            Session::put('date', $this->date);

            // Update cutoff based on current time
            $newCutoff = $this->getCurrentCutoff();
            Session::put('cutoff', $newCutoff);

            // Redirect to refresh the page
            return redirect()->route('endtime');
        }

        // Show toast notification
        $this->dispatch('showToast', [
            'title' => 'Auto Refresh',
            'message' => 'Auto refresh ' . ($this->autoRefresh ? 'enabled' : 'disabled'),
            'type' => 'success'
        ]);
    }

    public function updateDate($newDate)
    {
        $this->date = $newDate;
        $this->displayDate = date('M j, Y', strtotime($newDate));

        // Store in session
        Session::put('date', $this->date);
        Log::info("EndtimeDashboard updateDate - Date updated to: {$this->date}");

        // Redirect to the same page instead of using Livewire refresh
        return redirect()->route('endtime');
    }

    public function updateCutoff($newCutoff)
    {
        $this->cutoff = $newCutoff;

        // Store in session
        Session::put('cutoff', $this->cutoff);
        Log::info("EndtimeDashboard updateCutoff - Cutoff updated to: {$this->cutoff}");

        // If auto-refresh is enabled, disable it when manually changing cutoff
        if ($this->autoRefresh) {
            Log::info("EndtimeDashboard updateCutoff - Auto-refresh was ON, turning it OFF to respect manual cutoff selection");
            $this->autoRefresh = false;
            Session::put('autoRefresh', false);
        }

        // Redirect to the same page with explicit cutoff parameter
        return redirect()->route('endtime', [
            'cutoff' => $newCutoff,
            'date' => $this->date,
            'worktype' => $this->worktype,
            'lottype' => $this->lottype,
            'autoRefresh' => $this->autoRefresh ? '1' : '0'
        ]);
    }

    public function updateWorktype($newWorktype)
    {
        $this->worktype = $newWorktype;

        // Store in session
        Session::put('worktype', $this->worktype);
        Log::info("EndtimeDashboard updateWorktype - Worktype updated to: {$this->worktype}");

        // Redirect to the same page instead of using Livewire refresh
        return redirect()->route('endtime');
    }

    public function updateLottype($newLottype)
    {
        $this->lottype = $newLottype;

        // Store in session
        Session::put('lottype', $this->lottype);
        Log::info("EndtimeDashboard updateLottype - Lottype updated to: {$this->lottype}");

        // Redirect to the same page instead of using Livewire refresh
        return redirect()->route('endtime');
    }

    public function loadData()
    {
        // If auto-refresh is enabled, update cutoff based on current time and check for date change
        if ($this->autoRefresh) {
            // Get current date in Manila timezone
            $now = now()->setTimezone('Asia/Manila');
            $currentDate = $now->format('Y-m-d');

            // Check if the date has changed (e.g., after midnight)
            if ($currentDate !== $this->date) {
                Log::info("Date changed from {$this->date} to {$currentDate} - updating date in auto-refresh");

                // Update the date
                $this->date = $currentDate;
                $this->displayDate = $now->format('M j, Y');

                // Store in session
                Session::put('date', $this->date);

                // Dispatch event to notify other components about date change
                $this->dispatch('dateChanged', ['date' => $this->date]);

                // Show toast notification about date change
                $this->dispatch('showToast', [
                    'title' => 'Date Updated',
                    'message' => 'Date automatically updated to ' . $this->displayDate,
                    'type' => 'info'
                ]);
            }

            // Check if the cutoff was explicitly set to 'all' by the user
            $explicitlySetToAll = $this->cutoff === 'all' &&
                (request()->has('cutoff') && request()->input('cutoff') === 'all');

            // Only update cutoff if not explicitly set to 'all'
            if (!$explicitlySetToAll) {
                // Update cutoff based on current time only if auto-refresh is enabled
                $this->updateToCurrentCutoff();
            } else {
                Log::info("Auto-refresh is enabled but cutoff explicitly set to 'all', respecting user selection");
            }
        } else {
            // If auto-refresh is disabled, respect the user's selected cutoff
            // No need to update the cutoff here
            Log::info("Auto-refresh is disabled - keeping user's selected cutoff: {$this->cutoff}");
        }

        // Dispatch events to refresh all child components
        $this->dispatch('refreshData');

        // Show toast notification
        $this->dispatch('showToast', [
            'title' => 'Data Refreshed',
            'message' => 'Dashboard data has been refreshed',
            'type' => 'success'
        ]);
    }

    /**
     * Update to the current cutoff based on Manila time
     */
    public function updateToCurrentCutoff()
    {
        // Check if the cutoff is explicitly set to 'all'
        if ($this->cutoff === 'all' && request()->has('cutoff') && request()->input('cutoff') === 'all') {
            Log::info('Cutoff is explicitly set to "all", not updating to current time-based cutoff');
            return;
        }

        $newCutoff = $this->getCurrentCutoff();
        if ($this->cutoff !== $newCutoff) {
            $this->cutoff = $newCutoff;

            // Store in session
            Session::put('cutoff', $this->cutoff);

            // Dispatch event to notify other components
            $this->dispatch('cutoffChanged', ['cutoff' => $this->cutoff]);

            // Log the change
            Log::info('Cutoff automatically updated to ' . $this->cutoff);
        }
    }

    public function processWipData()
    {
        try {
            // Log the start of processing
            Log::info('Processing WIP data');

            if (empty($this->rawWipData)) {
                $this->dispatch('showToast', [
                    'title' => 'Error',
                    'message' => 'No data provided. Please paste WIP data into the textarea.',
                    'type' => 'error'
                ]);
                return;
            }

            // Parse the raw data (tab-separated values)
            $rows = explode("\n", trim($this->rawWipData));

            // Check if we have at least one row (header) plus data
            if (count($rows) < 2) {
                $this->dispatch('showToast', [
                    'title' => 'Error',
                    'message' => 'Invalid data format. Please ensure you have pasted the correct data.',
                    'type' => 'error'
                ]);
                return;
            }

            // Determine the delimiter (tab or multiple spaces)
            $firstRow = trim($rows[0]);
            $delimiter = (strpos($firstRow, "\t") !== false) ? "\t" : null;

            // Process the header row to get column indexes
            if ($delimiter) {
                // If tabs are found, use them as delimiter
                $header = explode($delimiter, $firstRow);
            } else {
                // Otherwise use regex to split by multiple spaces
                $header = preg_split('/\s{2,}/', $firstRow);
            }

            // Map column names to their indexes
            $columnMap = [];
            foreach ($header as $index => $columnName) {
                $columnName = strtolower(trim($columnName));
                // Skip lipas_yn and ham_yn columns as they don't exist in the database
                if ($columnName !== 'lipas_yn' && $columnName !== 'ham_yn') {
                    $columnMap[$columnName] = $index;
                }
            }

            // Log the column mapping for debugging
            Log::info('Column mapping: ' . json_encode($columnMap));

            // Required columns
            $requiredColumns = [
                'no', 'site', 'facility', 'major_process', 'sub_process', 'lot_status',
                'lot_id', 'model_id', 'lot_qty', 'chip_size', 'work_type', 'hold_yn',
                'tat_days', 'location', 'lot_details', 'routing_name'
            ];

            // Verify all required columns exist
            $missingColumns = [];
            foreach ($requiredColumns as $column) {
                // Special handling for chip_size/lot_size
                if ($column === 'chip_size') {
                    if (!isset($columnMap['chip_size']) && !isset($columnMap['lot_size'])) {
                        $missingColumns[] = 'chip_size or lot_size';
                    }
                } else if (!isset($columnMap[$column])) {
                    $missingColumns[] = $column;
                }
            }

            if (!empty($missingColumns)) {
                $this->dispatch('showToast', [
                    'title' => 'Error',
                    'message' => 'Missing required columns: ' . implode(', ', $missingColumns),
                    'type' => 'error'
                ]);
                return;
            }

            try {
                // Log the data format
                Log::info('WIP data format detected: ' . ($delimiter ? 'Tab-separated' : 'Space-separated'));
                Log::info('Header columns: ' . implode(', ', $header));

                // Get the database table structure
                $tableColumns = Schema::getColumnListing('vi_prod_wip_realtime');
                Log::info('Database table columns: ' . json_encode($tableColumns));

                // Truncate the existing table first (outside of transaction)
                DB::statement('TRUNCATE TABLE vi_prod_wip_realtime');
                Log::info('Existing WIP data truncated');

                // Process data rows
                $insertData = [];
                $rowCount = 0;

                for ($i = 1; $i < count($rows); $i++) {
                    $row = trim($rows[$i]);
                    if (empty($row)) continue;

                    // Split the row using the same delimiter as the header
                    if ($delimiter) {
                        $columns = explode($delimiter, $row);
                    } else {
                        $columns = preg_split('/\s{2,}/', $row);
                    }

                    // Skip if we don't have enough columns
                    if (count($columns) < count($header)) {
                        continue;
                    }

                    // Get the table columns to ensure we only include fields that exist in the database
                    $tableColumns = Schema::getColumnListing('vi_prod_wip_realtime');

                    // Log the column mapping for this row
                    if ($i === 1) {
                        Log::info('First row column count: ' . count($columns));
                    }

                    // Start with basic data that we know exists
                    $data = [
                        'no' => $columns[$columnMap['no']] ?? null,
                        'site' => $columns[$columnMap['site']] ?? null,
                        'facility' => $columns[$columnMap['facility']] ?? null,
                        'major_process' => $columns[$columnMap['major_process']] ?? null,
                        'sub_process' => $columns[$columnMap['sub_process']] ?? null,
                        'lot_status' => $columns[$columnMap['lot_status']] ?? null,
                        'lot_id' => $columns[$columnMap['lot_id']] ?? null,
                        'model_id' => $columns[$columnMap['model_id']] ?? null,
                        'lot_qty' => intval($columns[$columnMap['lot_qty']] ?? 0),
                        'chip_size' => isset($columnMap['chip_size']) ? $columns[$columnMap['chip_size']] : (isset($columnMap['lot_size']) ? $columns[$columnMap['lot_size']] : null),
                        'work_type' => $columns[$columnMap['work_type']] ?? null,
                        'hold_yn' => $columns[$columnMap['hold_yn']] ?? null,
                        'tat_days' => floatval($columns[$columnMap['tat_days']] ?? 0),
                        'location' => $columns[$columnMap['location']] ?? null,
                        'lot_details' => $columns[$columnMap['lot_details']] ?? null,
                        'routing_name' => $columns[$columnMap['routing_name']] ?? null,
                        'production_team' => isset($columnMap['production_team_type']) ? $columns[$columnMap['production_team_type']] : (isset($columnMap['production_team']) ? $columns[$columnMap['production_team']] : null), // Try both production_team_type and production_team
                        'chip_type' => $columns[$columnMap['chip_type']] ?? null,
                        'special_code' => $columns[$columnMap['special_code']] ?? null,
                        'powder_type' => $columns[$columnMap['powder_type']] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    // Explicitly ensure lipas_yn and ham_yn are not included in the data array
                    unset($data['lipas_yn']);
                    unset($data['ham_yn']);

                    // Add optional columns only if they exist in the database
                    if (in_array('work_equip', $tableColumns) && isset($columnMap['work_equip'])) {
                        $data['work_equip'] = $columns[$columnMap['work_equip']] ?? null;
                    }

                    if (in_array('rack', $tableColumns) && isset($columnMap['rack'])) {
                        $data['rack'] = $columns[$columnMap['rack']] ?? null;
                    }

                    if (in_array('facility_2', $tableColumns) && isset($columnMap['facility_2'])) {
                        $data['facility_2'] = $columns[$columnMap['facility_2']] ?? null;
                    }

                    $insertData[] = $data;
                    $rowCount++;

                    // Insert in batches of 1000 to avoid memory issues
                    if (count($insertData) >= 1000) {
                        // Start transaction for this batch
                        DB::beginTransaction();
                        try {
                            DB::table('vi_prod_wip_realtime')->insert($insertData);
                            DB::commit();
                            $insertData = [];
                        } catch (\Exception $e) {
                            if (DB::transactionLevel() > 0) {
                                DB::rollBack();
                            }
                            throw $e;
                        }
                    }
                }

                // Insert any remaining records
                if (!empty($insertData)) {
                    // Log the first record structure for debugging
                    if (isset($insertData[0])) {
                        Log::info('Sample record structure: ' . json_encode(array_keys($insertData[0])));

                        // Get the table columns to ensure we only include fields that exist in the database
                        $tableColumns = Schema::getColumnListing('vi_prod_wip_realtime');
                        Log::info('Database table columns: ' . json_encode($tableColumns));

                        // Filter out any columns that don't exist in the database table
                        foreach ($insertData as &$record) {
                            $originalKeys = array_keys($record);
                            foreach ($originalKeys as $key) {
                                if (!in_array($key, $tableColumns)) {
                                    Log::warning("Removing non-existent column from data: {$key}");
                                    unset($record[$key]);
                                }
                            }
                        }

                        // Log the filtered record structure
                        Log::info('Filtered record structure: ' . json_encode(array_keys($insertData[0])));
                    }

                    // Start transaction for remaining records
                    DB::beginTransaction();
                    try {
                        DB::table('vi_prod_wip_realtime')->insert($insertData);
                        DB::commit();
                    } catch (\Exception $e) {
                        if (DB::transactionLevel() > 0) {
                            DB::rollBack();
                        }
                        throw $e;
                    }
                }

                // Log success
                Log::info('WIP data import completed successfully. ' . $rowCount . ' records imported.');

                // Show success toast notification
                $this->dispatch('showToast', [
                    'title' => 'WIP Data Processed',
                    'message' => $rowCount . ' records have been successfully imported.',
                    'type' => 'success'
                ]);

                // Close the modal
                $this->dispatch('closeModal', ['modalId' => 'updateWipModal']);

                // Clear the textarea
                $this->rawWipData = '';

                // Refresh the dashboard data
                $this->dispatch('refreshData');

            } catch (\Exception $e) {
                // Rollback the transaction on error if it's active
                if (DB::transactionLevel() > 0) {
                    DB::rollBack();
                }

                Log::error('Error processing WIP data: ' . $e->getMessage());

                $this->dispatch('showToast', [
                    'title' => 'Error',
                    'message' => 'Failed to process WIP data: ' . $e->getMessage(),
                    'type' => 'error'
                ]);
            }

        } catch (\Exception $e) {
            // Rollback the transaction on error if it's active
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            Log::error('Error in processWipData: ' . $e->getMessage());

            $this->dispatch('showToast', [
                'title' => 'Error',
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Look up lot information from the database
     */
    public function lookupLot()
    {
        // Get the lot ID from the request
        $lotId = request('lotId');

        Log::info("EndtimeDashboard lookupLot - Called with request data", [
            'request' => request()->all(),
            'lotId' => $lotId
        ]);

        if (!$lotId) {
            Log::error("EndtimeDashboard lookupLot - No lot ID provided in request");
            $this->dispatch('lotLookupResult', [
                'success' => false,
                'message' => 'No lot ID provided'
            ]);
            return;
        }
        try {
            Log::info("EndtimeDashboard lookupLot - Looking up lot: {$lotId}");

            // Find the lot in the database
            $lot = ViProdWipRealtime::findByLotId($lotId);

            if ($lot) {
                Log::info("EndtimeDashboard lookupLot - Found lot: {$lotId}", [
                    'model_id' => $lot->model_id,
                    'lot_qty' => $lot->lot_qty
                ]);

                // Prepare the result data
                $result = [
                    'success' => true,
                    'model_id' => $lot->model_id,
                    'lot_qty' => $lot->lot_qty
                ];

                Log::info("EndtimeDashboard lotLookupResult - Emitting result", $result);

                // Emit the lot information
                $this->dispatch('lotLookupResult', $result);
            } else {
                Log::info("EndtimeDashboard lookupLot - Lot not found: {$lotId}");

                // Prepare the error data
                $result = [
                    'success' => false,
                    'message' => 'Lot not found'
                ];

                Log::info("EndtimeDashboard lotLookupResult - Emitting error", $result);

                // Emit an error
                $this->dispatch('lotLookupResult', $result);
            }
        } catch (\Exception $e) {
            Log::error("Error in EndtimeDashboard lookupLot: " . $e->getMessage(), [
                'exception' => $e
            ]);

            // Prepare the error data
            $result = [
                'success' => false,
                'message' => 'Error looking up lot: ' . $e->getMessage()
            ];

            Log::error("EndtimeDashboard lotLookupResult - Emitting exception error", $result);

            // Emit an error
            $this->dispatch('lotLookupResult', $result);
        }
    }

    public function render()
    {
        return view('livewire.endtime-dashboard');
    }

    /**
     * Determine the current cutoff based on Manila time
     *
     * @return string The current cutoff period
     */
    private function getCurrentCutoff()
    {
        // Get current time in Manila
        $now = now()->setTimezone('Asia/Manila');
        $hours = $now->hour;
        $minutes = $now->minute;
        $currentTime = $hours * 60 + $minutes; // Convert to minutes for easier comparison

        // Determine which cutoff period we're currently in
        $currentCutoff = null;

        if ($currentTime >= 0 && $currentTime < 240) {
            // 00:00~04:00 (12 AM to 4 AM)
            $currentCutoff = '00:00~04:00';
        } elseif ($currentTime >= 240 && $currentTime < 420) {
            // 04:00~07:00 (4 AM to 7 AM)
            $currentCutoff = '04:00~07:00';
        } elseif ($currentTime >= 420 && $currentTime < 720) {
            // 07:00~12:00 (7 AM to 12 PM)
            $currentCutoff = '07:00~12:00';
        } elseif ($currentTime >= 720 && $currentTime < 960) {
            // 12:00~16:00 (12 PM to 4 PM)
            $currentCutoff = '12:00~16:00';
        } elseif ($currentTime >= 960 && $currentTime < 1140) {
            // 16:00~19:00 (4 PM to 7 PM)
            $currentCutoff = '16:00~19:00';
        } else {
            // 19:00~00:00 (7 PM to 12 AM)
            $currentCutoff = '19:00~00:00';
        }

        Log::info("EndtimeDashboard getCurrentCutoff - Current time: {$hours}:{$minutes}, Determined cutoff: {$currentCutoff}");
        return $currentCutoff;
    }
}
