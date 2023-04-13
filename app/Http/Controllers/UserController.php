<?php

namespace App\Http\Controllers;

use App\Models\User;


class UserController extends Controller
{
    public function get()
    {
        $user_id = auth()->id();
        $user = User::find($user_id);
        return $user;
    }

}
