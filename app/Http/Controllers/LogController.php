<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Log;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Validator, Input, Redirect, Request;
use View, Auth,DataTables,App\Team;
use App\Providers\LoginServiceProvider;

class LogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLog($action){
        return DataTables::of(Log::query()->where("log",$action)->orderBy('id', 'DESC')->take(10))->make(true);
    }
}
