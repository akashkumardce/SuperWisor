<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    public function running()
    {
        return $this->belongsTo(ServiceServer::class, 'id', 'service_id')->where('status',config('status.service.RUNNING'));
    }
    public function server()
    {
        return $this->belongsTo(ServiceServer::class, 'id', 'service_id');
    }
}
