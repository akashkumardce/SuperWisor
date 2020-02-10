<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Log;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Validator, Input, Redirect, Request;
use View, Auth, DataTables, App\Team;
use App\Providers\LoginServiceProvider;

class LogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLog($action)
    {
        $server = Request::get('server');
        $service = Request::get('service');
        if(!empty($server)){
            return DataTables::of(Log::query()->where("log", $action)
                ->where("server_id",$server)->orderBy('id', 'DESC')->take(10))->make(true);
        }else if(!empty($service)){
            return DataTables::of(Log::query()->where("log", $action)
                ->where("service_id",$service)->orderBy('id', 'DESC')->take(10))->make(true);
        }else{
            return DataTables::of(Log::query()->where("log", $action)->orderBy('id', 'DESC')->take(10))->make(true);
        }
    }
}
