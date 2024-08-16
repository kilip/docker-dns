<?php

/*
 * This file is part of the DockerDNS project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DockerDNS\Bridge\Docker\Repository;

use DockerDNS\Bridge\Docker\DTO\Container as ContainerDTO;
use DockerDNS\Bridge\Docker\Entity\Container;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Container>
 */
class ContainerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Container::class);
    }

    public function findByContainerId(string $containerId): ?Container
    {
        return $this->findOneBy(['containerId' => $containerId]);
    }

    public function create(ContainerDTO $dto): void
    {
        $container = new Container();
        $container->name = $dto->getName();
        $container->containerId = $dto->id;
        $container->labels = $dto->labels;

        $this->getEntityManager()->persist($container);
        $this->getEntityManager()->flush($container);
    }

    public function remove(Container $container): void
    {
        $this->getEntityManager()->remove($container);
        $this->getEntityManager()->flush();
    }
}
