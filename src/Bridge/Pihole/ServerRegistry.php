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

use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * @implements \ArrayAccess<int, Server>
 */
class ServerRegistry implements \ArrayAccess, \Countable
{
    /**
     * @var array<int, Server>
     */
    private array $servers = [];

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

    public function count(): int
    {
        return count($this->servers);
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->servers);
    }

    public function offsetGet(mixed $offset): Server
    {
        return $this->servers[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->servers[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->servers[$offset]);
    }

    /**
     * @return array<int, Server>
     */
    public function getAll(): array
    {
        return $this->servers;
    }
}
