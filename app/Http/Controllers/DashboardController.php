<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\ServerTeam;
use App\ServiceServer;
use App\UserTeam;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Validator, Input, Redirect, Request;
use View, Auth;
use App\Providers\LoginServiceProvider;

class DashboardController extends Controller
{
    public function showDashbard()
    {
        $userId = Auth::user()->id;

        $teamListArr = UserTeam::query()->where('user_id',$userId)->select(['team_id'])->get()->toArray();
        $teamList = [];
        foreach ($teamListArr as $team){
            $teamList[$team['team_id']]=1;
        }
        $teamListId = array_keys($teamList);
        $serverListArr = ServerTeam::query()->whereIn('team_id',$teamListId)->with(['server'])->get()->toArray();
        $serverList[0]  = "Server";
        foreach ($serverListArr as $server){
            $serverList[$server['server']['id']]=$server['server']['name'];
        }
        $serverId = array_keys($serverList);
        unset($serverId[0]);

        $serviceListArr = ServiceServer::query()->whereIn('server_id',$serverId)->with(['service'])->get()->toArray();
        $serviceList[0]  = "Service";
        foreach ($serviceListArr as $service){
            $serviceList[$service['service']['id']]=$service['service']['name'];
        }

        $data = [];
        $data['serviceList'] = $serviceList;
        $data['serverList'] = $serverList;
        $data['teamList'] = $teamListId;

        return View::make('dashboard/dashboard',$data);
    }

    public function getTeamDetails($team)
    {
        $userList = User::query()->select(['id', 'name', 'email'])->orderBy('id', 'desc')->pluck('email', 'id')->all();
        $serverList = Server::query()->select(['id', 'name', 'ip'])->orderBy('id', 'desc')->pluck('ip', 'id')->all();
        $teamData = Team::query()->where('id',$team)->select(['name','created_at'])->get()->toArray();

        $data = array(
            "id" => $team,
            "name" => $teamData[0]["name"],
            "created_at" => $teamData[0]["created_at"],
            "userList" => $userList,
            "serverList" => $serverList,

        );
        return View::make('team/team', $data);
    }

}
