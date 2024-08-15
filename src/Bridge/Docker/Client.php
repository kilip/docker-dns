<?php

namespace DockerDNS\Bridge\Docker;

use GuzzleHttp\Client as GuzzleClient;

class Client
{
    public function __construct(
        private ?GuzzleClient $guzzle = null
    )
    {
        // @codeCoverageIgnoreStart
        if(is_null($guzzle)){
            $guzzle = new GuzzleClient([
                'base_uri' => 'http://localhost/v1.46',
                'curl' => [
                    CURLOPT_UNIX_SOCKET_PATH => '/var/run/docker.sock'
                ]
            ]);
        }
        // @codeCoverageIgnoreEnd

        $this->guzzle = $guzzle;
    }

    public function getContainers(): Containers
    {
        $response = $this->guzzle->request('GET', '/containers/json');
        $json = $response->getBody()->getContents();
        return Containers::fromJson($json);
    }
}
