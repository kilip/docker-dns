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

class PiholeException extends \Exception
{
    public static function cnameRecordNotExists(string $domain): static
    {
        throw new \InvalidArgumentException(sprintf("This server doesn't have cname with domain %s", $domain));
    }
}
