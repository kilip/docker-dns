<?php

/*
 * This file is part of the DockerDNS project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DockerDNS\Bridge\Docker;

class Docker
{
    public const EVENT_PROCESS = 'dockerdns.docker.process';
    public const EVENT_CLEANUP = 'dockerdns.docker.cleanup';
    public const EVENT_CONTAINER_REMOVED = 'dockerdns.docker.removed';
}
