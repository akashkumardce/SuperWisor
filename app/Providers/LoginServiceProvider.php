<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\User;

class LoginServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public static function attempt($userdata)
    {
        if(config("login.client")=="LDAP"){
            $user = User::where('email', $userdata['email'])
                ->where('password', $userdata['password'])
                ->first();
        }else {
            $user = User::where('email', $userdata['email'])
                ->where('password', $userdata['password'])
                ->first();
        }
        return $user;
    }
}
