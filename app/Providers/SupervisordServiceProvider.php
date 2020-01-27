<?php

namespace App\Providers;

use App\Server;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\User;
use Supervisor\Supervisor;
use Supervisor\Connector\XmlRpc;
use fXmlRpc\Client;
use fXmlRpc\Transport\HttpAdapterTransport;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
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
}
