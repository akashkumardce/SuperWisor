<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class,'created_by');
    }
    public function member()
    {
        return $this->belongsTo(UserTeam::class,'id','team_id');
    }
    public function master()
    {
        return $this->hasMany(UserTeam::class,'id','team_id')->where('role', 'MASTER_DEVELOPER');
    }
    public function developer()
    {
        return $this->hasMany(UserTeam::class,'id','team_id')->where('role', 'SERVICE_DEVELOPER');
    }
    public function server()
    {
        return $this->belongsTo(ServerTeam::class,'id','team_id');
    }
}
