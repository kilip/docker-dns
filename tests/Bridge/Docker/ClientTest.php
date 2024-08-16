<?php

/*
 * This file is part of the DockerDNS project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DockerDNS\Tests\Bridge\Docker;

use DockerDNS\Bridge\Docker\Client;
use DockerDNS\Tests\Fixtures;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testConnection(): void
    {
        $guzzle = Fixtures::getDockerContainers();
        $client = new Client($guzzle);
        $containers = $client->getContainers();
        $this->assertCount(2, $containers);
    }
}
