<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Control\Packages\Users\Http\Requests\User\ProcessInviteRequest;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\RedirectResponse;
use Shelter\Guard\Models\User;
use Shelter\Guard\Models\UserInvite;
use Shelter\Kernel\Http\ControllerUsesTrait;

class InviteController extends Controller
{
    use ControllerUsesTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('control.guest');
    }

    /**
     * @param string $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showInviteForm(string $token)
    {
        $invite = UserInvite::whereToken($token)->first();

        return view('control::pages.invite', [
            'invite' => $invite,
        ]);
    }

    /**
     * @param ProcessInviteRequest $request
     * @param string $token
     * @return RedirectResponse
     * @throws \Exception
     */
    public function process(ProcessInviteRequest $request, string $token): RedirectResponse
    {
        /** @var UserInvite|null $invite */
        $invite = UserInvite::whereToken($token)->first();

        if (! $invite) {
            return redirect()->route('control.auth.invite', $token);
        }

        $user = new User([
            'email' => $invite->email,
            'login' => $invite->payload['login'],
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'is_active' => true,
        ]);

        $user->is_admin = $invite->payload['as_admin'];

        $user->password = bcrypt($request->password);

        $user->save();

        if (! empty($invite->payload['roles'])
            && \is_array($invite->payload['roles'])
        ) {
            $user->roles()->sync(
                $invite->payload['roles']
            );
        }

        if (! empty($invite->payload['permissions'])
            && \is_array($invite->payload['permissions'])
        ) {
            $user->permissions()->createMany(
                \array_map(function (string $key) {
                    return [
                        'key' => $key,
                    ];
                }, $invite->payload['permissions'])
            );
        }

        $this->guard()->login($user);

        $invite->delete();

        return redirect()->route('control.index');
    }

    /**
     * @return StatefulGuard
     */
    protected function guard(): StatefulGuard
    {
        return app('shelter.auth');
    }
}
