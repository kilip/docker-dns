<?php

namespace DockerDNS\Tests\Bridge\Docker;

use GuzzleHttp\Client as GuzzleClient;
use DockerDNS\Bridge\Docker\Client;
use DockerDNS\Bridge\Docker\Containers;
use DockerDNS\Tests\Fixtures;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testConnection(): void
    {
        $guzzle = Fixtures::getDockerContainers();
        $client = new Client($guzzle);
        $containers = $client->getContainers();
        $this->assertInstanceOf(Containers::class, $containers);
        $this->assertCount(2, $containers);
    }
}
