<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        $this->setSessionCustomer();
        $this->setUserDefaultTimezone($request, $user);
        $this->setMultipleLoginWarning();
    }

    private function setSessionCustomer(): void
    {
        if (app()->user->getCustomers()->count() === 1) {
            app()->user->setSessionCustomer(app()->user->getCustomers()->first());
        } else {
            app()->user->removeSessionCustomer();
        }
    }

    /**
     * Set default user
     */
    private function setUserDefaultTimezone($request, $user)
    {
        app()->user->setDefaultTimezoneInUserSetting($request, $user);
    }

    private function setMultipleLoginWarning()
    {
        $checkAt = now()->subMinutes(env('LOGIN_SESSION_CHECK_MINUTES', 30));

        $userActiveSessions = DB::table('sessions')->where('user_id', Auth::id())
            ->where('id', '!=', Session::getId())
            ->where('last_activity', '>=', $checkAt->toTimeString())
            ->get();

        if (!empty($userActiveSessions) && count($userActiveSessions)) {
//            session()->flash('multiple_login', true);
        }
    }

    protected function loggedOut(Request $request)
    {
        return redirect('login');
    }
}
