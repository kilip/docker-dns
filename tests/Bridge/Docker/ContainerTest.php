<?php

namespace DockerDNS\Tests\Bridge\Docker;

use DockerDNS\Bridge\Docker\Container;
use DockerDNS\Tests\Fixtures;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function testLabel(): void
    {
        $container = Fixtures::createContainers()[0];
        $this->assertInstanceOf(Container::class, $container);

        $label = 'org.opencontainers.image.title';
        $this->assertTrue($container->hasLabel($label));
        $this->assertSame($container->getLabelValue($label), 'whoami');
    }
}