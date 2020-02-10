<?php

namespace App\Providers;

use App\ServiceServer;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\User;
use Supervisor\Supervisor;
use Supervisor\Connector\XmlRpc;
use fXmlRpc\Client;
use fXmlRpc\Transport\HttpAdapterTransport;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use App\Service,Auth,App\Server;
use \Http\Message\MessageFactory\DiactorosMessageFactory as MessageFactory;

class SupervisordServiceProvider extends ServiceProvider
{
    private $supervisor;

    public function __construct(Server $server)
    {
        $guzzleClient = new \GuzzleHttp\Client(['auth' => [$server['username'], $server['password']]]);

        $client = new Client(
            'http://'.$server['ip'].':'.$server['port'].'/RPC2',
            new HttpAdapterTransport(
                new MessageFactory(),
                new GuzzleAdapter($guzzleClient))
        );

        $connector = new XmlRpc($client);
        $this->supervisor = new Supervisor($connector);
    }

    public function isConnected(){
        return $this->supervisor->isConnected();
    }

    public function getProcess()
    {
        return $this->supervisor->getAllProcesses();
    }

    public function perform(ServiceServer $serviceServer,$action){
        switch ($action){
            case "start":
                $this->supervisor->startProcess($serviceServer->service->name);
                ServiceServer::query()->where("id",$serviceServer->id)->update(["start_by"=>Auth::user()->id]);
                return;
            case "stop":
                $this->supervisor->stopProcess($serviceServer->service->name);
                ServiceServer::query()->where("id",$serviceServer->id)->update(["stop_by"=>Auth::user()->id]);
                return;
            case "log":
                return $this->supervisor->readProcessStdoutLog($serviceServer->service->name,0,5000);
        }
    }
}
