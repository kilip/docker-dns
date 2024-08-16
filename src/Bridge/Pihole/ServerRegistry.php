<?php

namespace DockerDNS\Bridge\Pihole;

use DockerDNS\Bridge\Pihole\DTO\Server;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ServerRegistry
{
    /**
     * @var array<int, Server>
     */
    public array $servers;

    /**
     * @param array<int,array> $servers
     */
    public function __construct(
        #[Autowire(param: 'dockerdns.pihole.servers')]
        array $servers
    ) {
        foreach ($servers as $definition) {
            $this->servers[] = new Server(
                $definition['url'],
                $definition['token']
            );
        }
    }
}
