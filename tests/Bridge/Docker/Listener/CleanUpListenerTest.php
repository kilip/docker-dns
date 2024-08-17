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

use DockerDNS\Bridge\Docker\Docker;
use DockerDNS\Bridge\Docker\Entity\Container;
use DockerDNS\Bridge\Docker\Event\CleanUpEvent;
use DockerDNS\Bridge\Docker\Event\ContainerRemovedEvent;
use DockerDNS\Bridge\Docker\Listener\CleanUpListener;
use DockerDNS\Bridge\Docker\Repository\ContainerRepository;
use DockerDNS\Tests\Fixtures;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CleanUpListenerTest extends TestCase
{
    private MockObject|ContainerRepository $repository;
    private MockObject|EventDispatcherInterface $dispatcher;
    private MockObject|LoggerInterface $logger;
    private MockObject|CleanUpListener $listener;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(ContainerRepository::class);
        $this->dispatcher = $this->createMock(EventDispatcher::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->listener = new CleanUpListener(
            $this->repository,
            $this->dispatcher,
            $this->logger
        );
    }

    public function testInvoke(): void
    {
        $containers = Fixtures::createContainers();
        $event = new CleanUpEvent($containers);
        $entity = new Container();
        $entity->containerId = 'some-id';
        $entity->name = 'name';

        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn([$entity])
        ;

        $this->repository->expects($this->once())
            ->method('remove')
            ->with($entity);

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(ContainerRemovedEvent::class), Docker::EVENT_CONTAINER_REMOVED)
        ;

        $this->listener->__invoke($event);
    }
}
