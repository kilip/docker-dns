<?php

namespace DockerDNS\Bridge\Pihole;

use InvalidArgumentException;

class PiholeException extends \Exception
{
    public static function cnameRecordNotExists(string $domain): static
    {
        throw new InvalidArgumentException(
            sprintf("This server doesn't have cname with domain %s", $domain)
        );
    }
}