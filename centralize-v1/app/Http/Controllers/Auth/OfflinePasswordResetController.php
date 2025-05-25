<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class OfflinePasswordResetController extends Controller
{
    /**
     * Show the form for resetting password.
     *
     * @return \Illuminate\View\View
     */
    public function showResetForm()
    {
        return view('auth.reset');
    }

    /**
     * Reset the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        $request->validate([
            'user_id' => 'required|string|exists:users,user_id',
            'date_hired' => 'required|string|date_format:Y-m-d',
            'password' => 'required|string|min:4|confirmed',
        ]);

        // Find the user
        $user = User::where('user_id', $request->user_id)->first();
        
        if (!$user) {
            return back()->with('error', 'Employee ID not found.');
        }
        
        // Format the date_hired from the database to match the input format (YYYY-MM-DD)
        $dbDateHired = Carbon::parse($user->date_hired)->format('Y-m-d');
        $inputDateHired = $request->date_hired;
        
        // Check if the date_hired matches
        if ($dbDateHired !== $inputDateHired) {
            return back()->with('error', 'The hire date does not match our records.');
        }
        
        // Update the password
        $user->password = Hash::make($request->password);
        $user->save();
        
        // Redirect to login page with success message instead of logging in
        return redirect()->route('login')
                         ->with('success', 'Your password has been reset successfully. Please login with your new password.');
    }
}