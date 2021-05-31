<?php

namespace App;

use Laravel\Forge\Resources\Server;

class Forge
{

    /**
     * @var string
     */
    private $token;
    /**
     * @var Server[]
     */
    private $servers;

    /**
     * @param $token
     */
    public function fetchServers($token)
    {
        $forge = new \Laravel\Forge\Forge($token);
        $this->servers = $forge->servers();
    }

    /**
     * @return array
     */
    public function listServerNames()
    {
        return collect($this->servers)->mapWithKeys(function (Server $server) {
            $ready = $server->isReady ? 'Yes':'No';
            return [$server->ipAddress => "{$server->name}"];
        })->toArray();
    }

}
