<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
// use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    // use ResetsPasswords;

    protected $redirectTo = '/student/exams';

    public function showResetForm(\Illuminate\Http\Request $request, $token = null)
    {
        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    public function reset(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $response = \Illuminate\Support\Facades\Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->setRememberToken(\Illuminate\Support\Str::random(60));
                $user->save();

                event(new \Illuminate\Auth\Events\PasswordReset($user));

                $this->guard()->login($user);
            }
        );

        return $response == \Illuminate\Support\Facades\Password::PASSWORD_RESET
            ? redirect($this->redirectPath())->with('status', __($response))
            : back()->withErrors(['email' => __($response)]);
    }

    protected function guard()
    {
        return \Illuminate\Support\Facades\Auth::guard();
    }

    public function redirectPath()
    {
        return $this->redirectTo;
    }
}
