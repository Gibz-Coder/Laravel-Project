<?php

namespace App\Services;

use App\Models\ViProdEndtimeSubmitted;
use App\Models\ViCapaRef;
use Illuminate\Support\Facades\Log;

class EndtimeDashboardService
{
    /**
     * Get target capacity based on filters
     *
     * @param string $date
     * @param string $cutoff
     * @param string $worktype
     * @param string $lottype
     * @return array Returns an array with 'total', 'count', and 'percentage' keys
     */
    public static function getTargetCapacity($date, $cutoff, $worktype, $lottype)
    {
        try {
            // Ensure worktype and cutoff are strings
            if (is_array($worktype)) {
                $worktype = $worktype['worktype'] ?? 'all';
            }

            if (is_array($cutoff)) {
                $cutoff = $cutoff['cutoff'] ?? 'all';
            }

            // Force to string type
            $worktype = (string)$worktype;
            $cutoff = (string)$cutoff;

            // Log the values for debugging
            Log::info("EndtimeDashboardService getTargetCapacity - Using worktype: '$worktype', cutoff: '$cutoff'");

            // Query the database using the model with the worktype filter
            $query = ViCapaRef::query();

            // Apply worktype filtering using the scope in ViCapaRef model
            $query->filterByWorktype($worktype);

            // Get the SQL query for debugging
            $sql = $query->toSql();
            $bindings = $query->getBindings();
            Log::info("SQL Query: $sql with bindings: " . json_encode($bindings));

            // Get the results
            $results = $query->get();

            // Log the number of results
            Log::info("Number of results: " . $results->count());

            // Calculate the total capacity
            $totalCapacity = $results->sum('actual_capa');
            Log::info("Total capacity before cutoff division: $totalCapacity");

            // Count the number of machines (count distinct mc_no values)
            $machineCount = $results->pluck('mc_no')->unique()->count();
            Log::info("Machine count: {$machineCount}");

            // Apply cutoff division logic
            $finalCapacity = $totalCapacity;
            if (in_array($cutoff, ['00:00~04:00', '04:00~07:00', '07:00~12:00', '12:00~16:00', '16:00~19:00', '19:00~00:00'])) {
                // If any of the 6 cutoffs is selected, divide by 6 (16.6666%)
                $finalCapacity = round($totalCapacity / 6);
                Log::info("Applied cutoff division (1/6): {$finalCapacity}");
            } elseif (in_array($cutoff, ['day', 'night'])) {
                // If Day or Night is selected, divide by 2 (50%)
                $finalCapacity = round($totalCapacity / 2);
                Log::info("Applied cutoff division (1/2): {$finalCapacity}");
            } else {
                // If "all" is selected, use the total
                $finalCapacity = $totalCapacity;
                Log::info("Using total capacity (all cutoff): {$finalCapacity}");
            }

            // Calculate percentage (default to 100% for now)
            $percentage = 100;

            // Return the data as an array
            return [
                'total' => (int)$finalCapacity,
                'count' => $machineCount,
                'percentage' => $percentage
            ];
        } catch (\Exception $e) {
            Log::error("Error in getTargetCapacity: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());

            // Return default values in case of error
            return [
                'total' => 0,
                'count' => 0,
                'percentage' => 0
            ];
        }
    }

    /**
     * Get endtime data with filters
     *
     * @param string $date
     * @param string $cutoff
     * @param string $worktype
     * @param string $lottype
     * @return array
     */
    public static function getEndtimeData($date, $cutoff, $worktype, $lottype)
    {
        try {
            // Log the input parameters
            Log::info("getEndtimeData called with parameters:", [
                'date' => $date,
                'cutoff' => $cutoff,
                'worktype' => $worktype,
                'lottype' => $lottype
            ]);

            // Query the database using our model
            $query = ViProdEndtimeSubmitted::query();

            // Apply date filter - ensure it's using the correct date format
            if ($date) {
                // Format the date to ensure consistency
                $formattedDate = date('Y-m-d', strtotime($date));
                Log::info("Applying date filter with formatted date: $formattedDate");
                $query->whereDate('endtime_date', $formattedDate);
            }

            // Apply worktype filter
            if ($worktype && $worktype !== 'all' && $worktype !== 'Worktype - all') {
                Log::info("Applying worktype filter: $worktype");
                $query->where('work_type', $worktype);
            }

            // Apply lottype filter
            if ($lottype && $lottype !== 'all' && $lottype !== 'Lottype - all') {
                Log::info("Applying lottype filter: $lottype");
                $query->where('lot_type', $lottype);
            }

            // Apply cutoff filter
            if ($cutoff && $cutoff !== 'all') {
                if ($cutoff === 'day') {
                    Log::info("Applying day cutoff filter");
                    $query->whereIn('cutoff_time', ['07:00~12:00', '12:00~16:00', '16:00~19:00']);
                } elseif ($cutoff === 'night') {
                    Log::info("Applying night cutoff filter");
                    $query->whereIn('cutoff_time', ['00:00~04:00', '04:00~07:00', '19:00~00:00']);
                } else {
                    Log::info("Applying specific cutoff filter: $cutoff");
                    $query->where('cutoff_time', $cutoff);
                }
            }

            // Apply status filter - case insensitive "pending" and "submitted"
            Log::info("Applying status filter: pending and submitted (case insensitive)");
            $query->byStatus(['pending', 'submitted']);

            // Log the full query for debugging
            $fullSql = vsprintf(str_replace(['?'], ['\'%s\''], $query->toSql()), $query->getBindings());
            Log::info("Full SQL Query: $fullSql");

            // Get the SQL query for debugging
            $sql = $query->toSql();
            $bindings = $query->getBindings();
            Log::info("SQL Query: $sql with bindings: " . json_encode($bindings));

            // Calculate total from lot_qty column
            $total = $query->sum('lot_qty');

            // Count the number of lots
            $count = $query->count();

            // Get target capacity for percentage calculation
            $targetData = self::getTargetCapacity($date, $cutoff, $worktype, $lottype);
            $targetCapacity = $targetData['total'];

            // Calculate percentage
            $percentage = $targetCapacity > 0 ? round(($total / $targetCapacity) * 100, 1) : 0;

            Log::info("EndtimeCard data - Total: $total, Count: $count, Percentage: $percentage");

            return [
                'total' => $total,
                'count' => $count,
                'percentage' => $percentage
            ];
        } catch (\Exception $e) {
            Log::error("Error in getEndtimeData: " . $e->getMessage());
            return [
                'total' => 0,
                'count' => 0,
                'percentage' => 0
            ];
        }
    }

    /**
     * Get submitted data with filters (only status = 'submitted')
     *
     * @param string $date
     * @param string $cutoff
     * @param string $worktype
     * @param string $lottype
     * @return array
     */
    public static function getSubmittedData($date, $cutoff, $worktype, $lottype)
    {
        try {
            // Log the input parameters
            Log::info("getSubmittedData called with parameters:", [
                'date' => $date,
                'cutoff' => $cutoff,
                'worktype' => $worktype,
                'lottype' => $lottype
            ]);

            // Query the database using our model
            $query = ViProdEndtimeSubmitted::query();

            // Apply date filter - ensure it's using the correct date format
            if ($date) {
                // Format the date to ensure consistency
                $formattedDate = date('Y-m-d', strtotime($date));
                Log::info("Applying date filter with formatted date: $formattedDate");
                $query->whereDate('endtime_date', $formattedDate);
            }

            // Apply worktype filter
            if ($worktype && $worktype !== 'all' && $worktype !== 'Worktype - all') {
                Log::info("Applying worktype filter: $worktype");
                $query->where('work_type', $worktype);
            }

            // Apply lottype filter
            if ($lottype && $lottype !== 'all' && $lottype !== 'Lottype - all') {
                Log::info("Applying lottype filter: $lottype");
                $query->where('lot_type', $lottype);
            }

            // Apply cutoff filter
            if ($cutoff && $cutoff !== 'all') {
                if ($cutoff === 'day') {
                    Log::info("Applying day cutoff filter");
                    $query->whereIn('cutoff_time', ['07:00~12:00', '12:00~16:00', '16:00~19:00']);
                } elseif ($cutoff === 'night') {
                    Log::info("Applying night cutoff filter");
                    $query->whereIn('cutoff_time', ['00:00~04:00', '04:00~07:00', '19:00~00:00']);
                } else {
                    Log::info("Applying specific cutoff filter: $cutoff");
                    $query->where('cutoff_time', $cutoff);
                }
            }

            // Apply status filter - case insensitive "submitted" only
            Log::info("Applying status filter: submitted (case insensitive)");
            $query->byStatus('submitted');

            // Log the full query for debugging
            $fullSql = vsprintf(str_replace(['?'], ['\'%s\''], $query->toSql()), $query->getBindings());
            Log::info("Full SQL Query: $fullSql");

            // Calculate total from lot_qty column
            $total = $query->sum('lot_qty');

            // Count the number of lots
            $count = $query->count();

            // Get target capacity for percentage calculation
            $targetData = self::getTargetCapacity($date, $cutoff, $worktype, $lottype);
            $targetCapacity = $targetData['total'];

            // Calculate percentage
            $percentage = $targetCapacity > 0 ? round(($total / $targetCapacity) * 100, 1) : 0;

            Log::info("SubmittedCard data - Total: $total, Count: $count, Percentage: $percentage");

            return [
                'total' => $total,
                'count' => $count,
                'percentage' => $percentage
            ];
        } catch (\Exception $e) {
            Log::error("Error in getSubmittedData: " . $e->getMessage());
            return [
                'total' => 0,
                'count' => 0,
                'percentage' => 0
            ];
        }
    }

    /**
     * Get remaining data with filters (only status = 'pending')
     *
     * @param string $date
     * @param string $cutoff
     * @param string $worktype
     * @param string $lottype
     * @return array
     */
    public static function getRemainingData($date, $cutoff, $worktype, $lottype)
    {
        try {
            // Log the input parameters
            Log::info("getRemainingData called with parameters:", [
                'date' => $date,
                'cutoff' => $cutoff,
                'worktype' => $worktype,
                'lottype' => $lottype
            ]);

            // Query the database using our model
            $query = ViProdEndtimeSubmitted::query();

            // Apply date filter - ensure it's using the correct date format
            if ($date) {
                // Format the date to ensure consistency
                $formattedDate = date('Y-m-d', strtotime($date));
                Log::info("Applying date filter with formatted date: $formattedDate");
                $query->whereDate('endtime_date', $formattedDate);
            }

            // Apply worktype filter
            if ($worktype && $worktype !== 'all' && $worktype !== 'Worktype - all') {
                Log::info("Applying worktype filter: $worktype");
                $query->where('work_type', $worktype);
            }

            // Apply lottype filter
            if ($lottype && $lottype !== 'all' && $lottype !== 'Lottype - all') {
                Log::info("Applying lottype filter: $lottype");
                $query->where('lot_type', $lottype);
            }

            // Apply cutoff filter
            if ($cutoff && $cutoff !== 'all') {
                if ($cutoff === 'day') {
                    Log::info("Applying day cutoff filter");
                    $query->whereIn('cutoff_time', ['07:00~12:00', '12:00~16:00', '16:00~19:00']);
                } elseif ($cutoff === 'night') {
                    Log::info("Applying night cutoff filter");
                    $query->whereIn('cutoff_time', ['00:00~04:00', '04:00~07:00', '19:00~00:00']);
                } else {
                    Log::info("Applying specific cutoff filter: $cutoff");
                    $query->where('cutoff_time', $cutoff);
                }
            }

            // Apply status filter - case insensitive "pending" only
            Log::info("Applying status filter: pending (case insensitive)");
            $query->byStatus('pending');

            // Log the full query for debugging
            $fullSql = vsprintf(str_replace(['?'], ['\'%s\''], $query->toSql()), $query->getBindings());
            Log::info("Full SQL Query: $fullSql");

            // Calculate total from lot_qty column
            $total = $query->sum('lot_qty');

            // Count the number of lots
            $count = $query->count();

            // Get target capacity for percentage calculation
            $targetData = self::getTargetCapacity($date, $cutoff, $worktype, $lottype);
            $targetCapacity = $targetData['total'];

            // Calculate percentage
            $percentage = $targetCapacity > 0 ? round(($total / $targetCapacity) * 100, 1) : 0;

            Log::info("RemainingCard data - Total: $total, Count: $count, Percentage: $percentage");

            return [
                'total' => $total,
                'count' => $count,
                'percentage' => $percentage
            ];
        } catch (\Exception $e) {
            Log::error("Error in getRemainingData: " . $e->getMessage());
            return [
                'total' => 0,
                'count' => 0,
                'percentage' => 0
            ];
        }
    }
}
