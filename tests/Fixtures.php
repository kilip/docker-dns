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
    public const CONTAINER_KEY_WHOAMI = 'dae59015ea7d66ea927ecb75ee99d8381df4e8c17e127ffcb0c368bd0389396e';

    public static function getDockerContainers(): GuzzleClient
    {
        $body = file_get_contents(__DIR__.'/fixtures/containers.json');

        $mock = new MockHandler([
            new Response(200, [], $body),
        ]);
        $stack = new HandlerStack($mock);

        return new GuzzleClient(['handler' => $stack]);
    }

    public static function cnamesFileContent(): string
    {
        return file_get_contents(__DIR__.'/fixtures/cnames.json');
    }

    public static function getCNames(): GuzzleClient
    {
        $body = static::cnamesFileContent();

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
