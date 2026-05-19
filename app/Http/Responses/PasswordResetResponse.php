<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\PasswordResetResponse as Contract;

class PasswordResetResponse implements Contract
{
    public function toResponse($request)
    {
        return view('auth.login')->with('status', 'Password reset link sent!');
    }
}
