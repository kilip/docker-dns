<?php

/*
 * This file is part of the DockerDNS project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DockerDNS\Bridge\Docker\Entity;

use DockerDNS\Bridge\Docker\Repository\ContainerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContainerRepository::class)]
#[ORM\Table(name: 'docker_containers')]
class Container
{
    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue()]
    public ?int $id = null;

    #[ORM\Column(type: 'string', unique: true)]
    public string $containerId;

    #[ORM\Column(type: 'string')]
    public string $name;

    /**
     * @var array<string,string>
     */
    #[ORM\Column(type: 'array')]
    public array $labels;
}
