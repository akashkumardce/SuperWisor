<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    public static function  query()
    {
        return (new static)->newQuery()->with(['user']);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
