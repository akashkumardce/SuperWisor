<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function service()
    {
        return $this->belongsTo(ServiceServer::class, 'id', 'server_id');
    }
    public function team()
    {
        return $this->belongsTo(ServerTeam::class, 'id', 'server_id');
    }
    public function running()
    {
        return $this->belongsTo(ServiceServer::class, 'id', 'server_id');
    }
    public function error()
    {
        return $this->belongsTo(ServiceServer::class, 'id', 'server_id');
    }
}
