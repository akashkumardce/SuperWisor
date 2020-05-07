<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Log;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Validator, Input, Redirect, Request;
use View, Auth, DataTables, Config, App\Team;
use App\Providers\LoginServiceProvider;

class AdminManagerController extends Controller
{
    public function show()
    {
        $data = [];
        $data['config'] = config();
        $authDrivers = array_keys($data['config']['login']['config']);
        $authDriversArr = [];
        foreach($authDrivers as $auth){
            $authDriversArr[$auth] =  $data['config']['login']['config'][$auth]['desc'];
        }
        $data['authDriversSelected'] = config('login.config.driver');
        $data['authDrivers'] = $authDriversArr;
        return View::make('admin/dashboard',$data);
    }

    public function change()
    {
        $authorization = Request::get('authorization');
        Config::set('login.config.driver', $authorization);
        $a = config('login.config.driver');
        print_r($a);

        $validator = Validator::make([], []);
        $validator->errors()->add('authorization', 'Unable to connect to '.$a);
        return Redirect::to('/admin')
            ->withErrors($validator)// send back all errors to the login form
            ->with('success', 'No Server addedd successfully - '.$a)
            ->withInput();
    }
}
