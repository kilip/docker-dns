<?php

/*
 * This file is part of the DockerDNS project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DockerDNS\Bridge\Docker\Listener;

use DockerDNS\Bridge\Docker\Docker;
use DockerDNS\Bridge\Docker\Entity\Container;
use DockerDNS\Bridge\Docker\Event\CleanUpEvent;
use DockerDNS\Bridge\Docker\Event\ContainerRemovedEvent;
use DockerDNS\Bridge\Docker\Repository\ContainerRepository;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[WithMonologChannel('docker')]
#[AsEventListener(event: Docker::EVENT_CLEANUP)]
class CleanUpListener
{
    public function __construct(
        private ContainerRepository $repository,
        private EventDispatcherInterface $dispatcher,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(CleanUpEvent $event): void
    {
        $repository = $this->repository;
        $dispatcher = $this->dispatcher;
        $logger = $this->logger;
        $containers = $event->containers;

        /** @var Container $container */
        foreach ($repository->findAll() as $container) {
            $id = $container->containerId;
            if (!array_key_exists($id, $containers)) {
                $event = new ContainerRemovedEvent($container);
                $dispatcher->dispatch($event, Docker::EVENT_CONTAINER_REMOVED);
                $repository->remove($container);
                $logger->notice('removed container {0}', [$container->name]);
            }
        }
    }
}
