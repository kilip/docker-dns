<?php

/*
 * This file is part of the DockerDNS project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DockerDNS\Tests\Bridge\Pihole\DTO;

use DockerDNS\Bridge\Pihole\DTO\CName;
use DockerDNS\Bridge\Pihole\DTO\CNameCollection;
use DockerDNS\Tests\Fixtures;
use PHPUnit\Framework\TestCase;

class CNameCollectionTest extends TestCase
{
    private CNameCollection $cnames;

    protected function setUp(): void
    {
        $this->cnames = CNameCollection::fromJson(Fixtures::cnamesFileContent());
    }

    public function testArrayAccess(): void
    {
        $cnames = $this->cnames;
        $test1 = 'test1.home.lan';
        $target = 'server.home.lan';
        $this->assertTrue(isset($cnames[$test1]));
        $this->assertInstanceOf(CName::class, $cname = $cnames[$test1]);
        $cname->target = 'server1.home.lan';

        $cnames[$test1] = $cname;
        $this->assertSame($cname, $cnames[$test1]);
    }

    public function testCrud(): void
    {
        $cnames = $this->cnames;
        $test1 = 'test1.home.lan';

        $this->assertTrue($cnames->hasDomain($test1));
        $this->assertInstanceOf(CName::class, $cnames->get($test1));
        $cnames->remove($test1);
        $this->assertFalse($cnames->hasDomain($test1));
    }

    public function testGetException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->cnames->get('foo.bar');
    }

    public function testRemoveException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->cnames->remove('foo.bar');
    }
}
