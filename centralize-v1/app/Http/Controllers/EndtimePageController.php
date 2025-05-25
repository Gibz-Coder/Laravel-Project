<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class EndtimePageController extends Controller
{
    /**
     * Display the endtime dashboard page
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get auto-refresh state first with special handling
        if ($request->has('autoRefresh')) {
            // Convert to boolean for consistency (0 or "0" becomes false, 1 or "1" becomes true)
            $autoRefreshValue = $request->input('autoRefresh');
            $autoRefresh = ($autoRefreshValue === '1' || $autoRefreshValue === 1 || $autoRefreshValue === true || $autoRefreshValue === 'true');
            \Log::info("EndtimePageController index - Using autoRefresh from request: " . ($autoRefresh ? 'true' : 'false'));
        } else {
            // Get from session or default to false
            $autoRefresh = Session::get('autoRefresh', false);
            \Log::info("EndtimePageController index - Using autoRefresh from session: " . ($autoRefresh ? 'true' : 'false'));
        }

        // Get filter values from request or session or use defaults
        $date = $request->input('date', Session::get('date', Carbon::today()->format('Y-m-d')));
        $worktype = $request->input('worktype', Session::get('worktype', 'all'));
        $lottype = $request->input('lottype', Session::get('lottype', 'all'));

        // Handle cutoff parameter with special care
        if ($request->has('cutoff')) {
            // If cutoff is explicitly provided in the request, use it
            $cutoff = $request->input('cutoff');
            \Log::info("EndtimePageController index - Using cutoff from request: {$cutoff}");
        } else {
            // Otherwise, get from session or use default
            $cutoff = Session::get('cutoff', 'all');
            \Log::info("EndtimePageController index - Using cutoff from session: {$cutoff}");
        }

        // If auto-refresh is OFF, ensure we respect the user's cutoff selection
        if (!$autoRefresh && $request->has('cutoff')) {
            \Log::info("EndtimePageController index - Auto-refresh is OFF, respecting user's cutoff selection: {$cutoff}");
        }

        // Store filter values in session
        Session::put('date', $date);
        Session::put('cutoff', $cutoff);
        Session::put('worktype', $worktype);
        Session::put('lottype', $lottype);
        Session::put('autoRefresh', $autoRefresh);

        // Log the filter values for debugging
        \Log::info("EndtimePageController index - Using filters:", [
            'date' => $date,
            'cutoff' => $cutoff,
            'worktype' => $worktype,
            'lottype' => $lottype,
            'autoRefresh' => $autoRefresh
        ]);

        // Return the endtime dashboard view
        return view('pages.endtime');
    }
}
