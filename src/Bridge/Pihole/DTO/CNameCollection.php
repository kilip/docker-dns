<?php

/*
 * This file is part of the DockerDNS project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DockerDNS\Bridge\Pihole\DTO;

use DockerDNS\Bridge\Pihole\PiholeException;

/**
 * @implements \ArrayAccess<string, CName>
 */
class CNameCollection implements \ArrayAccess
{
    /**
     * @param array<int, CName> $cnames
     */
    public function __construct(
        public array $cnames
    ) {
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->cnames);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->cnames[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->cnames[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->cnames[$offset]);
    }

    public static function fromJson(string $json): CNameCollection
    {
        $data = json_decode($json, true)['data'];

        $cnames = [];
        foreach ($data as $record) {
            $domain = $record[0];
            $target = $record[1];
            $cnames[$domain] = new CName(
                $domain,
                $target
            );
        }

        return new CNameCollection($cnames);
    }

    public function hasDomain(string $domain): bool
    {
        return $this->offsetExists($domain);
    }

    public function remove(string $domain): void
    {
        if (!$this->hasDomain($domain)) {
            throw PiholeException::cnameRecordNotExists($domain);
        }

        unset($this[$domain]);
    }

    public function get(string $domain): CName
    {
        if ($this->hasDomain($domain)) {
            return $this[$domain];
        }
        throw PiholeException::cnameRecordNotExists($domain);
    }
}
