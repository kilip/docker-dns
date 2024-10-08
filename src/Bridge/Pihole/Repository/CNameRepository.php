<?php

/*
 * This file is part of the DockerDNS project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DockerDNS\Bridge\Pihole\Repository;

use DockerDNS\Bridge\Pihole\Entity\CName;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CName>
 */
class CNameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $manager)
    {
        parent::__construct($manager, CName::class);
    }

    /**
     * @return array<int, CName>
     */
    public function findByContainer(string $containerId): array
    {
        return $this->findBy(['containerId' => $containerId]);
    }

    public function findByDomainAndTarget(string $domain, string $target): ?CName
    {
        return $this->findOneBy([
            'domain' => $domain,
            'target' => $target,
        ]);
    }

    public function update(string $containerId, string $domain, string $target): void
    {
        $cname = $this->findByDomainAndTarget($domain, $target);
        if (is_null($cname)) {
            $cname = new CName();
            $cname->domain = $domain;
            $cname->target = $target;
        }
        $cname->containerId = $containerId;

        $this->getEntityManager()->persist($cname);
        $this->getEntityManager()->flush();
    }

    public function remove(string $containerId, ?string $domain = null, ?string $target = null): void
    {
        $filters['containerId'] = $containerId;
        if (!is_null($domain)) {
            $filters['domain'] = $domain;
        }

        if (!is_null($target)) {
            $filters['target'] = $target;
        }

        $items = $this->findBy($filters);
        foreach ($items as $cname) {
            $this->getEntityManager()->remove($cname);
            $this->getEntityManager()->flush();
        }
    }
}
