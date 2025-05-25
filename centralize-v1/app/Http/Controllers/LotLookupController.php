<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\ViProdWipRealtime;

class LotLookupController extends Controller
{
    /**
     * Look up lot information from the database
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function lookup(Request $request)
    {
        $lotId = $request->input('lotId');
        
        Log::info("LotLookupController lookup - Looking up lot: {$lotId}");
        
        if (!$lotId) {
            Log::error("LotLookupController lookup - No lot ID provided");
            return response()->json([
                'success' => false,
                'message' => 'No lot ID provided'
            ]);
        }
        
        try {
            // Find the lot in the database
            $lot = ViProdWipRealtime::findByLotId($lotId);
            
            if ($lot) {
                Log::info("LotLookupController lookup - Found lot: {$lotId}", [
                    'model_id' => $lot->model_id,
                    'lot_qty' => $lot->lot_qty
                ]);
                
                // Return the lot information
                return response()->json([
                    'success' => true,
                    'model_id' => $lot->model_id,
                    'lot_qty' => $lot->lot_qty
                ]);
            } else {
                Log::info("LotLookupController lookup - Lot not found: {$lotId}");
                
                // Return an error
                return response()->json([
                    'success' => false,
                    'message' => 'Lot not found'
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Error in LotLookupController lookup: " . $e->getMessage(), [
                'exception' => $e
            ]);
            
            // Return an error
            return response()->json([
                'success' => false,
                'message' => 'Error looking up lot: ' . $e->getMessage()
            ], 500);
        }
    }
}
