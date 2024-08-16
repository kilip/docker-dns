<?php

namespace DockerDNS\Bridge\Pihole\DTO;

class CName
{
    public function __construct(
        public string $domain,
        public string $target
    )
    {
        
    }
}