<?php

/*
 * This file is part of the DockerDNS project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DockerDNS\Tests\Bridge\Docker\Listener;

use DockerDNS\Bridge\Docker\Client as DockerClient;
use DockerDNS\Bridge\Docker\DTO\Container;
use DockerDNS\Bridge\Docker\Docker;
use DockerDNS\Bridge\Docker\Event\CleanupEvent;
use DockerDNS\Bridge\Docker\Listener\StartListener;
use DockerDNS\Bridge\Docker\Repository\ContainerRepository;
use DockerDNS\Tests\Fixtures;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class StartListenerTest extends TestCase
{
    private ContainerRepository $repository;
    private EventDispatcherInterface $dispatcher;
    private LoggerInterface $logger;
    private DockerClient $docker;
    private StartListener $listener;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(ContainerRepository::class);
        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->docker = $this->createMock(DockerClient::class);
        $this->listener = new StartListener(
            $this->repository,
            $this->dispatcher,
            $this->logger,
            $this->docker
        );
    }

    public function testInvoke(): void
    {
        $containers = Fixtures::createContainers();
        $this->docker->expects($this->once())
            ->method('getContainers')
            ->willReturn($containers);

        $this->repository->expects($this->exactly(2))
            ->method('findByContainerId');
        $this->repository->expects($this->exactly(2))
            ->method('create');

        $this->dispatcher->expects($this->exactly(3))
            ->method('dispatch')
            ->withConsecutive(
                [$this->isInstanceOf(Container::class), Docker::EVENT_PROCESS],
                [$this->isInstanceOf(Container::class), Docker::EVENT_PROCESS],
                [$this->isInstanceOf(CleanupEvent::class), Docker::EVENT_CLEANUP],
            )
        ;

        $this->listener->__invoke();
    }
}
