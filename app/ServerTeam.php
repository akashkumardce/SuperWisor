<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServerTeam extends Model
{
    protected $table = 'server_team';
    public static function  query()
    {
        return (new static)->newQuery()->with(['server']);
    }
    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}
