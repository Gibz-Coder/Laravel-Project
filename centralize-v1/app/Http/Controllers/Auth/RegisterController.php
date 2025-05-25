<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'employee_id' => [
                'required', 
                'string', 
                'max:255', 
                'exists:employees,employee_id',
                // Check if user_id already exists in users table
                function ($attribute, $value, $fail) {
                    if (\App\Models\User::where('user_id', $value)->exists()) {
                        $fail('This Employee ID is already registered. Please login instead.');
                    }
                },
            ],
            'name' => ['required', 'string', 'max:255'],
            'knox_email' => ['nullable', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:4', 'confirmed'], // Changed from min:8 to min:6
        ], [
            'employee_id.exists' => 'Employee ID not found in our records.',
            'password.min' => 'The password field must be at least 4 characters.' // Custom message
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // Get employee data - using correct column names from employees table
        $employee = \DB::table('employees')
            ->where('employee_id', $data['employee_id'])
            ->orWhere('employee_knox', $data['employee_id'])
            ->first();
        
        if (!$employee) {
            throw new \Exception('Employee not found');
        }
        
        // Extract first name (first word of full name)
        $nickName = explode(' ', $data['name'])[0];
        
        // Extract knox_id from knox_email if provided
        $knoxId = null;
        if (!empty($data['knox_email'])) {
            $knoxId = explode('@', $data['knox_email'])[0];
        }
        
        return User::create([
            'user_type' => 'user',
            'user_stat' => 'pending',
            'nick_name' => $nickName,
            'full_name' => $data['name'],
            'user_id' => $data['employee_id'],
            'knox_id' => $knoxId,
            'knox_email' => $data['knox_email'],
            'date_hired' => $employee->date_hired ?? null, // Get date_hired from employee record
            'position' => $employee->position ?? null, // Get position from employee record
            'gender' => $employee->gender ?? null,
            'bio' => null,
            'picture' => null,
            'phone' => null,
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        // Don't log the user in
        // $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 201)
            : redirect()->route('register')
                ->with('success', 'Registration successful! Please wait for admin approval before logging in.');
    }
}
