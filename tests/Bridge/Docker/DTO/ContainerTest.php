<?php

/*
 * This file is part of the DockerDNS project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DockerDNS\Tests\Bridge\Docker\DTO;

use DockerDNS\Bridge\Docker\DTO\Container;
use DockerDNS\Tests\Fixtures;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function testLabel(): void
    {
        $container = Fixtures::createContainers()['dae59015ea7d66ea927ecb75ee99d8381df4e8c17e127ffcb0c368bd0389396e'];
        $this->assertInstanceOf(Container::class, $container);

        $label = 'org.opencontainers.image.title';
        $this->assertTrue($container->hasLabel($label));
        $this->assertSame($container->getLabelValue($label), 'whoami');
    }
}
