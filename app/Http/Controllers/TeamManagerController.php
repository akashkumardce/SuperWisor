<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Log;
use App\Providers\RouteServiceProvider;
use App\Server;
use App\ServerTeam;
use App\User;
use http\Exception;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Validator, Input, Redirect, Request;
use View, Auth, DataTables, App\Team, App\UserTeam;
use Illuminate\Database\QueryException;
use App\Providers\LoginServiceProvider;
use App\Providers\TeamServiceProvider;

class TeamManagerController extends Controller
{
    public function showDashbard()
    {
        return View::make('team/dashboard');
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
            "serverList" => $serverList
        );
        return View::make('team/team', $data);
    }

    public function removeTeam($team)
    {
        $id = Request::get('id');

        $rules = array(
            'id' => 'required|integer|exists:teams,id|unique:user_team,team_id|unique:server_team,team_id'
        );

        // run the validation rules on the inputs from the form
        $validator = Validator::make(Request::all(), $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return response()->json(['error' => "Only a team with no Members and Servers can be deleted"]);
        } else {
            try {
                Team::query()->where('id',$id)->where('id',$team)->delete();

                $Log = new Log();
                $Log->user_id = Auth::user()->id;
                $Log->log = "team";
                $Log->message = Auth::user()->name." Deleted Team ".Request::get('value')." at ".date("Y-m-d H:i:s", time());
                $Log->save();
            } catch (QueryException $e) { // It's actually a QueryException but this works too
                return response()->json(['error' => $e->getMessage()]);
            }
            return response()->json(['success' => "Team Deleted successfully"]);
        }
    }

    public function addTeam()
    {
        $name = Request::get('team');
        $rules = array(
            'team' => 'required|regex:/^[\pL\s\-]+$/u|min:3|max:15|unique:teams,name'
        );

        // run the validation rules on the inputs from the form
        $validator = Validator::make(Request::all(), $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return Redirect::to('/team-manage')
                ->withErrors($validator)// send back all errors to the login form
                ->withInput();
        } else {
            try {
                $team = new Team();
                $team->name = $name;
                $team->created_by = Auth::user()->id;
                $team->save();

                $Log = new Log();
                $Log->user_id = Auth::user()->id;
                $Log->log = "team";
                $Log->message = Auth::user()->name." Added Team ".Request::get('team')." at ".date("Y-m-d H:i:s", time());
                $Log->save();
            } catch (QueryException $e) { // It's actually a QueryException but this works too
                $validator = Validator::make([], []);
                if ($e->getCode() == 23000) {
                    $validator->errors()->add('team', 'Team Already Exist in the team');
                }else{
                    $validator->errors()->add('team', $e->getMessage());
                }
                return Redirect::to('/team-manage')
                    ->withErrors($validator)// send back all errors to the login form
                    ->withInput();
            }
            return Redirect::to('/teams/'.$team->id);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTeamList()
    {
        return DataTables::of(Team::query()->with(['user'])->withCount(['member','server']))->make(true);
    }

    public function getMasters($team)
    {
        return DataTables::of(UserTeam::query()->where('team_id', $team)->where('role', 'MASTER_DEVELOPER')->with(['user']))->make(true);
    }

    public function addMasters($team)
    {
        $masterId = Request::get('id');
        $rules = array(
            'id' => 'required|integer|exists:users,id'
        );

        // run the validation rules on the inputs from the form
        $validator = Validator::make(Request::all(), $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        } else {
            try {
                $userTeam = new UserTeam();
                $userTeam->user_id = $masterId;
                $userTeam->team_id = $team;
                $userTeam->role = config('roles.team.MASTER_DEVELOPER');
                $userTeam->created_by = Auth::user()->id;
                $userTeam->save();

                $Log = new Log();
                $Log->user_id = Auth::user()->id;
                $Log->log = "team".$team;
                $Log->message = Auth::user()->name." Added User ".Request::get('value')." as master for team ".$team." at ".date("Y-m-d H:i:s", time());
                $Log->save();
            } catch (QueryException $e) { // It's actually a QueryException but this works too
                if ($e->getCode() == 23000) {
                    return response()->json(['error' => "User Already Exist in the team"]);
                }
                return response()->json(['error' => $e->getMessage()]);
            }
            return response()->json(['success' => "Master adedd successfully"]);
        }
    }

    public function removeMasters($team)
    {
        $id = Request::get('id');
        $rules = array(
            'id' => 'required|integer|exists:user_team,id'
        );

        // run the validation rules on the inputs from the form
        $validator = Validator::make(Request::all(), $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        } else {
            try {
                UserTeam::query()->where('id',$id)->where('team_id',$team)->delete();

                $Log = new Log();
                $Log->user_id = Auth::user()->id;
                $Log->log = "team".$team;
                $Log->message = Auth::user()->name." Deleted Master ".Request::get('value')." from team ".$team." at ".date("Y-m-d H:i:s", time());
                $Log->save();
            } catch (QueryException $e) { // It's actually a QueryException but this works too
                return response()->json(['error' => $e->getMessage()]);
            }
            return response()->json(['success' => "Master Deleted successfully"]);
        }
    }


    public function getUsers($team)
    {
        return DataTables::of(UserTeam::query()->where('team_id', $team)->where('role', 'SERVICE_DEVELOPER')->with(['user']))->make(true);
    }
    public function addUsers($team)
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
            try {
                $userTeam = new UserTeam();
                $userTeam->user_id = $userId;
                $userTeam->team_id = $team;
                $userTeam->role = config('roles.team.SERVICE_DEVELOPER');
                $userTeam->created_by = Auth::user()->id;
                $userTeam->save();

                $Log = new Log();
                $Log->user_id = Auth::user()->id;
                $Log->log = "team".$team;
                $Log->message = Auth::user()->name." Added User ".Request::get('value')." as Developer for team ".$team." at ".date("Y-m-d H:i:s", time());
                $Log->save();
            } catch (QueryException $e) { // It's actually a QueryException but this works too
                if ($e->getCode() == 23000) {
                    return response()->json(['error' => "User Already Exist in the team"]);
                }
                return response()->json(['error' => $e->getMessage()]);
            }
            return response()->json(['success' => "User adedd successfully"]);
        }
    }
    public function removeUsers($team)
    {
        $id = Request::get('id');
        $rules = array(
            'id' => 'required|integer|exists:user_team,id'
        );

        // run the validation rules on the inputs from the form
        $validator = Validator::make(Request::all(), $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        } else {
            try {
                UserTeam::query()->where('id',$id)->where('team_id',$team)->delete();

                $Log = new Log();
                $Log->user_id = Auth::user()->id;
                $Log->log = "team".$team;
                $Log->message = Auth::user()->name." Deleted Developer ".Request::get('value')." from team ".$team." at ".date("Y-m-d H:i:s", time());
                $Log->save();
            } catch (QueryException $e) { // It's actually a QueryException but this works too
                return response()->json(['error' => $e->getMessage()]);
            }
            return response()->json(['success' => "Developer Deleted successfully"]);
        }
    }

    public function getServers($team)
    {
        return DataTables::of(ServerTeam::query()->where('team_id', $team)->with(['server']))->make(true);
    }
    public function addServers($team)
    {
        $serverId = Request::get('id');
        $rules = array(
            'id' => 'required|integer|exists:servers,id'
        );

        // run the validation rules on the inputs from the form
        $validator = Validator::make(Request::all(), $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        } else {
            try {
                $serverTeam = new ServerTeam();
                $serverTeam->server_id = $serverId;
                $serverTeam->team_id = $team;
                $serverTeam->created_by = Auth::user()->id;
                $serverTeam->save();

                $Log = new Log();
                $Log->user_id = Auth::user()->id;
                $Log->log = "team".$team;
                $Log->message = Auth::user()->name." Added Server ".Request::get('value')." in team ".$team." at ".date("Y-m-d H:i:s", time());
                $Log->save();
            } catch (QueryException $e) { // It's actually a QueryException but this works too
                if ($e->getCode() == 23000) {
                    return response()->json(['error' => "Server Already Exist in the team"]);
                }
                return response()->json(['error' => $e->getMessage()]);
            }
            return response()->json(['success' => "Server adedd successfully"]);
        }
    }
    public function removeServers($team)
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
                ServerTeam::query()->where('id',$id)->where('team_id',$team)->delete();

                $Log = new Log();
                $Log->user_id = Auth::user()->id;
                $Log->log = "team".$team;
                $Log->message = Auth::user()->name." Deleted Server ".Request::get('value')." from team ".$team." at ".date("Y-m-d H:i:s", time());
                $Log->save();
            } catch (QueryException $e) { // It's actually a QueryException but this works too
                return response()->json(['error' => $e->getMessage()]);
            }
            return response()->json(['success' => "Server Deleted successfully"]);
        }
    }
}
