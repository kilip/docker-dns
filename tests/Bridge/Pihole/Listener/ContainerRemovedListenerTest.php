<?php

/*
 * This file is part of the DockerDNS project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DockerDNS\Tests\Bridge\Pihole\Listener;

use DockerDNS\Bridge\Docker\Entity\Container;
use DockerDNS\Bridge\Docker\Event\ContainerRemovedEvent;
use DockerDNS\Bridge\Pihole\Entity\CName;
use DockerDNS\Bridge\Pihole\Listener\ContainerRemovedListener;
use DockerDNS\Bridge\Pihole\Repository\CNameRepository;
use DockerDNS\Bridge\Pihole\Server;
use DockerDNS\Bridge\Pihole\ServerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ContainerRemovedListenerTest extends TestCase
{
    private ServerRegistry $servers;
    private MockObject|CNameRepository $repository;
    private MockObject|LoggerInterface $logger;
    private MockObject|Server $server;
    private ContainerRemovedListener $listener;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(CNameRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->servers = new ServerRegistry([]);
        $this->server = $this->createMock(Server::class);
        $this->server->name = 'localhost';
        $this->servers[] = $this->server;

        $this->listener = new ContainerRemovedListener(
            $this->servers,
            $this->repository,
            $this->logger
        );
    }

    public function testInvoke(): void
    {
        $container = new Container();
        $container->containerId = 'id';
        $container->name = 'container';
        $event = new ContainerRemovedEvent($container);
        $cname = new CName();
        $cname->domain = 'domain';
        $cname->target = 'target';

        $this->repository->expects($this->once())
            ->method('findByContainer')
            ->with($container->containerId)
            ->willReturn([$cname]);

        $this->server->expects($this->once())
            ->method('removeCName');

        $this->listener->__invoke($event);
    }
}
