<?php

/*
 * This file is part of the DockerDNS project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DockerDNS\Tests\Bridge\Pihole\Repository;

use DockerDNS\Bridge\Pihole\Entity\CName;
use DockerDNS\Bridge\Pihole\Repository\CNameRepository;
use DockerDNS\Tests\Fixtures;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CNameRepositoryTest extends KernelTestCase
{
    private ?ObjectManager $manager;

    protected function setUp(): void
    {
        $kernel = static::bootKernel();

        $this->manager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testCrud(): void
    {
        /** @var CNameRepository $repository */
        $repository = $this->manager->getRepository(CName::class);
        $dto = Fixtures::createContainers()[Fixtures::CONTAINER_KEY_WHOAMI];

        $repository->update($dto->id, 'domain', 'target');

        $cnames = $repository->findByContainer($dto->id);
        $this->assertCount(1, $cnames);
        $this->assertInstanceOf(CName::class, $cnames[0]);

        $cname = $repository->findByDomainAndTarget('domain', 'target');
        $this->assertInstanceOf(CName::class, $cname);

        $repository->remove($dto->id, 'domain', 'target');
        $cnames = $repository->findByContainer($dto->id);
        $this->assertCount(0, $cnames);
    }
}
