<?php

/*
 * This file is part of the DockerDNS project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DockerDNS\Tests;

use DockerDNS\Bridge\Docker\Client as DockerClient;
use DockerDNS\Bridge\Docker\DTO\Container;
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
            new Response(200, [], $body),
        ]);
        $stack = new HandlerStack($mock);

        return new GuzzleClient(['handler' => $stack]);
    }

    /**
     * @return array<string, Container>
     */
    public static function createContainers(): array
    {
        $guzzle = Fixtures::getDockerContainers();
        $client = new DockerClient($guzzle);

        return $client->getContainers();
    }
}
