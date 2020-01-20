<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    public static function  query()
    {
        return (new static)->newQuery()->with(['user'])->withCount(['member','server']);
    }
    public function user()
    {
        return $this->belongsTo(User::class,'created_by');
    }
    public function member()
    {
        return $this->belongsTo(UserTeam::class,'id','team_id');
    }
    public function server()
    {
        return $this->belongsTo(ServerTeam::class,'id','team_id');
    }
}
