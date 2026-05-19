<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginViewResponse as Contract;

class LoginViewResponse implements Contract
{
    public function toResponse($request)
    {
        return view('auth.login');
    }
}
