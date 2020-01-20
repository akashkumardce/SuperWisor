<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Validator, Input, Redirect, Request;
use View, Auth;
use App\Providers\LoginServiceProvider;

class DashboardController extends Controller
{
    public function showDashbard()
    {
        //$states = DB::table("demo_state")->lists("name","id");
        $data = array('serviceList'=>array("0"=>"Select Service","1"=>"Duplicate Service","2"=>"Profile Service"));
        $data['serverList'] = array("0"=>"Select Server","1"=>"192.168.1.1");
        return View::make('dashboard',$data);
    }
}
