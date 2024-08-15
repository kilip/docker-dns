<?php

namespace DockerDNS\Tests;

use DockerDNS\Bridge\Docker\Client as DockerClient;
use DockerDNS\Bridge\Docker\Containers;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class Fixtures
{
    public static function getDockerContainers(): GuzzleClient
    {
        $body = file_get_contents(__DIR__.'/fixtures/containers.json');

        $mock = new MockHandler([
            new Response(200, [], $body)
        ]);
        $stack = new HandlerStack($mock);
        return new GuzzleClient(['handler' => $stack]);
    }

    public static function createContainers(): Containers
    {
        $guzzle = Fixtures::getDockerContainers();
        $client = new DockerClient($guzzle);
        return $client->getContainers();
    }
}