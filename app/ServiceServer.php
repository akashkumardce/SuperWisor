<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceServer extends Model
{
    protected $table = 'service_server';

    public function server()
    {
        return $this->belongsTo(Server::class, 'server_id', 'id');
    }
    public function serverteam()
    {
        return $this->hasMany(ServerTeam::class, 'server_id', 'server_id');
    }
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }
    public function running()
    {
        return $this->belongsTo(ServiceServer::class,'service_id','service_id');
    }
    public function startby()
    {
        return $this->belongsTo(User::class, 'start_by', 'id');
    }
    public function stopby()
    {
        return $this->belongsTo(User::class, 'stop_by', 'id');
    }
}
