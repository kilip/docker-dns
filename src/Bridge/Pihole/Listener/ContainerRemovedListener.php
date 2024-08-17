<?php

/*
 * This file is part of the DockerDNS project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DockerDNS\Bridge\Pihole\Listener;

use DockerDNS\Bridge\Docker\Docker;
use DockerDNS\Bridge\Docker\Event\ContainerRemovedEvent;
use DockerDNS\Bridge\Pihole\DTO\CName as CNameDTO;
use DockerDNS\Bridge\Pihole\Entity\CName;
use DockerDNS\Bridge\Pihole\Repository\CNameRepository;
use DockerDNS\Bridge\Pihole\ServerRegistry;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: Docker::EVENT_CONTAINER_REMOVED)]
#[WithMonologChannel('pihole')]
class ContainerRemovedListener
{
    public function __construct(
        private ServerRegistry $registry,
        private CNameRepository $repository,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(ContainerRemovedEvent $event): void
    {
        $container = $event->container;
        $logger = $this->logger;
        $servers = $this->registry->servers;
        $repository = $this->repository;
        $items = $repository->findByContainer($container->containerId);

        /** @var CName $item */
        foreach ($items as $item) {
            foreach ($servers as $server) {
                $dto = new CNameDTO($item->domain, $item->target);
                $server->removeCName($dto);
                $logger->notice('{0}: removed container {1} domain: {2} target: {3}', [
                    $server->name,
                    $container->name,
                    $item->domain,
                    $item->target,
                ]);
            }
        }

        $repository->remove($container->containerId);
    }
}
