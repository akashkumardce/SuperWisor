<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Team;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Validator, Input, Redirect, Request;
use View, Auth,DataTables;
use App\Providers\LoginServiceProvider;

class UserManagerController extends Controller
{
    public function showDashbard()
    {
        return View::make('user/dashboard');
    }

    public function getUserList()
    {
        return DataTables::of(User::query())->make(true);
    }
}
