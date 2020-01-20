<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserTeam extends Model
{
    protected $table = 'user_team';
    public static function  query()
    {
        return (new static)->newQuery()->with(['user']);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
