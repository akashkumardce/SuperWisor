<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Team;
use App\User;
use App\UserTeam;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Validator, Input, Redirect, Request;
use View, Auth,DataTables, Illuminate\Validation\Rule, App\Log;
use App\Providers\LoginServiceProvider;

class UserManagerController extends Controller
{
    public function showDashbard()
    {
        $param['roles'] = config('roles.system');
        return View::make('user/dashboard',$param);
    }

    public function getUserList()
    {
        return DataTables::of(User::query()->with(['creator'])->withCount(['master' => function ($query) {
            $query->where('role', 'MASTER_DEVELOPER');
        }, 'developer' => function ($query) {
            $query->where('role', 'SERVICE_DEVELOPER');
        }]))->make(true);
    }
    public function getMasterTeams($userId)
    {
        return DataTables::of(UserTeam::query()->where("user_id",$userId)->where('role', 'MASTER_DEVELOPER')->with(['team']))->make(true);
    }
    public function getDevTeams($userId)
    {
        return DataTables::of(UserTeam::query()->where("user_id",$userId)->where('role', 'SERVICE_DEVELOPER')->with(['team']))->make(true);
    }


    public function getUserDetails($userId)
    {
        $userInfo = User::query()->where("id",$userId)->get()->toArray();

        $data = array(
            "id" => $userId,
            "name" => $userInfo[0]["name"],
            "created_at" => $userInfo[0]["created_at"],
            "created_by" => $userInfo[0]["created_by"],
            "email" => $userInfo[0]["email"],
            "role" => $userInfo[0]["role"],
            "roleOptions" => array_merge([""=>"select"],config('roles.system'))
        );
        return View::make('user/user', $data);
    }

    public function addUser(){
        $name = Request::get('name');
        $email = Request::get('email');
        $passsword = Request::get('password');
        $role = Request::get('role');

        $rules = array(
            'name' => 'required|min:4|max:25',
            'email' =>'required|email|unique:users,email',
            'role' => [ Rule::in(config('roles.system'))],
            'password' => 'required'
        );

        // run the validation rules on the inputs from the form
        $validator = Validator::make(Request::all(), $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return Redirect::to('/user-manage')
                ->withErrors($validator)// send back all errors to the login form
                ->withInput();
        } else {
            try {
                $team = new User();
                $team->name = $name;
                $team->password = $passsword;
                $team->email = $email;
                $team->role = $role;
                $team->created_by = Auth::user()->id;
                $team->save();

                $Log = new Log();
                $Log->user_id = Auth::user()->id;
                $Log->log = "user";
                $Log->message = Auth::user()->name." Added User with role ".$role." email ".Request::get('email')." at ".date("Y-m-d H:i:s", time());
                $Log->save();
            } catch (QueryException $e) { // It's actually a QueryException but this works too
                echo $e->getMessage();die;
                $validator = Validator::make([], []);
                if ($e->getCode() == 23000) {
                    $validator->errors()->add('email', 'User Already Exist in the team');
                }else{
                    $validator->errors()->add('email', $e->getMessage());
                }
                return Redirect::to('/user-manage')
                    ->withErrors($validator)// send back all errors to the login form
                    ->withInput();
            }
            return Redirect::to('/users/'.$team->id);
        }
    }

    public function editUser($userId){
        $id = $userId;
        $role = Request::get('role');

        $rules = array(
            'role' => [ Rule::in(config('roles.system'))],
        );

        // run the validation rules on the inputs from the form
        $validator = Validator::make(Request::all(), $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return Redirect::to('/users/'.$userId)
                ->withErrors($validator)// send back all errors to the login form
                ->withInput();
        } else {
            try {
                User::query()->where('id',$id)->where('user_id',$userId)->update(["role"=>$role]);

                $Log = new Log();
                $Log->user_id = Auth::user()->id;
                $Log->log = "user".$id;
                $Log->message = Auth::user()->name." Updated role of ".Request::get('id')." to ".$role." at ".date("Y-m-d H:i:s", time());
                $Log->save();
            } catch (QueryException $e) {
                echo $e->getMessage();die;
                $validator = Validator::make([], []);
                $validator->errors()->add('role', $e->getMessage());
                return Redirect::to('/users/'.$userId)
                    ->withErrors($validator)// send back all errors to the login form
                    ->withInput();
            }
            return Redirect::to('/users/'.$userId);
        }
    }

    public function removeUser($userId){
        $id = Request::get('id');

        $rules = array(
            'id' => 'required|integer|exists:users,id|unique:user_team,user_id'
        );

        // run the validation rules on the inputs from the form
        $validator = Validator::make(Request::all(), $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return response()->json(['error' => "Only a User with no Team responsiblity can be deleted"]);
        } else {
            try {
                User::query()->where('id',$id)->where('id',$userId)->delete();

                $Log = new Log();
                $Log->user_id = Auth::user()->id;
                $Log->log = "user";
                $Log->message = Auth::user()->name." Deleted User ".Request::get('value')." at ".date("Y-m-d H:i:s", time());
                $Log->save();
            } catch (QueryException $e) { // It's actually a QueryException but this works too
                return response()->json(['error' => $e->getMessage()]);
            }
            return response()->json(['success' => "User Deleted successfully"]);
        }
    }

    public function removeTeam($userId){
        $id = Request::get('id');

        $rules = array(
            'id' => 'required|integer|exists:user_team,id'
        );

        // run the validation rules on the inputs from the form
        $validator = Validator::make(Request::all(), $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return response()->json(['error' => "No Team exist"]);
        } else {
            try {
                UserTeam::query()->where('id',$id)->where('user_id',$userId)->delete();

                $Log = new Log();
                $Log->user_id = Auth::user()->id;
                $Log->log = "user";
                $Log->message = Auth::user()->name." Deleted User ".$id." from team ".Request::get('value')." at ".date("Y-m-d H:i:s", time());
                $Log->save();
            } catch (QueryException $e) { // It's actually a QueryException but this works too
                return response()->json(['error' => $e->getMessage()]);
            }
            return response()->json(['success' => "Team Deleted successfully"]);
        }
    }
}
