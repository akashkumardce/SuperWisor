<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Server;
use App\ServerTeam;
use App\Team;
use App\User;
use App\UserTeam;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Validator, Input, Redirect, Request;
use View, Auth,DataTables, Illuminate\Validation\Rule, App\Log,App\ServiceServer;
use App\Providers\LoginServiceProvider;
use App\Providers\SupervisordServiceProvider;

class ServerManagerController extends Controller
{
    public function __construct() {
        Validator::extend("ips", function($attribute, $value, $parameters) {
            $rules = [
                'ip' => 'required|ip|unique:servers,ip',
            ];
            $value = explode(",",$value);
            foreach ($value as $ip) {
                $data = [
                    'ip' => $ip
                ];
                $validator = Validator::make($data, $rules);
                if ($validator->fails()) {
                    return false;
                }
            }
            return true;
        });
    }
    public function showDashboard()
    {
        //$param['roles'] = config('roles.system');
        return View::make('server/dashboard');
    }

    public function getServerList()
    {
        return DataTables::of(Server::query()->with(['creator'])->withCount([
            'team',
            'service',
            'running' => function ($query) {
                $query->where('status',config('status.service.RUNNING'));
            },
            'error' => function ($query) {
                $query->where('status',config('status.service.FATAL'));
            }]))->make(true);
    }

    public function getServerTeams($serverId)
    {
        return DataTables::of(ServerTeam::query()->where("server_id",$serverId)->with(['team']))->make(true);
    }

    public function getServerServices($serverId)
    {
        return DataTables::of(ServiceServer::query()->where("server_id",$serverId)->with(['server','service']))->make(true);
    }

    public function getServerDetails($serverId)
    {
        $userInfo = Server::query()->where("id",$serverId)->get()->toArray();
        if (empty($userInfo)) {
            return Redirect::to('/server-manage');
        }

        $data = array(
            "id" => $serverId,
            "name" => $userInfo[0]["name"],
            "created_at" => $userInfo[0]["created_at"],
            "created_by" => $userInfo[0]["created_by"],
        );
        return View::make('server/server', $data);
    }

    public function addServer(){
        $ips = Request::get('ip');
        $passsword = Request::get('password');

        $rules = array(
            'ip' => 'required|min:5|ips',
            'password' => 'required|min:5'
        );

        // run the validation rules on the inputs from the form
        $validator = Validator::make(Request::all(), $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return Redirect::to('/server-manage')
                ->withErrors($validator)// send back all errors to the login form
                ->withInput();
        } else {
            $value = explode(",",$ips);
            foreach ($value as $ip) {

                try {
                    $server = new Server();
                    $server->name = $ip;
                    $server->ip = $ip;
                    $server->port = 9001;
                    $server->username = 'superwisor';
                    $server->password = $passsword;
                    $server->created_by = Auth::user()->id;
                    $supervisord = $this->validateServerConnection($server);
                    $server->save();
                    $this->fetchServices($supervisord,$server);

                } catch (QueryException $e) { // It's actually a QueryException but this works too
                    echo $e->getMessage();
                    die;
                    $validator = Validator::make([], []);
                    if ($e->getCode() == 23000) {
                        $validator->errors()->add('ip', 'Few Servers Already Exist in the IP List');
                    } else {
                        $validator->errors()->add('ip', $e->getMessage());
                    }
                    return Redirect::to('/user-manage')
                        ->withErrors($validator)// send back all errors to the login form
                        ->withInput();
                } catch (\Exception $e){
                    $validator->errors()->add('ip', 'Unable to connect to '.$ip);
                    return Redirect::to('/server-manage')
                        ->withErrors($validator)// send back all errors to the login form
                        ->withInput();
                }
                $success=1;
            }
            if($success) {
                $Log = new Log();
                $Log->user_id = Auth::user()->id;
                $Log->log = "server";
                $Log->message = Auth::user()->name . " Added Server " . $ips . " at " . date("Y-m-d H:i:s", time());
                $Log->save();
                return Redirect::to('/servers/' . $server->id);
            }else{
                $validator = Validator::make([], []);
                $validator->errors()->add('ip', "unable to add servers");
                return Redirect::to('/user-manage')
                    ->withErrors($validator)// send back all errors to the login form
                    ->withInput();
            }
        }
    }

    public function removeServer($serverId){
        $id = Request::get('id');

        $rules = array(
            'id' => 'required|integer|exists:servers,id|unique:server_team,server_id'
        );

        // run the validation rules on the inputs from the form
        $validator = Validator::make(Request::all(), $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return response()->json(['error' => "Only a server with no Team working on it can be deleted"]);
        } else {
            try {
                Server::query()->where('id',$id)->where('id',$serverId)->delete();

                $Log = new Log();
                $Log->user_id = Auth::user()->id;
                $Log->log = "server";
                $Log->message = Auth::user()->name." Deleted Server ".Request::get('value')." at ".date("Y-m-d H:i:s", time());
                $Log->save();
            } catch (QueryException $e) { // It's actually a QueryException but this works too
                return response()->json(['error' => $e->getMessage()]);
            }
            return response()->json(['success' => "Server Deleted successfully"]);
        }
    }

    public function removeTeam($serverId)
    {
        $id = Request::get('id');
        $rules = array(
            'id' => 'required|integer|exists:server_team,id'
        );

        // run the validation rules on the inputs from the form
        $validator = Validator::make(Request::all(), $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        } else {
            try {
                ServerTeam::query()->where('id',$id)->where('server_id',$serverId)->delete();

                $Log = new Log();
                $Log->user_id = Auth::user()->id;
                $Log->log = "server";
                $Log->message = Auth::user()->name." Deleted Team ".Request::get('value')." from server ".$serverId." at ".date("Y-m-d H:i:s", time());
                $Log->save();
            } catch (QueryException $e) { // It's actually a QueryException but this works too
                return response()->json(['error' => $e->getMessage()]);
            }
            return response()->json(['success' => "Team Unlinked successfully"]);
        }
    }

    public function updateServer($serverId){
        $id = $serverId;
        $password = Request::get('password');

        $rules = array(
            'password' => 'required|min:5',
        );

        // run the validation rules on the inputs from the form
        $validator = Validator::make(Request::all(), $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return Redirect::to('/servers/'.$serverId)
                ->withErrors($validator)// send back all errors to the login form
                ->withInput();
        } else {
            try {
                Server::query()->where('id',$id)->where('id',$serverId)->update(["password"=>$password]);

                $Log = new Log();
                $Log->user_id = Auth::user()->id;
                $Log->log = "server";
                $Log->message = Auth::user()->name." Updated password of ".Request::get('value')." at ".date("Y-m-d H:i:s", time());
                $Log->save();
            } catch (QueryException $e) {
                $validator = Validator::make([], []);
                $validator->errors()->add('password', $e->getMessage());
                return Redirect::to('/servers/'.$serverId)
                    ->withErrors($validator)// send back all errors to the login form
                    ->withInput();
            }
            return Redirect::to('/servers/'.$serverId)->with('success', 'Password updated successfully!!');;
        }
    }

    private function validateServerConnection(Server $server)
    {
        $supervisor = new SupervisordServiceProvider($server);
        if(!$supervisor->isConnected()){
            throw new \Exception('Not able to connect');
        }
        $runningProcessess = $supervisor->getProcess();
        //TODO- save running processess

        return $supervisor;
    }

    private function fetchServices($supervisor, Server $server)
    {
        print_r($supervisor->getProcess());die;
    }
}
