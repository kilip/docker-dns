<?php

/*
 * This file is part of the DockerDNS project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DockerDNS\Bridge\Pihole\Entity;

use DockerDNS\Bridge\Pihole\Repository\CNameRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CNameRepository::class)]
#[ORM\Table(name: 'pihole_cname')]
#[ORM\UniqueConstraint('cname', ['domain', 'target'])]
class CName
{
    #[ORM\Id]
    #[ORM\GeneratedValue()]
    #[ORM\Column()]
    public int $id;

    #[ORM\Column(type: 'string')]
    public string $containerId;

    #[ORM\Column(type: 'string')]
    public string $domain;

    #[ORM\Column(type: 'string')]
    public string $target;
}
