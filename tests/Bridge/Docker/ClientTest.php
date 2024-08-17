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

/**
 * @covers \DockerDNS\Bridge\Docker\Client
 * @covers \DockerDNS\Bridge\Docker\Serializer\PropertyNameConverter
 * @covers \DockerDNS\Bridge\Docker\DTO\Container
 */
class ClientTest extends TestCase
{
    public function testConnection(): void
    {
        $guzzle = Fixtures::getDockerContainers();
        $client = new Client($guzzle);
        $containers = $client->getContainers();
        $this->assertCount(2, $containers);

        $key = 'dae59015ea7d66ea927ecb75ee99d8381df4e8c17e127ffcb0c368bd0389396e';
        $this->assertArrayHasKey($key, $containers);
        $container = $containers[$key];

        $this->assertSame('whoami', $container->getName());
        $this->assertTrue($container->hasLabel($label = 'dockerdns.pihole.cname.domain'));
        $this->assertSame('whoami.home.lan', $container->getLabelValue($label));
        $this->assertSame('foobar', $container->getLabelValue('foo', 'foobar'));
    }
}
