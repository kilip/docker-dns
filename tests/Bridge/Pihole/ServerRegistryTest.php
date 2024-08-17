<?php

/*
 * This file is part of the DockerDNS project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DockerDNS\Tests\Bridge\Pihole;

use DockerDNS\Bridge\Pihole\Server;
use DockerDNS\Bridge\Pihole\ServerRegistry;
use PHPUnit\Framework\TestCase;

class ServerRegistryTest extends TestCase
{
    public function testConstruct(): void
    {
        $servers = new ServerRegistry([
            ['url' => 'http://localhost', 'token' => 'token'],
        ]);
        $this->assertCount(1, $servers);
        $this->assertInstanceOf(Server::class, $servers[0]);
        $this->assertTrue(isset($servers[0]));

        unset($servers[0]);
        $this->assertFalse(isset($servers[0]));
        $servers[0] = new Server('http://localhost', 'token');
        $this->assertCount(1, $servers);
    }
}
