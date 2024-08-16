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

use DockerDNS\Bridge\Docker\Client as DockerClient;
use DockerDNS\Bridge\Docker\DTO\Container;
use DockerDNS\Bridge\Docker\Docker;
use DockerDNS\Bridge\Docker\Event\CleanUpEvent;
use DockerDNS\Bridge\Docker\Repository\ContainerRepository;
use DockerDNS\Constants;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[AsEventListener(event: Constants::EVENT_START)]
#[WithMonologChannel('docker')]
class StartListener
{
    public function __construct(
        private ContainerRepository $repository,
        private EventDispatcherInterface $dispatcher,
        private LoggerInterface $logger,
        private DockerClient $docker
    ) {}

    public function __invoke()
    {
        $dispatcher = $this->dispatcher;
        $containers = $this->docker->getContainers();

        foreach ($containers as $container) {
            $this->register($container);
            $dispatcher->dispatch($container, Docker::EVENT_PROCESS);
        }

        $event = new CleanUpEvent($containers);
        $dispatcher->dispatch($event, Docker::EVENT_CLEANUP);
    }

    private function register(Container $container): void
    {
        $repository = $this->repository;
        $logger = $this->logger;

        if (null === $repository->findByContainerId($container->id)) {
            $repository->create($container);
            $logger->notice('registered container {0}', [$container->getName()]);
        }
    }
}
