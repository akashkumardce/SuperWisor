<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//Route::get('/', function () {
//  return view('welcome');
//});
Route::get('/', array("as" => "login", 'uses' => 'HomeController@showLogin'))->middleware('non.auth');
Route::get('/login', array('uses' => 'HomeController@showLogin'))->middleware('non.auth');
Route::post('/login', array('uses' => 'HomeController@doLogin'))->middleware('non.auth');
Route::post('/logout', array("as" => "logout", 'uses' => 'HomeController@logout'))->middleware('auth');

Route::get('/dashboard', array('uses' => 'DashboardController@showDashbard'))->middleware('auth');
Route::get('/team-manage', array('uses' => 'TeamManagerController@showDashbard'))->middleware(['auth','auth.admin']);
Route::get('/team-list', array('uses' => 'TeamManagerController@getTeamList'))->middleware(['auth','auth.admin']);

Route::get('/teams/{teamId}', array('uses' => 'TeamManagerController@getTeamDetails'))->middleware(['auth','auth.admin']);
Route::post('/teams', array('uses' => 'TeamManagerController@addTeam'))->middleware(['auth','auth.admin']);
Route::delete('/teams/{teamId}', array('uses' => 'TeamManagerController@removeTeam'))->middleware(['auth','auth.admin']);

Route::get('/teams/{teamId}/masters', array('uses' => 'TeamManagerController@getMasters'))->middleware(['auth','auth.admin']);
Route::post('/teams/{teamId}/masters', array('uses' => 'TeamManagerController@addMasters'))->middleware(['auth','auth.admin']);
Route::delete('/teams/{teamId}/masters', array('uses' => 'TeamManagerController@removeMasters'))->middleware(['auth','auth.admin']);

Route::get('/teams/{teamId}/users', array('uses' => 'TeamManagerController@getUsers'))->middleware(['auth','auth.admin']);
Route::post('/teams/{teamId}/users', array('uses' => 'TeamManagerController@addUsers'))->middleware(['auth','auth.admin']);
Route::delete('/teams/{teamId}/users', array('uses' => 'TeamManagerController@removeUsers'))->middleware(['auth','auth.admin']);

Route::get('/teams/{teamId}/servers', array('uses' => 'TeamManagerController@getServers'))->middleware(['auth','auth.admin']);
Route::post('/teams/{teamId}/servers', array('uses' => 'TeamManagerController@addServers'))->middleware(['auth','auth.admin']);
Route::delete('/teams/{teamId}/servers', array('uses' => 'TeamManagerController@removeServers'))->middleware(['auth','auth.admin']);

Route::get('/log-list/{action}', array('uses' => 'LogController@getLog'))->middleware(['auth','auth.admin']);

Route::get('/user-manage', array('uses' => 'UserManagerController@showDashbard'))->middleware(['auth','auth.admin']);
Route::get('/user-list', array('uses' => 'UserManagerController@getUserList'))->middleware(['auth','auth.admin']);

Route::get('/server-manage', array('uses' => 'ServerManagerController@showUsers'))->middleware('auth');
Route::get('/service-manage', array('uses' => 'ServiceManagerController@showUsers'))->middleware('auth');
