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

use DockerDNS\Bridge\Pihole\DTO\CName;
use DockerDNS\Bridge\Pihole\DTO\CNameCollection;
use DockerDNS\Bridge\Pihole\Listener\ProcessContainerListener;
use DockerDNS\Bridge\Pihole\Repository\CNameRepository;
use DockerDNS\Bridge\Pihole\Server;
use DockerDNS\Bridge\Pihole\ServerRegistry;
use DockerDNS\Tests\Fixtures;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ProcessContainerListenerTest extends TestCase
{
    private MockObject|ServerRegistry $servers;
    private MockObject|CNameRepository $repository;
    private MockObject|LoggerInterface $logger;
    private MockObject|Server $server;
    private ProcessContainerListener $listener;

    protected function setUp(): void
    {
        $this->servers = new ServerRegistry([]);
        $this->repository = $this->createMock(CNameRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->server = $this->createMock(Server::class);

        $this->servers[0] = $this->server;

        $this->server->name = 'localhost';
        $this->listener = new ProcessContainerListener(
            $this->servers,
            $this->repository,
            $this->logger
        );
    }

    public function testInvoke(): void
    {
        $dto = Fixtures::createContainers()[Fixtures::CONTAINER_KEY_WHOAMI];
        $cnames = $this->createMock(CNameCollection::class);
        $cname = new CName(
            $domain = 'whoami.home.lan',
            'test.home.lan'
        );

        $this->server->expects($this->any())
            ->method('getCNames')
            ->willReturn($cnames);

        $cnames->method('get')->willReturn($cname);

        $cnames->expects($this->exactly(2))
            ->method('hasDomain')
            ->with($domain)
            ->willReturnOnConsecutiveCalls(true, false);

        $this->repository->expects($this->exactly(1))
            ->method('update');
        $this->listener->__invoke($dto);
    }
}
