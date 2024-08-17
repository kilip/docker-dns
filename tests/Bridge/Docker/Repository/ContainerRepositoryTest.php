<?php

/*
 * This file is part of the DockerDNS project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DockerDNS\Tests\Bridge\Docker\Repository;

use DockerDNS\Bridge\Docker\Entity\Container;
use DockerDNS\Bridge\Docker\Repository\ContainerRepository;
use DockerDNS\Tests\Fixtures;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ContainerRepositoryTest extends KernelTestCase
{
    private ?ObjectManager $manager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->manager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    private function ensureContainersCleaned(string $containerId): void
    {
        /** @var ContainerRepository $repository */
        $repository = $this->manager->getRepository(Container::class);
        $ob = $repository->findByContainerId($containerId);

        if ($ob instanceof Container) {
            $this->manager->remove($ob);
            $this->manager->flush();
        }
    }

    public function testCRUD(): void
    {
        /** @var ContainerRepository $repository */
        $repository = $this->manager->getRepository(Container::class);
        $dto = Fixtures::createContainers()[Fixtures::CONTAINER_KEY_WHOAMI];

        $this->ensureContainersCleaned($dto->id);
        $this->assertInstanceOf(ContainerRepository::class, $repository);
        $repository->create($dto);
    }
}
