<?php

/*
 * This file is part of the DockerDNS project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DockerDNS\Bridge\Pihole;

use DockerDNS\Bridge\Pihole\DTO\Server;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ServerRegistry
{
    /**
     * @var array<int, Server>
     */
    public array $servers = [];

    /**
     * @param array<int, array<string,string>> $servers
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
