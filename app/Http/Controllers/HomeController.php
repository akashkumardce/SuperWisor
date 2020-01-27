<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Validator, Input, Redirect, Request;
use View, Auth;
use App\Providers\LoginServiceProvider;

class HomeController extends Controller
{
    public function showLogin()
    {
        return View::make('login');
    }

    public function logout()
    {
        Auth::logout();
        return View::make('login');
    }

    public function doLogin()
    {
        $rules = array(
            'email' => 'required|email', // make sure the email is an actual email
            'password' => 'required|alphaNum|min:6' // password can only be alphanumeric and has to be greater than 3 characters
        );

        // run the validation rules on the inputs from the form
        $validator = Validator::make(Request::all(), $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return Redirect::to('/')
                ->withErrors($validator)// send back all errors to the login form
                ->withInput(Request::except('password')); // send back the input (not the password) so that we can repopulate the form
        } else {
            // create our user data for the authentication
            $userdata = array(
                'email' => Request::get('email'),
                'password' => Request::get('password')
            );

            // attempt to do the login
            if ($user=LoginServiceProvider::attempt($userdata)) {

                Auth::login($user);
                return Redirect::to('/dashboard');

            } else {
                $validator = Validator::make([], []);
                $validator->errors()->add('password', 'Invalid credential');
                return Redirect::to('/')
                    ->withErrors($validator)// send back all errors to the login form
                    ->withInput(Request::except('password'));

            }
        }
    }

    public function install()
    {
        $param = [];

        if(is_writable(base_path()."/storage")){
            $param["storage"] = true;
        }

        return View::make('install',$param);
    }
}
