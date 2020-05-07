<?php

namespace App\Providers;

use App\Server;
use App\ServerTeam;
use App\Service;
use App\ServiceServer;
use App\Team;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\User;
use Supervisor\Supervisor;
use Supervisor\Connector\XmlRpc;
use fXmlRpc\Client;
use fXmlRpc\Transport\HttpAdapterTransport;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use \Http\Message\MessageFactory\DiactorosMessageFactory as MessageFactory;

class ServiceManagerProvider
{
    public function addService(ServiceServer $serviceServer, Service $service){
        try {
            if(stristr($service->name,'background-worker')){
                return;
            }
            $serviceObj = Service::query()->where("name",$service->name)->select(['id'])->first();
            if(empty($serviceObj)){
                $service->save();
            }else{
                $service->id = $serviceObj->id;
            }
            $serviceServerObj = ServiceServer::query()->where("server_id",$serviceServer->server_id)
                ->where("service_id",$service->id)
                ->where("team_id",$serviceServer->team_id)->select(['id'])->first();
            if(empty($serviceServerObj)){
                $serviceServer->service_id=$service->id;
                $serviceServer->team_id=$serviceServer->team_id;
                $serviceServer->save();
            }else{
                ServiceServer::query()->where("id",$serviceServerObj->id)->update(["state"=>$serviceServer->status,
                    "team_id"=>$serviceServer->team_id,
                    "status"=>$serviceServer->status,
                    "stop"=>$serviceServer->stop,
                    "start"=>$serviceServer->start,
                    "state_id"=>$serviceServer->state_id,
                    "checked_at"=>DB::raw('now()')]);
            }
        } catch (\Exception $e) { // It's actually a QueryException but this works too
            throw new \Exception("Unable to store service information");
        }

    }

    public function fetchServices(Server $server, SupervisordServiceProvider $supervisor)
    {
        if (!$supervisor) {
            $supervisor = new SupervisordServiceProvider($server);
        }
        $processes = $supervisor->getProcess();
        $serviceList = [];
        $teamArr = Team::query()->get("id","name")->pluck('id','name')->all();
        foreach ($processes as $process) {
            $supervisorService = $process->getPayload();
            $service = new Service();
            if (stristr($supervisorService['group'], "---")) {
                $groupDivision = explode("---",$supervisorService['group']);
                $teamId = $teamArr[$groupDivision[0]];
                $serviceName = $groupDivision[1];
            }else{
                $teamId = 0;
                $serviceName = $supervisorService['name'];
            }
            $service->name = $serviceName;
            $service->group_name = $supervisorService['group'];

            $serviceServer = new ServiceServer();
            $serviceServer->display_name = $supervisorService['name'];
            $serviceServer->team_id = $teamId;
            $serviceServer->server_id = $server->id;
            $serviceServer->description = $supervisorService['description'];
            $serviceServer->start = $supervisorService['start'];
            $serviceServer->stop = $supervisorService['stop'];
            $serviceServer->state = $supervisorService['statename'];
            $serviceServer->status = $supervisorService['statename'];
            $serviceServer->state_id = $supervisorService['state'];
            $serviceServer->pid = $supervisorService['pid'];
            $serviceServer->exitstatus = $supervisorService['exitstatus'];
            $serviceServer->logfile = $supervisorService['stdout_logfile'];

            $this->addService($serviceServer, $service, $teamArr);
        }

    }

}