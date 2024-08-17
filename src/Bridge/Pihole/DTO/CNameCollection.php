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

class CNameCollection
{
    /**
     * @param array<int, CName> $cnames
     */
    public function __construct(
        public array $cnames
    ) {
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
        return array_key_exists($domain, $this->cnames);
    }

    public function remove(string $domain): void
    {
        if ($this->hasDomain($domain)) {
            unset($this->cnames[$domain]);
        }

        throw PiholeException::cnameRecordNotExists($domain);
    }

    public function get(string $domain): CName
    {
        if ($this->hasDomain($domain)) {
            return $this->cnames[$domain];
        }
        throw PiholeException::cnameRecordNotExists($domain);
    }
}
