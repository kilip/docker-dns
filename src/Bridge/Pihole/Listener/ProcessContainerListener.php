<?php

namespace DockerDNS\Bridge\Pihole\Listener;

use DockerDNS\Bridge\Pihole\Client as PiholeClient;
use DockerDNS\Bridge\Docker\Container;
use DockerDNS\Bridge\Pihole\DTO\Server;
use DockerDNS\Bridge\Pihole\Pihole;
use DockerDNS\Constants;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: Constants::PROCESS_CONTAINER)]
class ProcessContainerListener
{
    /**
     * @var array<int, Server>
     */
    private array $servers = [];

    /**
     * @param array<int,array> $servers
     */
    public function __construct(
        #[Autowire(param: 'dockerdns.pihole.servers')]
        array $servers,
        private LoggerInterface $logger
    )
    {
        foreach($servers as $definition){
            $this->servers[] = new Server(
                $definition['url'],
                $definition['token']
            );
        }
    }

    public function __invoke(Container $container): void
    {
        if($container->hasLabel(Pihole::LABEL_CNAME_DOMAIN)){
            $this->process($container);           
        }
    }

    private function process(Container $container): void
    {
        $logger = $this->logger;
        

        if($container->hasLabel(Pihole::LABEL_CNAME_DOMAIN) && $container->hasLabel(Pihole::LABEL_CNAME_TARGET)){
            $logger->debug('pihole: processing container {0}', [$container->getName()]);
            $servers = $this->servers;
            foreach($servers as $server){
                $this->processServer($container, $server);
            }
        }
    }

    private function processServer(Container $container, Server $server): void
    {
        $logger = $this->logger;
        $cnames = $server->getCNames();
        $domain = $container->getLabelValue(Pihole::LABEL_CNAME_DOMAIN);
        $target = $container->getLabelValue(Pihole::LABEL_CNAME_TARGET);

        if($cnames->hasDomain($domain) && $cnames->get($domain)->target != $target){
            // need to delete domain first
            $server->removeCName($cnames->get($domain));
            $logger->debug('removed cname {0} target {1}', [
                $cnames->get($domain)->domain,
                $cnames->get($domain)->target
            ]);
            $cnames->remove($domain);
        }
        
        if(!$cnames->hasDomain($domain)){
            $server->addCName($domain, $target);
            $logger->info("added cname domain: {0} target: {1}", [$domain, $target]);
        }
    }
}