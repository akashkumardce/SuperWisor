<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Providers\ServiceManagerProvider;
use App\Providers\SupervisordServiceProvider;
use App\Server;
use App\ServerTeam;
use App\Service;
use App\ServiceUser;
use App\Team;
use App\User;
use App\UserTeam;
use fXmlRpc\Exception\FaultException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Validator, Input, Redirect, Request, Illuminate\Database\QueryException;
use View, Auth,DataTables, Illuminate\Validation\Rule, App\Log,App\ServiceServer;
use App\Providers\LoginServiceProvider;

class ServiceManagerController extends Controller
{
    public function showDashboard()
    {
        return View::make('service/dashboard');
    }

    public function getServiceList()
    {
        $serviceArr = ServiceUser::query()->where("user_id",Auth::user()->id)
            ->select("service_id")->get()->toArray();
        foreach ($serviceArr as $service){
             $serviceId[$service['service_id']] = 1;
        }
        $serviceId = array_keys($serviceId);

        //UserTeam
        $userTeamArr = UserTeam::query()->where("user_id",Auth::user()->id)
            ->where("role",config('roles.team.MASTER_DEVELOPER'))
            ->select("team_id")->get()->toArray();
        $teamId = [];
        foreach ($userTeamArr as $team){
            $teamId[$team['team_id']] = 1;
        }
        $teamId = array_keys($teamId);

        $serverTeamArr = ServerTeam::query()->whereIn("team_id",$teamId)->select("server_id")->get()->toArray();
        $serverId = [];
        foreach ($serverTeamArr as $serverTeam){
            $serverId[$serverTeam['server_id']] = 1;
        }
        $serverId = array_keys($serverId);

        $serviceServerArr = ServiceServer::query()->whereIn("server_id",$serverId)
            ->select("service_id")->get()->toArray();
        $serviceObjId = [];
        foreach ($serviceServerArr as $serviceServer){
            $serviceObjId[$serviceServer['service_id']] = 1;
        }
        $serviceObjId = array_keys($serviceObjId);

        $serviceObjId[] =  $serviceId;



        return DataTables::of(Service::query()->whereIn("id",$serviceObjId)
            ->withCount(['server','running']))->make(true);
    }

    public function getServiceServer($serviceId)
    {
        return DataTables::of(ServiceServer::query()->where("service_id",$serviceId)
            ->with(['server','service','startby','stopby']))
            ->make(true);
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
        return DataTables::of(UserTeam::query()->whereIn("team_id",$teamId)->where('role','MASTER_DEVELOPER')->with(['user']))->make(true);
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
        $masterUserId = [];
        $teamId = array_keys($teamId);
        $masterUsers = UserTeam::query()->whereIn("team_id", $teamId)->where('role', 'MASTER_DEVELOPER')->get()->toArray();
        foreach ($masterUsers as $masterUser) {
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
        return DataTables::of(Team::query()->whereIn("id",$teamId))->make(true);
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

        $masterUserId = [];
        $masterUsers = UserTeam::query()->whereIn("team_id", $teamId)->where('role', 'MASTER_DEVELOPER')->get()->toArray();
        foreach ($masterUsers as $masterUser) {
            $masterUserId[$masterUser['user_id']] = 1;
        }

        $masterUserId = array_keys($masterUserId);

        $userListArr = [];
        $userList = UserTeam::query()->whereIn("team_id", $teamId)->whereNotIn('user_id', $masterUserId)->with(['user'])->get()->toArray();
        foreach ($userList as $userInfo) {
            $userListArr[$userInfo['user']['id']] = $userInfo['user']['email'];
        }

        if(empty($userListArr)){
            $userListArr[] = "No Developers or Teams added to the servers";
        }
        $isMaster = in_array(Auth::user()->id,$masterUserId);

        $data = array(
            "id" => $serviceId,
            "name" => $serviceInfo[0]["name"],
            "created_at" => $serviceInfo[0]["created_at"],
            "userList" => $userListArr,
            "isMaster"=>$isMaster?true:false
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

            $masterUsers = UserTeam::query()->whereIn("team_id",$teamId)->where('role','MASTER_DEVELOPER')->get()->toArray();
            foreach ($masterUsers as $masterUser){
                $masterUserId[$masterUser['user_id']] = 1;
            }
            $masterUserId = array_keys($masterUserId);

            $userList = UserTeam::query()->whereIn("team_id",$teamId)->where('user_id',$userId)->whereNotIn('user_id',$masterUserId)->with(['user'])->get()->all();

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

    public function perform(){
        $action = Request::get('action');
        $serviceId = Request::get('service');
        $serverId = Request::get('server');

        $service = Service::query()->find($serviceId);
        //Service Permission
        if($action!="logs") {
            $developerExist = ServiceUser::query()->where(["service_id" => $serviceId, "user_id" => Auth::user()->id])->count();
            $serverTeams = ServerTeam::query()->where(["server_id" => $serverId])->select("team_id")->get()->toArray();
            $teamList = [];
            foreach ($serverTeams as $serverTeam) {
                $teamList[$serverTeam['team_id']] = 1;
            }
            $teamList = array_keys($teamList);
            $masterExist = UserTeam::query()->whereIn("team_id", $teamList)
                ->where(["user_id" => Auth::user()->id, "role" => config('roles.team.MASTER_DEVELOPER')])->count();

            if ($developerExist == 0 && $masterExist == 0) {
                return response()->json(['error' => "You dont have access On Service"]);
            }
        }

        $server = Server::query()->find($serverId);
        $serviceServer = ServiceServer::query()->where("server_id",$serverId)->where("service_id",$serviceId)->limit(1)->get();

        $Log = new Log();
        $Log->user_id = Auth::user()->id;
        $Log->log = "action";
        $Log->server_id = $server->id;
        $Log->service_id = $service->id;


        if(empty($server) || empty($service) || empty($serviceServer)){
            return response()->json(['error' => "Service or Server Invalid"]);
        }
        try {
            $supervisor = new SupervisordServiceProvider($server);
            $response = $supervisor->perform($serviceServer[0],$action);
            $Log->message = " SUCCESSFULLY $action Service ".$service->name. " on ". $server->name;
            if($action!="logs") {
                $Log->save();
            }
        }catch(FaultException $e){
            if($action!="logs") {
                $Log->message = " FAILED $action Service " . $service->name . " on " . $server->name . "--" . $e->getMessage();
                $Log->save();
            }
            return response()->json(['error' => $e->getMessage()]);
        }

        return response()->json(['success' => "Service ".$action." successfully",
            "message"=>$response]);


    }
}
