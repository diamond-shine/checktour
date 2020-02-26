<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Validation\ValidationException;
use Shelter\Kernel\Http\ControllerUsesTrait;

use Illuminate\Http\Request;

// use Illuminate\Http\{
//     Request,
//     JsonResponse
// };

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
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');

        $this
            ->middleware('control.guest')
            ->except('logout');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('pages.login');
    }

        /**
     * @param  Request $request
     * @return JsonResponse
     */
    protected function loggedOut(Request $request)
    {
        return response()->json(
            [
                'redirect_url' => url($this->redirectTo),
            ],
            301
        );
    }
}
