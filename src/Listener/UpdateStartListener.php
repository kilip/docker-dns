<?php

namespace DockerDNS\Listener;

use DockerDNS\Bridge\Docker\Client as DockerClient;
use DockerDNS\Constants;
use DockerDNS\Event\UpdateEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[AsEventListener(event: Constants::UPDATE_START)]
class UpdateStartListener 
{
    public function __construct(
        private EventDispatcherInterface $dispatcher,
        private LoggerInterface $logger,
        private DockerClient $docker
    )
    {
        
    }

    public function __invoke()
    {
        $logger = $this->logger;
        $dispatcher = $this->dispatcher;
        $logger->info('start updating dns');
        $containers = $this->docker->getContainers();
        $logger->debug('found {0} container', [count($containers)]);
        for($i=0;$i<count($containers); $i++){
            /** @var Container $container */
            $container = $containers[$i];
            $dispatcher->dispatch($container, Constants::PROCESS_CONTAINER);
        }
        $logger->info('completed');
    }
}