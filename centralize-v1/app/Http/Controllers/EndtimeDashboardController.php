<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EndtimeDashboard;
use App\Models\ViEqpMcList;
use App\Models\ViProdEndtimeSubmitted;
use App\Models\ViProdWipRealtime;
use App\Models\ViLipasModels;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class EndtimeDashboardController extends Controller
{
    /**
     * Save auto-refresh state to session
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveAutoRefreshState(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'autoRefresh' => 'required|boolean',
                'cutoff' => 'sometimes|string',
            ]);

            // Get the auto-refresh state from the request
            $autoRefresh = $request->input('autoRefresh');

            // Get the cutoff from the request if provided
            $requestCutoff = $request->input('cutoff');

            // Get the previous auto-refresh state
            $previousAutoRefresh = session('autoRefresh', false);

            // Store in session
            session(['autoRefresh' => $autoRefresh]);

            // If turning auto-refresh ON (from OFF), reset all filters to default
            if ($autoRefresh && !$previousAutoRefresh) {
                // Get current time in Manila
                $now = now()->setTimezone('Asia/Manila');

                // Reset date to current date
                session(['date' => $now->format('Y-m-d')]);

                // Reset worktype and lottype to 'all'
                session(['worktype' => 'all']);
                session(['lottype' => 'all']);

                // Determine current cutoff based on time
                $currentCutoff = $this->getCurrentCutoffTime();
                session(['cutoff' => $currentCutoff]);

                Log::info('Auto refresh enabled - reset all filters to default values');
                Log::info('Auto refresh enabled - updated cutoff to ' . $currentCutoff);
            }
            // If auto-refresh is already ON, just update the cutoff
            else if ($autoRefresh && $previousAutoRefresh) {
                // Get current time in Manila
                $now = now()->setTimezone('Asia/Manila');

                // Update date to current date
                session(['date' => $now->format('Y-m-d')]);

                // Determine current cutoff based on time
                $currentCutoff = $this->getCurrentCutoffTime();
                session(['cutoff' => $currentCutoff]);

                Log::info('Auto refresh already enabled - updated cutoff to ' . $currentCutoff);
            }
            // If turning OFF, keep the user's selected cutoff
            else {
                if ($requestCutoff) {
                    session(['cutoff' => $requestCutoff]);
                    Log::info('Auto refresh disabled - using cutoff from request: ' . $requestCutoff);
                } else {
                    // Keep the current cutoff selection from session
                    Log::info('Auto refresh disabled - keeping current cutoff selection: ' . session('cutoff', 'all'));
                }
            }

            // Log the change
            Log::info('Auto refresh ' . ($autoRefresh ? 'enabled' : 'disabled') . ' via API');

            return response()->json([
                'success' => true,
                'message' => 'Auto refresh ' . ($autoRefresh ? 'enabled' : 'disabled'),
                'autoRefresh' => $autoRefresh,
                'cutoff' => session('cutoff'),
                'worktype' => session('worktype'),
                'lottype' => session('lottype'),
                'date' => session('date')
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving auto-refresh state: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error saving auto-refresh state: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Get target capacity
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTargetCapacity(Request $request)
    {
        try {
            $date = $request->input('date', Carbon::today()->format('Y-m-d'));
            $cutoff = $request->input('cutoff', 'all');
            $worktype = $request->input('worktype', 'all');
            $lottype = $request->input('lottype', 'all');

            // Check if the vi_capa_ref table exists
            $tableExists = DB::select("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'vi_capa_ref'");

            if ($tableExists[0]->count == 0) {
                // Table doesn't exist, return a default value
                return response()->json([
                    'target' => 10000000,
                    'error' => 'Table vi_capa_ref not found'
                ]);
            }

            // Check if the actual_capa column exists
            $columnExists = DB::select("SELECT COUNT(*) as count FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'vi_capa_ref' AND column_name = 'actual_capa'");

            if ($columnExists[0]->count == 0) {
                // Column doesn't exist, return a default value
                return response()->json([
                    'target' => 10000000,
                    'error' => 'Column actual_capa not found in vi_capa_ref table'
                ]);
            }

            $target = EndtimeDashboard::getTargetCapacity($date, $cutoff, $worktype, $lottype);

            return response()->json([
                'target' => $target,
                'filters' => [
                    'date' => $date,
                    'cutoff' => $cutoff,
                    'worktype' => $worktype,
                    'lottype' => $lottype
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'target' => 10000000,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get performance target (full day target)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPerformanceTarget(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $worktype = $request->input('worktype', 'all');
        $lottype = $request->input('lottype', 'all');

        // For performance target, we always use the full day target (cutoff = 'all')
        $target = EndtimeDashboard::getTargetCapacity($date, 'all', $worktype, $lottype);

        return response()->json([
            'target' => $target
        ]);
    }

    /**
     * Get endtime total
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEndtimeTotal(Request $request)
    {
        try {
            $date = $request->input('date', Carbon::today()->format('Y-m-d'));
            $cutoff = $request->input('cutoff', 'all');
            $worktype = $request->input('worktype', 'all');
            $lottype = $request->input('lottype', 'all');

            // Check if the vi_prod_endtime_submitted table exists
            $tableExists = DB::select("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'vi_prod_endtime_submitted'");

            if ($tableExists[0]->count == 0) {
                // Table doesn't exist, return default values
                return response()->json([
                    'total' => 0,
                    'count' => 0,
                    'percentage' => 0,
                    'error' => 'Table vi_prod_endtime_submitted not found'
                ]);
            }

            $data = EndtimeDashboard::getEndtimeTotal($date, $cutoff, $worktype, $lottype);

            // Add filter information to the response
            $data['filters'] = [
                'date' => $date,
                'cutoff' => $cutoff,
                'worktype' => $worktype,
                'lottype' => $lottype
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'total' => 0,
                'count' => 0,
                'percentage' => 0,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get endtime total by cutoff
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEndtimeTotalByCutoff(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $cutoff = $request->input('cutoff');
        $worktype = $request->input('worktype', 'all');
        $lottype = $request->input('lottype', 'all');

        $data = EndtimeDashboard::getEndtimeTotal($date, $cutoff, $worktype, $lottype);

        return response()->json($data);
    }

    /**
     * Get submitted total
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSubmittedTotal(Request $request)
    {
        try {
            $date = $request->input('date', Carbon::today()->format('Y-m-d'));
            $cutoff = $request->input('cutoff', 'all');
            $worktype = $request->input('worktype', 'all');
            $lottype = $request->input('lottype', 'all');

            // Check if the vi_prod_endtime_submitted table exists
            $tableExists = DB::select("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'vi_prod_endtime_submitted'");

            if ($tableExists[0]->count == 0) {
                // Table doesn't exist, return default values
                return response()->json([
                    'total' => 0,
                    'count' => 0,
                    'percentage' => 0,
                    'error' => 'Table vi_prod_endtime_submitted not found'
                ]);
            }

            $data = EndtimeDashboard::getSubmittedTotal($date, $cutoff, $worktype, $lottype);

            // Add filter information to the response
            $data['filters'] = [
                'date' => $date,
                'cutoff' => $cutoff,
                'worktype' => $worktype,
                'lottype' => $lottype
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'total' => 0,
                'count' => 0,
                'percentage' => 0,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get remaining total
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRemainingTotal(Request $request)
    {
        try {
            $date = $request->input('date', Carbon::today()->format('Y-m-d'));
            $cutoff = $request->input('cutoff', 'all');
            $worktype = $request->input('worktype', 'all');
            $lottype = $request->input('lottype', 'all');

            // Check if the vi_prod_endtime_submitted table exists
            $tableExists = DB::select("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'vi_prod_endtime_submitted'");

            if ($tableExists[0]->count == 0) {
                // Table doesn't exist, return default values
                return response()->json([
                    'total' => 0,
                    'count' => 0,
                    'percentage' => 0,
                    'error' => 'Table vi_prod_endtime_submitted not found'
                ]);
            }

            $data = EndtimeDashboard::getRemainingTotal($date, $cutoff, $worktype, $lottype);

            // Add filter information to the response
            $data['filters'] = [
                'date' => $date,
                'cutoff' => $cutoff,
                'worktype' => $worktype,
                'lottype' => $lottype
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'total' => 0,
                'count' => 0,
                'percentage' => 0,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get line production data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLineProduction(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $cutoff = $request->input('cutoff', 'all');
        $worktype = $request->input('worktype', 'all');
        $lottype = $request->input('lottype', 'all');

        // Define the lines
        $lines = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "VMI"];

        $endtimeData = [];
        $submittedData = [];

        foreach ($lines as $line) {
            // Get endtime data for this line
            $query = EndtimeDashboard::query();

            // Apply date filter
            if ($date) {
                $query->whereDate('endtime_date', $date);
            } else {
                $query->whereDate('endtime_date', Carbon::today());
            }

            // Apply cutoff filter
            if ($cutoff && $cutoff != 'all') {
                if ($cutoff == 'day') {
                    $query->whereIn('cutoff_time', ['04:00~07:00', '07:00~12:00', '12:00~16:00']);
                } elseif ($cutoff == 'night') {
                    $query->whereIn('cutoff_time', ['16:00~19:00', '19:00~00:00', '00:00~04:00']);
                } else {
                    $query->where('cutoff_time', $cutoff);
                }
            }

            // Apply worktype filter
            if ($worktype && $worktype != 'all' && $worktype != 'Worktype - all') {
                $query->where('work_type', $worktype);
            }

            // Apply lottype filter
            if ($lottype && $lottype != 'all' && $lottype != 'Lottype - all') {
                $query->where('lot_type', $lottype);
            }

            // Filter by line
            $query->where('line', $line);

            // Get total quantity for endtime
            $endtimeTotal = $query->sum('lot_qty');
            $endtimeData[] = (int)$endtimeTotal;

            // Get total quantity for submitted (status = 'Submitted')
            $submittedTotal = $query->where('status', 'Submitted')->sum('lot_qty');
            $submittedData[] = (int)$submittedTotal;
        }

        return response()->json([
            'endtime' => $endtimeData,
            'submitted' => $submittedData
        ]);
    }

    /**
     * Get line target data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLineTarget(Request $request)
    {
        $worktype = $request->input('worktype', 'all');
        $cutoff = $request->input('cutoff', 'all');

        // Define the lines
        $lines = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "VMI"];

        // Get base target
        $baseTarget = EndtimeDashboard::getTargetCapacity(null, $cutoff, $worktype, null);

        // Distribution percentages for each line (example values)
        $lineDistribution = [
            "A" => 0.15,  // 15% of target for line A
            "B" => 0.15,  // 15% of target for line B
            "C" => 0.10,  // 10% of target for line C
            "D" => 0.10,  // 10% of target for line D
            "E" => 0.10,  // 10% of target for line E
            "F" => 0.10,  // 10% of target for line F
            "G" => 0.10,  // 10% of target for line G
            "H" => 0.05,  // 5% of target for line H
            "I" => 0.05,  // 5% of target for line I
            "J" => 0.05,  // 5% of target for line J
            "VMI" => 0.05 // 5% of target for line VMI
        ];

        $targetData = [];

        foreach ($lines as $line) {
            $lineTarget = $baseTarget * ($lineDistribution[$line] ?? 0.09); // Default to 9% if not specified
            $targetData[] = (int)$lineTarget;
        }

        return response()->json([
            'target' => $targetData
        ]);
    }

    /**
     * Get size production data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSizeProduction(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $cutoff = $request->input('cutoff', 'all');
        $worktype = $request->input('worktype', 'all');
        $lottype = $request->input('lottype', 'all');

        // Define the sizes
        $sizes = ["03", "05", "10", "21", "31", "32"];

        $endtimeData = [];
        $submittedData = [];

        foreach ($sizes as $size) {
            // Get endtime data for this size
            $query = EndtimeDashboard::query();

            // Apply date filter
            if ($date) {
                $query->whereDate('endtime_date', $date);
            } else {
                $query->whereDate('endtime_date', Carbon::today());
            }

            // Apply cutoff filter
            if ($cutoff && $cutoff != 'all') {
                if ($cutoff == 'day') {
                    $query->whereIn('cutoff_time', ['04:00~07:00', '07:00~12:00', '12:00~16:00']);
                } elseif ($cutoff == 'night') {
                    $query->whereIn('cutoff_time', ['16:00~19:00', '19:00~00:00', '00:00~04:00']);
                } else {
                    $query->where('cutoff_time', $cutoff);
                }
            }

            // Apply worktype filter
            if ($worktype && $worktype != 'all' && $worktype != 'Worktype - all') {
                $query->where('work_type', $worktype);
            }

            // Apply lottype filter
            if ($lottype && $lottype != 'all' && $lottype != 'Lottype - all') {
                $query->where('lot_type', $lottype);
            }

            // Filter by size
            $query->where('chip_size', $size);

            // Get total quantity for endtime
            $endtimeTotal = $query->sum('lot_qty');
            $endtimeData[] = (int)$endtimeTotal;

            // Get total quantity for submitted (status = 'Submitted')
            $submittedTotal = $query->where('status', 'Submitted')->sum('lot_qty');
            $submittedData[] = (int)$submittedTotal;
        }

        return response()->json([
            'endtime' => $endtimeData,
            'submitted' => $submittedData
        ]);
    }

    /**
     * Get size target data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSizeTarget(Request $request)
    {
        $worktype = $request->input('worktype', 'all');
        $cutoff = $request->input('cutoff', 'all');

        // Define the sizes
        $sizes = ["03", "05", "10", "21", "31", "32"];

        // Get base target
        $baseTarget = EndtimeDashboard::getTargetCapacity(null, $cutoff, $worktype, null);

        // Distribution percentages for each size (example values)
        $sizeDistribution = [
            "03" => 0.20,  // 20% of target for size 03
            "05" => 0.20,  // 20% of target for size 05
            "10" => 0.20,  // 20% of target for size 10
            "21" => 0.15,  // 15% of target for size 21
            "31" => 0.15,  // 15% of target for size 31
            "32" => 0.10   // 10% of target for size 32
        ];

        $targetData = [];

        foreach ($sizes as $size) {
            $sizeTarget = $baseTarget * ($sizeDistribution[$size] ?? 0.16); // Default to 16% if not specified
            $targetData[] = (int)$sizeTarget;
        }

        return response()->json([
            'target' => $targetData
        ]);
    }

    /**
     * Look up machine information from vi_eqp_mc_list table
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function lookupMachine(Request $request)
    {
        try {
            $mcNo = $request->input('mcNo');

            if (!$mcNo) {
                return response()->json([
                    'success' => false,
                    'message' => 'No machine number provided'
                ]);
            }

            // Check if the vi_eqp_mc_list table exists
            $tableExists = DB::select("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'vi_eqp_mc_list'");

            if ($tableExists[0]->count == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Table vi_eqp_mc_list not found'
                ]);
            }

            // Query the database for the machine information using the model
            $machine = ViEqpMcList::where('mc_no', $mcNo)
                ->select('mc_no', 'area', 'line', 'mc_type')
                ->first();

            if ($machine) {
                return response()->json([
                    'success' => true,
                    'mc_no' => $machine->mc_no,
                    'area' => $machine->area,
                    'line' => $machine->line,
                    'mc_type' => $machine->mc_type
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Machine not found'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error looking up machine: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Save endtime entries to the database
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveEndtime(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'entries' => 'required|array',
                'entries.*.lot_id' => 'required|string',
                'entries.*.model_id' => 'required|string',
                'entries.*.lot_qty' => 'required|numeric',
                'entries.*.mc_no' => 'required|string',
                'entries.*.lot_type' => 'required|string',
                'entries.*.endtime_date' => 'required|string',
                'entries.*.cutoff_time' => 'required|string',
            ]);

            $entries = $request->input('entries');
            $savedEntries = [];
            $errors = [];
            $duplicates = [];

            foreach ($entries as $index => $entry) {
                try {
                    // Extract data from the entry and transform lot_id to uppercase
                    $lotId = strtoupper(trim($entry['lot_id']));
                    $modelId = $entry['model_id'];
                    $lotQty = $entry['lot_qty'];
                    $mcNo = $entry['mc_no'];
                    $lotType = $entry['lot_type'];
                    $endtimeDate = $entry['endtime_date'];
                    $cutoffTime = $entry['cutoff_time'];
                    $area = $entry['area'] ?? null;

                    // Check for duplicate lot_id in the same cutoff and date (regardless of mc_no and lot_type)
                    Log::info("Checking for duplicate lot_id in same cutoff", [
                        'lot_id' => $lotId,
                        'endtime_date' => $endtimeDate,
                        'cutoff_time' => $cutoffTime
                    ]);

                    $duplicateLotQuery = ViProdEndtimeSubmitted::where('lot_id', $lotId)
                        ->whereDate('endtime_date', $endtimeDate)
                        ->where('cutoff_time', $cutoffTime);

                    $existingLotEntry = $duplicateLotQuery->first();

                    if ($existingLotEntry) {
                        Log::warning("Duplicate lot_id found in same cutoff", [
                            'lot_id' => $lotId,
                            'endtime_date' => $endtimeDate,
                            'cutoff_time' => $cutoffTime,
                            'existing_mc_no' => $existingLotEntry->mc_no,
                            'existing_lot_type' => $existingLotEntry->lot_type,
                            'existing_status' => $existingLotEntry->status
                        ]);

                        $duplicates[] = [
                            'index' => $index,
                            'lot_id' => $lotId,
                            'endtime_date' => $endtimeDate,
                            'cutoff_time' => $cutoffTime,
                            'existing_mc_no' => $existingLotEntry->mc_no,
                            'existing_lot_type' => $existingLotEntry->lot_type,
                            'existing_status' => $existingLotEntry->status,
                            'reason' => "Same lot number already exists in this cutoff (MC: {$existingLotEntry->mc_no})"
                        ];

                        // Skip this entry
                        continue;
                    }

                    // Calculate qty_class based on chip_size and lot_qty
                    $qtyClass = 'large'; // Default to large

                    // Look up lot information from vi_prod_wip_realtime
                    $wipLot = ViProdWipRealtime::where('lot_id', $lotId)->first();
                    $workType = $wipLot ? $wipLot->work_type : null;

                    // Get chip size from the database if available
                    $chipSize = '';
                    if ($wipLot && !empty($wipLot->chip_size)) {
                        $chipSize = $wipLot->chip_size;
                    } else {
                        // Try to extract chip size from model_id if possible
                        // This is a fallback and might need adjustment based on your model_id format
                        if (preg_match('/(\d{2})/', $modelId, $matches)) {
                            $chipSize = $matches[1];
                        } else {
                            // Default to '03' if we can't determine the chip size
                            $chipSize = '03';
                            Log::warning("Could not determine chip size for model: $modelId, defaulting to 03");
                        }
                    }

                    // Determine qty_class based on chip_size and lot_qty
                    if (($chipSize == '02' || $chipSize == '03' || $chipSize == '05') && $lotQty < 1000000) {
                        $qtyClass = 'small';
                    } elseif ($chipSize == '10' && $lotQty < 500000) {
                        $qtyClass = 'small';
                    } elseif ($chipSize == '21' && $lotQty < 300000) {
                        $qtyClass = 'small';
                    } elseif ($chipSize == '31' && $lotQty < 150000) {
                        $qtyClass = 'small';
                    } elseif ($chipSize == '32' && $lotQty < 50000) {
                        $qtyClass = 'small';
                    }

                    // Check for duplicate entries (same Mc No, status=PENDING, same endtime_date)
                    Log::info("Checking for duplicate entry", [
                        'mc_no' => $mcNo,
                        'endtime_date' => $endtimeDate,
                        'qty_class' => $qtyClass
                    ]);

                    // Build the query
                    $query = ViProdEndtimeSubmitted::where('mc_no', $mcNo)
                        ->where('status', 'PENDING')
                        ->whereDate('endtime_date', $endtimeDate);

                    // Log the SQL query for debugging
                    $sql = $query->toSql();
                    $bindings = $query->getBindings();
                    Log::info("Duplicate check SQL query: " . $sql, [
                        'bindings' => $bindings
                    ]);

                    // Execute the query
                    $existingEntry = $query->first();

                    if ($existingEntry) {
                        Log::info("Found existing entry with same MC No", [
                            'existing_entry' => $existingEntry->toArray(),
                            'new_entry_qty_class' => $qtyClass
                        ]);

                        // If the current lot is small, allow it to be added to the same machine
                        if ($qtyClass === 'small') {
                            Log::info("Allowing small lot to be added to the same machine", [
                                'lot_id' => $lotId,
                                'mc_no' => $mcNo,
                                'qty_class' => $qtyClass
                            ]);
                            // Continue processing this entry (don't skip)
                        } else {
                            // For large lots, follow the original logic - don't allow duplicates
                            // Found a duplicate entry
                            $duplicates[] = [
                                'index' => $index,
                                'mc_no' => $mcNo,
                                'endtime_date' => $endtimeDate,
                                'existing_lot_id' => $existingEntry->lot_id,
                                'qty_class' => $qtyClass
                            ];

                            Log::warning("Duplicate entry detected for large lot", [
                                'mc_no' => $mcNo,
                                'endtime_date' => $endtimeDate,
                                'existing_lot_id' => $existingEntry->lot_id,
                                'new_lot_id' => $lotId,
                                'qty_class' => $qtyClass
                            ]);

                            // Skip this entry
                            continue;
                        }
                    }

                    // Extract chip size from the model_id if not already done
                    if (empty($chipSize) && strlen($modelId) >= 4) {
                        // Try to extract chip size using regex pattern for Samsung models
                        // Pattern: CL followed by 2 digits (chip size)
                        if (preg_match('/^CL(\d{2})/', $modelId, $matches)) {
                            $chipSize = $matches[1];
                        } else {
                            // Fallback to extracting the 3rd and 4th characters
                            $chipSize = substr($modelId, 2, 2);
                        }
                    }

                    // Look up machine information from vi_eqp_mc_list
                    $machine = ViEqpMcList::where('mc_no', $mcNo)->first();
                    $line = $machine ? $machine->line : null;
                    $mcType = $machine ? $machine->mc_type : null;
                    $inspectionType = $machine ? $machine->inspection_type : null;

                    // If area is not provided, use the one from the machine lookup
                    if (!$area && $machine) {
                        $area = $machine->area;
                    }

                    // Look up lipas_yn and ham_yn from vi_lipas_models
                    try {
                        $lipasModel = ViLipasModels::where('model_id', $modelId)->first();
                        $lipasYn = $lipasModel ? $lipasModel->lipas_yn : 'N';
                        $hamYn = $lipasModel ? $lipasModel->ham_yn : 'N';
                    } catch (\Exception $e) {
                        // If there's any error with the model lookup, default to 'N'
                        Log::warning("Error looking up model in vi_lipas_models: " . $e->getMessage(), [
                            'model_id' => $modelId
                        ]);
                        $lipasYn = 'N';
                        $hamYn = 'N';
                    }

                    // Calculate week number based on the first Thursday of the year
                    $weekNo = $this->calculateWeekNumber($endtimeDate);

                    // Create a new endtime entry
                    $endtimeEntry = new ViProdEndtimeSubmitted();
                    $endtimeEntry->lot_id = $lotId;
                    $endtimeEntry->model_id = $modelId;
                    $endtimeEntry->lot_qty = $lotQty;
                    $endtimeEntry->qty_class = $qtyClass;
                    $endtimeEntry->chip_size = $chipSize;
                    $endtimeEntry->work_type = $workType;
                    $endtimeEntry->lot_type = $lotType;
                    $endtimeEntry->mc_no = $mcNo;
                    $endtimeEntry->line = $line;
                    $endtimeEntry->area = $area;
                    $endtimeEntry->mc_type = $mcType;
                    $endtimeEntry->inspection_type = $inspectionType;
                    $endtimeEntry->lipas_yn = $lipasYn;
                    $endtimeEntry->ham_yn = $hamYn;
                    $endtimeEntry->status = 'PENDING';
                    $endtimeEntry->week_no = $weekNo;
                    $endtimeEntry->endtime_date = $endtimeDate;
                    $endtimeEntry->cutoff_time = $cutoffTime;
                    $endtimeEntry->save();

                    $savedEntries[] = $endtimeEntry;
                } catch (\Exception $e) {
                    $errors[] = [
                        'index' => $index,
                        'message' => $e->getMessage()
                    ];
                    Log::error("Error saving endtime entry: " . $e->getMessage(), [
                        'entry' => $entry,
                        'exception' => $e
                    ]);
                }
            }

            return response()->json([
                'success' => count($savedEntries) > 0,
                'saved_count' => count($savedEntries),
                'error_count' => count($errors),
                'errors' => $errors,
                'duplicate_count' => count($duplicates),
                'duplicates' => $duplicates
            ]);
        } catch (\Exception $e) {
            Log::error("Error in saveEndtime: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save submitted lot entries to the database
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveSubmitted(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'entries' => 'required|array',
                'entries.*.lot_id' => 'required|string',
                'entries.*.model_id' => 'required|string',
                'entries.*.lot_qty' => 'required|numeric',
                'entries.*.mc_no' => 'required|string',
                'entries.*.lot_type' => 'required|string',
                'entries.*.endtime_date' => 'required|string',
                'entries.*.cutoff_time' => 'required|string',
            ]);

            $entries = $request->input('entries');
            $savedEntries = [];
            $updatedEntries = [];
            $errors = [];
            $duplicates = [];

            foreach ($entries as $index => $entry) {
                try {
                    // Extract data from the entry and transform lot_id to uppercase
                    $lotId = strtoupper(trim($entry['lot_id']));
                    $modelId = $entry['model_id'];
                    $lotQty = $entry['lot_qty'];
                    $mcNo = $entry['mc_no'];
                    $lotType = $entry['lot_type'];
                    $endtimeDate = $entry['endtime_date'];
                    $cutoffTime = $entry['cutoff_time'];
                    $area = $entry['area'] ?? null;

                    // Check for duplicate lot_id in the same cutoff and date (regardless of mc_no and lot_type)
                    Log::info("Checking for duplicate lot_id in same cutoff", [
                        'lot_id' => $lotId,
                        'endtime_date' => $endtimeDate,
                        'cutoff_time' => $cutoffTime
                    ]);

                    $duplicateLotQuery = ViProdEndtimeSubmitted::where('lot_id', $lotId)
                        ->whereDate('endtime_date', $endtimeDate)
                        ->where('cutoff_time', $cutoffTime);

                    $existingLotEntry = $duplicateLotQuery->first();

                    if ($existingLotEntry) {
                        // Special case: If same lot_id and same mc_no with PENDING status, update to SUBMITTED
                        if ($existingLotEntry->mc_no === $mcNo && $existingLotEntry->status === 'PENDING') {
                            Log::info("Found PENDING entry with same lot_id and mc_no, updating to SUBMITTED", [
                                'lot_id' => $lotId,
                                'mc_no' => $mcNo,
                                'endtime_date' => $endtimeDate,
                                'cutoff_time' => $cutoffTime
                            ]);

                            // Update the status to SUBMITTED
                            $existingLotEntry->status = 'SUBMITTED';
                            $existingLotEntry->save();

                            $updatedEntries[] = $existingLotEntry;
                            continue; // Skip to the next entry
                        }

                        // Otherwise, it's a duplicate with different MC or already SUBMITTED
                        Log::warning("Duplicate lot_id found in same cutoff", [
                            'lot_id' => $lotId,
                            'endtime_date' => $endtimeDate,
                            'cutoff_time' => $cutoffTime,
                            'existing_mc_no' => $existingLotEntry->mc_no,
                            'existing_lot_type' => $existingLotEntry->lot_type,
                            'existing_status' => $existingLotEntry->status,
                            'new_mc_no' => $mcNo
                        ]);

                        $duplicates[] = [
                            'index' => $index,
                            'lot_id' => $lotId,
                            'endtime_date' => $endtimeDate,
                            'cutoff_time' => $cutoffTime,
                            'existing_mc_no' => $existingLotEntry->mc_no,
                            'existing_lot_type' => $existingLotEntry->lot_type,
                            'existing_status' => $existingLotEntry->status,
                            'reason' => "Same lot number already exists in this cutoff (MC: {$existingLotEntry->mc_no})"
                        ];

                        // Skip this entry
                        continue;
                    }



                    // Check for duplicate entries (same lot_id, mc_no, lot_type, cutoff_time, endtime_date)
                    Log::info("Checking for duplicate entry", [
                        'lot_id' => $lotId,
                        'mc_no' => $mcNo,
                        'endtime_date' => $endtimeDate,
                        'cutoff_time' => $cutoffTime
                    ]);

                    // Build the query for duplicates
                    $duplicateQuery = ViProdEndtimeSubmitted::where('lot_id', $lotId)
                        ->where('mc_no', $mcNo)
                        ->where('lot_type', $lotType)
                        ->whereDate('endtime_date', $endtimeDate)
                        ->where('cutoff_time', $cutoffTime);

                    // Check if a duplicate exists
                    $existingEntry = $duplicateQuery->first();

                    if ($existingEntry) {
                        Log::warning("Duplicate entry found", [
                            'lot_id' => $lotId,
                            'mc_no' => $mcNo,
                            'existing_status' => $existingEntry->status,
                            'endtime_date' => $endtimeDate
                        ]);

                        $duplicates[] = [
                            'lot_id' => $lotId,
                            'mc_no' => $mcNo,
                            'existing_status' => $existingEntry->status,
                            'endtime_date' => $endtimeDate
                        ];

                        continue; // Skip this entry
                    }

                    // Extract chip size from the model_id
                    $chipSize = '';
                    if (strlen($modelId) >= 4) {
                        // Try to extract chip size using regex pattern for Samsung models
                        // Pattern: CL followed by 2 digits (chip size)
                        if (preg_match('/^CL(\d{2})/', $modelId, $matches)) {
                            $chipSize = $matches[1];
                        } else {
                            // Fallback to extracting the 3rd and 4th characters
                            $chipSize = substr($modelId, 2, 2);
                        }
                    }

                    // Calculate qty_class based on chip_size and lot_qty
                    $qtyClass = 'large'; // Default to large

                    // Look up lot information from vi_prod_wip_realtime
                    $wipLot = ViProdWipRealtime::where('lot_id', $lotId)->first();
                    $workType = $wipLot ? $wipLot->work_type : null;

                    // If chip size is empty or not recognized, try to get it from the database
                    if (empty($chipSize) && $wipLot && !empty($wipLot->chip_size)) {
                        $chipSize = $wipLot->chip_size;
                    } elseif (empty($chipSize)) {
                        // Default to '03' if we can't determine the chip size
                        $chipSize = '03';
                        Log::warning("Could not determine chip size for model: $modelId, defaulting to 03");
                    }

                    // Determine qty_class based on chip_size and lot_qty
                    if (($chipSize == '02' || $chipSize == '03' || $chipSize == '05') && $lotQty < 1000000) {
                        $qtyClass = 'small';
                    } elseif ($chipSize == '10' && $lotQty < 500000) {
                        $qtyClass = 'small';
                    } elseif ($chipSize == '21' && $lotQty < 300000) {
                        $qtyClass = 'small';
                    } elseif ($chipSize == '31' && $lotQty < 150000) {
                        $qtyClass = 'small';
                    } elseif ($chipSize == '32' && $lotQty < 50000) {
                        $qtyClass = 'small';
                    }

                    // Look up machine information from vi_eqp_mc_list
                    $machine = ViEqpMcList::where('mc_no', $mcNo)->first();
                    $line = $machine ? $machine->line : null;
                    $mcType = $machine ? $machine->mc_type : null;
                    $inspectionType = $machine ? $machine->inspection_type : null;

                    // If area is not provided, use the one from the machine lookup
                    if (!$area && $machine) {
                        $area = $machine->area;
                    }

                    // Look up lipas_yn and ham_yn from vi_lipas_models
                    try {
                        $lipasModel = ViLipasModels::where('model_id', $modelId)->first();
                        $lipasYn = $lipasModel ? $lipasModel->lipas_yn : 'N';
                        $hamYn = $lipasModel ? $lipasModel->ham_yn : 'N';
                    } catch (\Exception $e) {
                        // If there's any error with the model lookup, default to 'N'
                        Log::warning("Error looking up model in vi_lipas_models: " . $e->getMessage(), [
                            'model_id' => $modelId
                        ]);
                        $lipasYn = 'N';
                        $hamYn = 'N';
                    }

                    // Calculate week number based on the first Thursday of the year
                    $weekNo = $this->calculateWeekNumber($endtimeDate);

                    // Create a new submitted entry
                    $submittedEntry = new ViProdEndtimeSubmitted();
                    $submittedEntry->lot_id = $lotId;
                    $submittedEntry->model_id = $modelId;
                    $submittedEntry->lot_qty = $lotQty;
                    $submittedEntry->qty_class = $qtyClass;
                    $submittedEntry->chip_size = $chipSize;
                    $submittedEntry->work_type = $workType;
                    $submittedEntry->lot_type = $lotType;
                    $submittedEntry->mc_no = $mcNo;
                    $submittedEntry->line = $line;
                    $submittedEntry->area = $area;
                    $submittedEntry->mc_type = $mcType;
                    $submittedEntry->inspection_type = $inspectionType;
                    $submittedEntry->lipas_yn = $lipasYn;
                    $submittedEntry->ham_yn = $hamYn;
                    $submittedEntry->status = 'SUBMITTED'; // Set status to SUBMITTED by default
                    $submittedEntry->week_no = $weekNo;
                    $submittedEntry->endtime_date = $endtimeDate;

                    // Ensure we're using the current cutoff time for submitted entries
                    $currentCutoffTime = $this->getCurrentCutoffTime();
                    Log::info("Using current cutoff time for submitted entry: {$currentCutoffTime}");
                    $cutoffTime = $currentCutoffTime;
                    $submittedEntry->cutoff_time = $cutoffTime;
                    $submittedEntry->save();

                    $savedEntries[] = $submittedEntry;
                } catch (\Exception $e) {
                    $errors[] = [
                        'index' => $index,
                        'message' => $e->getMessage()
                    ];
                    Log::error("Error saving submitted lot entry: " . $e->getMessage(), [
                        'entry' => $entry,
                        'exception' => $e
                    ]);
                }
            }

            return response()->json([
                'success' => (count($savedEntries) > 0 || count($updatedEntries) > 0),
                'saved_count' => count($savedEntries),
                'updated_count' => count($updatedEntries),
                'error_count' => count($errors),
                'errors' => $errors,
                'duplicate_count' => count($duplicates),
                'duplicates' => $duplicates
            ]);
        } catch (\Exception $e) {
            Log::error("Error in saveSubmitted: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate ISO week number to match Excel's WEEKNUM(date, 14) function
     *
     * @param string $date Date in Y-m-d format
     * @return string Week number padded with leading zero if needed
     */
    private function calculateWeekNumber($date)
    {
        try {
            // Create a DateTime object from the input date
            $dateObj = new \DateTime($date);

            // Use PHP's built-in ISO week calculation but add 1 to match Excel's WEEKNUM
            // Excel's WEEKNUM(date, 14) is ISO-8601 compliant but with a different week numbering
            $weekNumber = (int)$dateObj->format('W') + 1;

            // Ensure the week number is between 1 and 53
            if ($weekNumber > 53) {
                $weekNumber = 1;
            }

            return str_pad($weekNumber, 2, '0', STR_PAD_LEFT);
        } catch (\Exception $e) {
            // Log the error and return a default value
            Log::error("Error calculating week number: " . $e->getMessage(), [
                'date' => $date,
                'exception' => $e
            ]);

            // Return current week number as fallback
            return str_pad(date('W'), 2, '0', STR_PAD_LEFT);
        }
    }

    /**
     * Process WIP data from API request (Pure JavaScript version)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processWipData(Request $request)
    {
        try {
            // Log the start of processing
            Log::info('Processing WIP data via Pure JavaScript API');

            // Debug: Log the entire request for troubleshooting
            Log::info('WIP data request content:', [
                'all' => $request->all(),
                'content_type' => $request->header('Content-Type'),
                'content_length' => $request->header('Content-Length'),
                'content_preview' => substr($request->getContent(), 0, 200) . '...',
                'method' => $request->method(),
                'user_agent' => $request->header('User-Agent'),
                'is_ajax' => $request->ajax()
            ]);

            // Check if we have data in the request content before validation
            $content = $request->getContent();
            $hasDataInContent = false;

            if (!empty($content)) {
                try {
                    $jsonData = json_decode($content, true);
                    if (is_array($jsonData) && (isset($jsonData['wipData']) || isset($jsonData['wip_data']))) {
                        $hasDataInContent = true;
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to parse JSON content during pre-validation: ' . $e->getMessage());
                }
            }

            // Only validate if we don't already have data in the content
            if (!$hasDataInContent) {
                // Validate the request - check for either field name
                $validator = \Validator::make($request->all(), [
                    'wipData' => 'required_without:wip_data|string',
                    'wip_data' => 'required_without:wipData|string',
                ], [
                    'wipData.required_without' => 'The wip data field is required. Please provide WIP data in the request.',
                    'wip_data.required_without' => 'The wip data field is required. Please provide WIP data in the request.'
                ]);

                if ($validator->fails()) {
                    Log::error('Error in processWipData: ' . $validator->errors()->first());
                    return response()->json([
                        'success' => false,
                        'message' => $validator->errors()->first()
                    ], 422);
                }
            }

            // Try to get the data from the request in multiple ways
            $rawWipData = $request->input('wipData') ?? $request->input('wip_data');

            // If still not found, try to parse the raw JSON content
            if (empty($rawWipData)) {
                $content = $request->getContent();
                if (!empty($content)) {
                    try {
                        $jsonData = json_decode($content, true);
                        if (is_array($jsonData)) {
                            $rawWipData = $jsonData['wipData'] ?? $jsonData['wip_data'] ?? null;
                            if (!empty($rawWipData)) {
                                Log::info('WIP data found in raw JSON content');
                            }
                        }
                    } catch (\Exception $e) {
                        Log::warning('Failed to parse JSON content: ' . $e->getMessage());
                    }
                }
            }

            // Log which field was found for debugging purposes
            if ($request->has('wipData')) {
                Log::info('WIP data found in "wipData" field');
            } elseif ($request->has('wip_data')) {
                Log::info('WIP data found in "wip_data" field');
            } elseif (!empty($rawWipData)) {
                Log::info('WIP data found through raw content parsing');
            } else {
                Log::warning('WIP data field not found in any expected location');
            }

            if (empty($rawWipData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data provided. Please paste WIP data into the textarea.',
                    'error_code' => 'NO_DATA',
                    'timestamp' => now()->toISOString()
                ], 400);
            }

            // Parse the raw data (tab-separated values)
            $rows = explode("\n", trim($rawWipData));

            // Check if we have at least one row (header) plus data
            if (count($rows) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid data format. Please ensure you have pasted the correct data.'
                ]);
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
                return response()->json([
                    'success' => false,
                    'message' => 'Missing required columns: ' . implode(', ', $missingColumns)
                ]);
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
                $errors = [];

                for ($i = 1; $i < count($rows); $i++) {
                    $row = trim($rows[$i]);
                    if (empty($row)) continue;

                    try {
                        // Split the row using the same delimiter as the header
                        if ($delimiter) {
                            $columns = explode($delimiter, $row);
                        } else {
                            $columns = preg_split('/\s{2,}/', $row);
                        }

                        // Skip if we don't have enough columns
                        if (count($columns) < count($header)) {
                            $errors[] = [
                                'index' => $i,
                                'message' => 'Row has fewer columns than header'
                            ];
                            continue;
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

                        // Filter out any columns that don't exist in the database table
                        foreach (array_keys($data) as $key) {
                            if (!in_array($key, $tableColumns) && $key !== 'created_at' && $key !== 'updated_at') {
                                unset($data[$key]);
                            }
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
                    } catch (\Exception $e) {
                        $errors[] = [
                            'index' => $i,
                            'message' => $e->getMessage()
                        ];
                        Log::error("Error processing row {$i}: " . $e->getMessage());
                    }
                }

                // Insert any remaining records
                if (!empty($insertData)) {
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

                return response()->json([
                    'success' => true,
                    'message' => 'WIP data processed successfully',
                    'saved_count' => $rowCount,
                    'error_count' => count($errors),
                    'errors' => $errors,
                    'timestamp' => now()->toISOString(),
                    'processing_time' => round(microtime(true) - LARAVEL_START, 2) . 's',
                    'data_summary' => [
                        'total_rows_processed' => $rowCount,
                        'successful_imports' => $rowCount - count($errors),
                        'failed_imports' => count($errors)
                    ]
                ]);

            } catch (\Exception $e) {
                // Rollback the transaction on error if it's active
                if (DB::transactionLevel() > 0) {
                    DB::rollBack();
                }

                Log::error('Error processing WIP data: ' . $e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to process WIP data: ' . $e->getMessage()
                ]);
            }

        } catch (\Exception $e) {
            // Rollback the transaction on error if it's active
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            Log::error('Error in processWipData: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
                'error_code' => 'PROCESSING_ERROR',
                'timestamp' => now()->toISOString(),
                'error_details' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => config('app.debug') ? $e->getTraceAsString() : 'Enable debug mode for detailed trace'
                ]
            ], 500);
        }
    }

    /**
     * Get the current cutoff time based on Manila time
     *
     * @return string Current cutoff time in database format (e.g., '19:00~00:00')
     */
    private function getCurrentCutoffTime()
    {
        // Get current time in Manila
        $now = now()->setTimezone('Asia/Manila');
        $hours = $now->hour;
        $minutes = $now->minute;
        $currentTime = $hours * 60 + $minutes; // Convert to minutes for easier comparison

        // Determine which cutoff period we're currently in
        // No default value - we'll ensure one of the conditions matches
        $currentCutoff = null;

        if ($currentTime >= 0 && $currentTime < 240) {
            // 00:00~04:00 (4AM)
            $currentCutoff = "00:00~04:00";
        } else if ($currentTime >= 240 && $currentTime < 420) {
            // 04:00~07:00 (7AM)
            $currentCutoff = "04:00~07:00";
        } else if ($currentTime >= 420 && $currentTime < 720) {
            // 07:00~12:00 (12NN)
            $currentCutoff = "07:00~12:00";
        } else if ($currentTime >= 720 && $currentTime < 960) {
            // 12:00~16:00 (4PM)
            $currentCutoff = "12:00~16:00";
        } else if ($currentTime >= 960 && $currentTime < 1140) {
            // 16:00~19:00 (7PM)
            $currentCutoff = "16:00~19:00";
        } else {
            // 19:00~00:00 (12MN) - this will catch any time from 19:00 to 23:59
            $currentCutoff = "19:00~00:00";
        }

        Log::info("getCurrentCutoffTime - Current time: {$hours}:{$minutes}, Determined cutoff: {$currentCutoff}");
        return $currentCutoff;
    }
}
