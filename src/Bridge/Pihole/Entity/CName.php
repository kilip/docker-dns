<?php

namespace DockerDNS\Bridge\Pihole\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: 'pihole_cname')]
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
