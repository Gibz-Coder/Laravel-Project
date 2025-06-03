<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LotListController extends Controller
{
    /**
     * Get lot list data for pure JavaScript modal
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLotList(Request $request)
    {
        try {
            // Validate request parameters
            $request->validate([
                'date' => 'required|date',
                'cutoff' => 'required|string',
                'worktype' => 'string',
                'lottype' => 'string',
                'type' => 'string',
                'line' => 'string',
                'page' => 'integer|min:1',
                'per_page' => 'integer|min:1|max:100',
                'sort_field' => 'string',
                'sort_direction' => 'in:asc,desc',
                'search_query' => 'string',
                'selected_line' => 'string',
                'selected_worktype' => 'string',
                'selected_lottype' => 'string',
                'selected_size' => 'string'
            ]);

            $date = $request->input('date');
            $cutoff = $request->input('cutoff', 'all');
            $worktype = $request->input('worktype', 'all');
            $lottype = $request->input('lottype', 'all');
            $type = $request->input('type', 'all');
            $line = $request->input('line');
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 15);
            $sortField = $request->input('sort_field', 'lot_id');
            $sortDirection = $request->input('sort_direction', 'asc');
            $searchQuery = $request->input('search_query', '');
            $selectedLine = $request->input('selected_line', 'ALL');
            $selectedWorkType = $request->input('selected_worktype', 'ALL');
            $selectedLotType = $request->input('selected_lottype', 'ALL');
            $selectedSize = $request->input('selected_size', 'ALL');

            Log::info('LotListController getLotList - Request parameters', [
                'date' => $date,
                'cutoff' => $cutoff,
                'worktype' => $worktype,
                'lottype' => $lottype,
                'type' => $type,
                'line' => $line,
                'page' => $page,
                'per_page' => $perPage,
                'sort_field' => $sortField,
                'sort_direction' => $sortDirection,
                'search_query' => $searchQuery,
                'selected_line' => $selectedLine,
                'selected_worktype' => $selectedWorkType,
                'selected_lottype' => $selectedLotType,
                'selected_size' => $selectedSize
            ]);

            // Build the query
            $query = DB::table('vi_prod_endtime_submitted')
                ->where('endtime_date', $date);

            // Apply cutoff filter
            if ($cutoff !== 'all') {
                if ($cutoff === 'day') {
                    $query->whereIn('cutoff_time', ['07:00~12:00', '12:00~16:00', '16:00~19:00']);
                } elseif ($cutoff === 'night') {
                    $query->whereIn('cutoff_time', ['19:00~00:00', '00:00~04:00', '04:00~07:00']);
                } else {
                    $query->where('cutoff_time', $cutoff);
                }
            }

            // Apply worktype filter
            if ($worktype !== 'all') {
                $query->where('work_type', 'LIKE', '%' . $worktype . '%');
            }

            // Apply lottype filter
            if ($lottype !== 'all') {
                $query->where('lot_type', 'LIKE', '%' . $lottype . '%');
            }

            // Apply line filter (from initial parameters)
            if ($line && $line !== 'ALL') {
                $query->where('line', $line);
            }

            // Apply additional filters
            if ($selectedLine !== 'ALL') {
                $query->where('line', $selectedLine);
            }

            if ($selectedWorkType !== 'ALL') {
                $query->where('work_type', 'LIKE', '%' . $selectedWorkType . '%');
            }

            if ($selectedLotType !== 'ALL') {
                $query->where('lot_type', 'LIKE', '%' . $selectedLotType . '%');
            }

            if ($selectedSize !== 'ALL') {
                $query->where('chip_size', $selectedSize);
            }

            // Apply search filter
            if (!empty($searchQuery)) {
                $query->where(function($q) use ($searchQuery) {
                    $q->where('lot_id', 'LIKE', '%' . $searchQuery . '%')
                      ->orWhere('model_id', 'LIKE', '%' . $searchQuery . '%')
                      ->orWhere('mc_no', 'LIKE', '%' . $searchQuery . '%')
                      ->orWhere('area', 'LIKE', '%' . $searchQuery . '%');
                });
            }

            // Get total count before pagination
            $totalCount = $query->count();

            // Apply sorting
            $query->orderBy($sortField, $sortDirection);

            // Apply pagination
            $offset = ($page - 1) * $perPage;
            $lots = $query->skip($offset)->take($perPage)->get();

            // Transform the results
            $transformedLots = $lots->map(function($lot) {
                return [
                    'lot_id' => $lot->lot_id,
                    'model_id' => $lot->model_id,
                    'lot_qty' => $lot->lot_qty,
                    'line' => $lot->line,
                    'area' => $lot->area,
                    'mc_no' => $lot->mc_no,
                    'chip_size' => $lot->chip_size,
                    'work_type' => $lot->work_type,
                    'lot_type' => $lot->lot_type,
                    'status' => $lot->status ?? 'Submitted',
                    'cutoff_time' => $lot->cutoff_time,
                    'endtime_date' => $lot->endtime_date
                ];
            });

            // Calculate pagination info
            $totalPages = ceil($totalCount / $perPage);
            $hasNextPage = $page < $totalPages;
            $hasPrevPage = $page > 1;

            Log::info('LotListController getLotList - Query completed', [
                'total_count' => $totalCount,
                'current_page' => $page,
                'total_pages' => $totalPages,
                'lots_returned' => $transformedLots->count()
            ]);

            return response()->json([
                'success' => true,
                'data' => $transformedLots,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $totalCount,
                    'total_pages' => $totalPages,
                    'has_next_page' => $hasNextPage,
                    'has_prev_page' => $hasPrevPage,
                    'from' => $offset + 1,
                    'to' => min($offset + $perPage, $totalCount)
                ],
                'filters' => [
                    'date' => $date,
                    'cutoff' => $cutoff,
                    'worktype' => $worktype,
                    'lottype' => $lottype,
                    'type' => $type,
                    'line' => $line,
                    'selected_line' => $selectedLine,
                    'selected_worktype' => $selectedWorkType,
                    'selected_lottype' => $selectedLotType,
                    'selected_size' => $selectedSize,
                    'search_query' => $searchQuery
                ],
                'sorting' => [
                    'field' => $sortField,
                    'direction' => $sortDirection
                ],
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Error in LotListController getLotList: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch lot list: ' . $e->getMessage(),
                'error_code' => 'FETCH_ERROR',
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * Get filter options for the lot list modal
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFilterOptions(Request $request)
    {
        try {
            $request->validate([
                'date' => 'required|date',
                'cutoff' => 'required|string'
            ]);

            $date = $request->input('date');
            $cutoff = $request->input('cutoff', 'all');

            // Build base query
            $query = DB::table('vi_prod_endtime_submitted')
                ->where('endtime_date', $date);

            // Apply cutoff filter
            if ($cutoff !== 'all') {
                if ($cutoff === 'day') {
                    $query->whereIn('cutoff_time', ['07:00~12:00', '12:00~16:00', '16:00~19:00']);
                } elseif ($cutoff === 'night') {
                    $query->whereIn('cutoff_time', ['19:00~00:00', '00:00~04:00', '04:00~07:00']);
                } else {
                    $query->where('cutoff_time', $cutoff);
                }
            }

            // Get available lines
            $lines = $query->clone()->select('line')->distinct()->orderBy('line')->pluck('line')->toArray();
            $availableLines = array_merge(['ALL'], $lines);

            // Get available work types
            $workTypes = $query->clone()->select('work_type')->distinct()->orderBy('work_type')->pluck('work_type')->toArray();
            $availableWorkTypes = array_merge(['ALL'], $workTypes);

            // Get available lot types
            $lotTypes = $query->clone()->select('lot_type')->distinct()->orderBy('lot_type')->pluck('lot_type')->toArray();
            $availableLotTypes = array_merge(['ALL'], $lotTypes);

            // Get available chip sizes
            $chipSizes = $query->clone()->select('chip_size')->distinct()->orderBy('chip_size')->pluck('chip_size')->toArray();
            $availableSizes = array_merge(['ALL'], $chipSizes);

            return response()->json([
                'success' => true,
                'filter_options' => [
                    'lines' => $availableLines,
                    'work_types' => $availableWorkTypes,
                    'lot_types' => $availableLotTypes,
                    'sizes' => $availableSizes
                ],
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Error in LotListController getFilterOptions: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch filter options: ' . $e->getMessage(),
                'error_code' => 'FILTER_OPTIONS_ERROR',
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }
}
