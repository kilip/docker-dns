<?php

/*
 * This file is part of the DockerDNS project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DockerDNS\Bridge\Docker\Event;

use DockerDNS\Bridge\Docker\DTO\Container;

class CleanUpEvent
{
    /**
     * @param array<string, Container> $containers
     */
    public function __construct(
        public array $containers
    ) {
    }
}
