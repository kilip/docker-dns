<?php

/*
 * This file is part of the DockerDNS project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DockerDNS\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class StartCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        static::bootKernel();
        $app = new Application(static::$kernel);
        $command = $app->find('start');
        $tester = new CommandTester($command);

        $tester->execute([
            '-vv' => true,
        ]);

        $tester->assertCommandIsSuccessful();
    }
}
