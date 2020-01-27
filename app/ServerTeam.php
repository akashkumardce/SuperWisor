<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServerTeam extends Model
{
    protected $table = 'server_team';

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
    public function team()
    {
        return $this->belongsTo(Team::class,"team_id","id");
    }
}
