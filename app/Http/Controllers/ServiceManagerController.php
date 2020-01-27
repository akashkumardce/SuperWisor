<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Server;
use App\ServerTeam;
use App\Service;
use App\ServiceUser;
use App\Team;
use App\User;
use App\UserTeam;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Validator, Input, Redirect, Request, Illuminate\Database\QueryException;
use View, Auth,DataTables, Illuminate\Validation\Rule, App\Log,App\ServiceServer;
use App\Providers\LoginServiceProvider;

class ServiceManagerController extends Controller
{
    public function showDashboard()
    {
        //$param['roles'] = config('roles.system');
        return View::make('service/dashboard');
    }

    public function getServiceList()
    {
        return DataTables::of(Service::query()->withCount(['server','running']))->make(true);
    }

    public function getServiceServer($serviceId)
    {
        return DataTables::of(ServiceServer::query()->where("service_id",$serviceId)->with(['server','service']))->make(true);
    }

    public function getMasterUsers($serviceId)
    {
        $serviceArr = ServiceServer::query()->where("service_id",$serviceId)->with(['serverteam.team'])->get()->toArray();
        $teamId = [];
        foreach ($serviceArr as $serverArr){
            foreach ($serverArr['serverteam'] as $teamArr){
                $teamId[$teamArr['team_id']] = 1;
            }
        }
        $teamId = array_keys($teamId);
        return DataTables::of(UserTeam::query()->where("team_id",$teamId)->where('role','MASTER_DEVELOPER')->with(['user']))->make(true);
    }
    public function getDeveloperUsers($serviceId)
    {
        $serviceArr = ServiceServer::query()->where("service_id",$serviceId)->with(['serverteam.team'])->get()->toArray();
        $teamId = [];
        foreach ($serviceArr as $serverArr){
            foreach ($serverArr['serverteam'] as $teamArr){
                $teamId[$teamArr['team_id']] = 1;
            }
        }
        $teamId = array_keys($teamId);
        $masterUsers = UserTeam::query()->where("team_id",$teamId)->where('role','MASTER_DEVELOPER')->get()->toArray();
        foreach ($masterUsers as $masterUser){
            $masterUserId[$masterUser['user_id']] = 1;
        }
        $masterUserId = array_keys($masterUserId);

        return DataTables::of(ServiceUser::query()->where("service_id",$serviceId)->whereNotIn('user_id',$masterUserId)->with(['user']))->make(true);
    }
    public function getTeams($serviceId)
    {
        $serviceArr = ServiceServer::query()->where("service_id",$serviceId)->with(['serverteam.team'])->get()->toArray();
        $teamId = [];
        foreach ($serviceArr as $serverArr){
            foreach ($serverArr['serverteam'] as $teamArr){
                $teamId[$teamArr['team_id']] = 1;
            }
        }
        $teamId = array_keys($teamId);
        return DataTables::of(Team::query()->where("id",$teamId))->make(true);
    }


    public function getServiceDetails($serviceId)
    {
        $serviceInfo = Service::query()->where("id",$serviceId)->get()->toArray();
        if (empty($serviceInfo)) {
            return Redirect::to('/service-manage');
        }
        $serviceArr = ServiceServer::query()->where("service_id",$serviceId)->with(['serverteam.team'])->get()->toArray();
        $teamId = [];
        foreach ($serviceArr as $serverArr){
            foreach ($serverArr['serverteam'] as $teamArr){
                $teamId[$teamArr['team_id']] = 1;
            }
        }
        $teamId = array_keys($teamId);

        $masterUsers = UserTeam::query()->where("team_id",$teamId)->where('role','MASTER_DEVELOPER')->get()->toArray();
        foreach ($masterUsers as $masterUser){
            $masterUserId[$masterUser['user_id']] = 1;
        }
        $masterUserId = array_keys($masterUserId);

        $userList = UserTeam::query()->where("team_id",$teamId)->whereNotIn('user_id',$masterUserId)->with(['user'])->get()->all();
        $userListArr = [];
        foreach ($userList as $userInfo){
            $userListArr[$userInfo['user']['id']] = $userInfo['user']['email'];
        }

        $data = array(
            "id" => $serviceId,
            "name" => $serviceInfo[0]["name"],
            "created_at" => $serviceInfo[0]["created_at"],
            "userList" => $userListArr
        );
        return View::make('service/service', $data);
    }

    public function removeDeveloper($serviceId){
        $id = Request::get('id');

        $rules = array(
            'id' => 'required|integer|exists:service_user,id'
        );

        // run the validation rules on the inputs from the form
        $validator = Validator::make(Request::all(), $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return response()->json(['error' => "Developer does not exist"]);
        } else {
            try {
                ServiceUser::query()->where('id',$id)->where('service_id',$serviceId)->delete();

                $Log = new Log();
                $Log->user_id = Auth::user()->id;
                $Log->log = "service";
                $Log->message = Auth::user()->name." deleted - Developer ".Request::get('value')." from service ".$serviceId." at ".date("Y-m-d H:i:s", time());
                $Log->save();
            } catch (QueryException $e) { // It's actually a QueryException but this works too
                return response()->json(['error' => $e->getMessage()]);
            }
            return response()->json(['success' => "Developer Unlinked successfully"]);
        }
    }

    public function linkDeveloper($serviceId)
    {
        $userId = Request::get('id');
        $rules = array(
            'id' => 'required|integer|exists:users,id'
        );

        // run the validation rules on the inputs from the form
        $validator = Validator::make(Request::all(), $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        } else {

            $serviceInfo = Service::query()->where("id",$serviceId)->get()->toArray();
            if (empty($serviceInfo)) {
                return Redirect::to('/service-manage');
            }
            $serviceArr = ServiceServer::query()->where("service_id",$serviceId)->with(['serverteam.team'])->get()->toArray();
            $teamId = [];
            foreach ($serviceArr as $serverArr){
                foreach ($serverArr['serverteam'] as $teamArr){
                    $teamId[$teamArr['team_id']] = 1;
                }
            }
            $teamId = array_keys($teamId);

            $masterUsers = UserTeam::query()->where("team_id",$teamId)->where('role','MASTER_DEVELOPER')->get()->toArray();
            foreach ($masterUsers as $masterUser){
                $masterUserId[$masterUser['user_id']] = 1;
            }
            $masterUserId = array_keys($masterUserId);

            $userList = UserTeam::query()->where("team_id",$teamId)->where('user_id',$userId)->whereNotIn('user_id',$masterUserId)->with(['user'])->get()->all();

            if(empty($userList)){
                return response()->json(['error' => "Developer is not eligible to be in the service"]);
            }


            try {
                $developer = new ServiceUser();
                $developer->user_id = $userId;
                $developer->service_id = $serviceId;
                $developer->created_by = Auth::user()->id;
                $developer->save();

                $Log = new Log();
                $Log->user_id = Auth::user()->id;
                $Log->log = "service";
                $Log->message = Auth::user()->name." Added Developer ".Request::get('value')." for team ".$serviceId." at ".date("Y-m-d H:i:s", time());
                $Log->save();
            } catch (QueryException $e) { // It's actually a QueryException but this works too
                if ($e->getCode() == 23000) {
                    return response()->json(['error' => "Developer Already Exist in the team"]);
                }
                return response()->json(['error' => $e->getMessage()]);
            }
            return response()->json(['success' => "Developer adedd successfully"]);
        }
    }
}
