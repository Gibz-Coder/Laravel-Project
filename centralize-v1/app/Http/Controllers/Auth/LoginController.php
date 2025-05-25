<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        // In Laravel 12, middleware should be defined in routes or route groups
        // This constructor can remain empty or be removed
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'login';
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $login = $request->input('login');
        $password = $request->input('password');

        // Check if user exists with either user_id or knox_id
        $user = User::where('user_id', $login)->orWhere('knox_id', $login)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'login' => ['No account found with this Employee ID or Knox ID.'],
            ]);
        }

        // Try to authenticate with user_id and check status
        $credentials = ['user_id' => $login, 'password' => $password];
        if ($this->guard()->attempt($credentials, $request->filled('remember'))) {
            $user = $this->guard()->user();
            if ($user->user_stat === 'pending') {
                $this->guard()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                throw ValidationException::withMessages([
                    'login' => ['Your access is ongoing approval, Please follow-up Sr Gibz, Sr JJ or Sr Pombz!'],
                ]);
            }
            return true;
        }

        // Try to authenticate with knox_id and check status
        $credentials = ['knox_id' => $login, 'password' => $password];
        if ($this->guard()->attempt($credentials, $request->filled('remember'))) {
            $user = $this->guard()->user();
            if ($user->user_stat === 'pending') {
                $this->guard()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                throw ValidationException::withMessages([
                    'login' => ['Your access is ongoing approval, Please follow-up Sr Gibz, Sr JJ or Sr Pombz!'],
                ]);
            }
            return true;
        }

        // If we get here, the user exists but password is wrong
        throw ValidationException::withMessages([
            'password' => ['The password you entered is incorrect.'],
        ]);
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'password' => [trans('auth.failed')],
        ]);
    }
}
