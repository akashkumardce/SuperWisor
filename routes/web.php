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
Route::get('/install', array("as" => "login", 'uses' => 'HomeController@install'))->middleware('non.auth');
Route::get('/', array("as" => "login", 'uses' => 'HomeController@showLogin'))->middleware('non.auth');
Route::get('/login', array('uses' => 'HomeController@showLogin'))->middleware('non.auth');
Route::post('/login', array('uses' => 'HomeController@doLogin'))->middleware('non.auth');
Route::post('/logout', array("as" => "logout", 'uses' => 'HomeController@logout'))->middleware('auth');

//TEAM
Route::get('/dashboard', array('uses' => 'DashboardController@showDashbard'))->middleware('auth');
Route::get('/team-manage', array('uses' => 'TeamManagerController@showDashbard'))->middleware(['auth']);
Route::get('/team-list', array('uses' => 'TeamManagerController@getTeamList'))->middleware(['auth']);

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

//LOG
Route::get('/log-list/{action}', array('uses' => 'LogController@getLog'))->middleware(['auth']);

//ADMIN
Route::get('/admin', array('uses' => 'AdminManagerController@show'))->middleware(['auth','auth.admin']);
Route::put('/admin', array('uses' => 'AdminManagerController@change'))->middleware(['auth','auth.admin']);

Route::get('/clear', function() {

    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');

    return "Cleared!";

});




//USER
Route::get('/user-manage', array('uses' => 'UserManagerController@showDashbard'))->middleware(['auth','auth.admin']);
Route::get('/user-list', array('uses' => 'UserManagerController@getUserList'))->middleware(['auth','auth.admin']);

Route::get('/users/{userId}', array('uses' => 'UserManagerController@getUserDetails'))->middleware(['auth','auth.admin']);
Route::put('/users/{userId}', array('uses' => 'UserManagerController@editUser'))->middleware(['auth','auth.admin']);
Route::post('/users', array('uses' => 'UserManagerController@addUser'))->middleware(['auth','auth.admin']);
Route::delete('/users/{userId}', array('uses' => 'UserManagerController@removeUser'))->middleware(['auth','auth.admin']);
Route::delete('/users/{userId}/team', array('uses' => 'UserManagerController@removeTeam'))->middleware(['auth','auth.admin']);

Route::get('/users/{userId}/master-team', array('uses' => 'UserManagerController@getMasterTeams'))->middleware(['auth','auth.admin']);
Route::get('/users/{userId}/dev-team', array('uses' => 'UserManagerController@getDevTeams'))->middleware(['auth','auth.admin']);

//SERVER
Route::get('/server-manage', array('uses' => 'ServerManagerController@showDashboard'))->middleware(['auth.admin']);
Route::get('/server-list', array('uses' => 'ServerManagerController@getServerList'))->middleware(['auth','auth.admin']);
Route::post('/servers', array('uses' => 'ServerManagerController@addServer'))->middleware(['auth','auth.admin']);
Route::get('/servers/{serverId}', array('uses' => 'ServerManagerController@getServerDetails'))->middleware(['auth','auth.admin']);
Route::put('/servers/{serverId}', array('uses' => 'ServerManagerController@updateServer'))->middleware(['auth','auth.admin']);
Route::get('/servers/{serverId}/team', array('uses' => 'ServerManagerController@getServerTeams'))->middleware(['auth','auth.admin']);
Route::get('/servers/{serverId}/service', array('uses' => 'ServerManagerController@getServerServices'))->middleware(['auth']);
Route::delete('/servers/{serverId}', array('uses' => 'ServerManagerController@removeServer'))->middleware(['auth','auth.admin']);
Route::delete('/servers/{serverId}/team', array('uses' => 'ServerManagerController@removeTeam'))->middleware(['auth','auth.admin']);
Route::put('/servers/status/{serverId}', array('uses' => 'ServerManagerController@statusUpdate'))->middleware(['auth']);

//Service
Route::get('/service-manage', array('uses' => 'ServiceManagerController@showDashboard'))->middleware('auth');
Route::get('/service-list', array('uses' => 'ServiceManagerController@getServiceList'))->middleware(['auth']);
Route::get('/services/{serviceId}', array('uses' => 'ServiceManagerController@getServiceDetails'))->middleware(['auth']);
Route::get('/services/{serviceId}/server', array('uses' => 'ServiceManagerController@getServiceServer'))->middleware(['auth']);
Route::get('/services/{serviceId}/master-team', array('uses' => 'ServiceManagerController@getMasterUsers'))->middleware(['auth']);
Route::get('/services/{serviceId}/developer-team', array('uses' => 'ServiceManagerController@getDeveloperUsers'))->middleware(['auth']);
Route::get('/services/{serviceId}/team', array('uses' => 'ServiceManagerController@getTeams'))->middleware(['auth']);
Route::delete('/services/{serverId}/developer', array('uses' => 'ServiceManagerController@removeDeveloper'))->middleware(['auth']);
Route::post('/services/{serverId}/developer', array('uses' => 'ServiceManagerController@linkDeveloper'))->middleware(['auth']);


Route::post('/services/perform', array('uses' => 'ServiceManagerController@perform'))->middleware(['auth']);
