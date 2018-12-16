<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Http\Controllers\Backend\BackendController;
use Illuminate\Foundation\Auth\ResetsPasswords;

use Illuminate\Http\Request;

class PasswordController extends BackendController
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

    use ResetsPasswords;

    protected $linkRequestView = 'backend.auth.reset_password';
    protected $resetView       = 'backend.auth.reset_link';

    protected $redirectPath    = 'backend/dashboard'; // for reset password

    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');

        config(['auth.passwords.users.email' => 'backend.emails.password']);
    }

    public function resetPasswordAction(Request $request, $token = null) {
        if ( $request->isMethod('GET') )
            return $this->showResetForm($request, $token);

        return $this->postEmail( $request );
    }
}
