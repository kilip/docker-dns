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

class Pihole
{
    public const LABEL_CNAME_DOMAIN = 'dockerdns.pihole.cname.domain';
    public const LABEL_CNAME_TARGET = 'dockerdns.pihole.cname.target';
}
