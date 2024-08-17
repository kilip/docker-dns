<?php

/*
 * This file is part of the DockerDNS project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DockerDNS\Tests\Bridge\Pihole;

use DockerDNS\Bridge\Pihole\DTO\CName;
use DockerDNS\Bridge\Pihole\DTO\CNameCollection;
use DockerDNS\Bridge\Pihole\Server;
use DockerDNS\Tests\Fixtures;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class ServerTest extends TestCase
{
    private MockObject|GuzzleClient $guzzle;
    private MockObject|Response $response;
    private MockObject|StreamInterface $body;
    private Server $server;

    protected function setUp(): void
    {
        $this->guzzle = $this->createMock(GuzzleClient::class);
        $this->response = $this->createMock(Response::class);
        $this->body = $this->createMock(StreamInterface::class);

        $this->response->method('getBody')
            ->willReturn($this->body);

        $this->server = new Server(
            'http://localhost',
            'token',
            null,
            $this->guzzle
        );
    }

    public function testGetCNames(): void
    {
        $expectedOptions = [
            'query' => [
                'customcname' => '',
                'action' => 'get',
                'auth' => 'token',
            ],
        ];
        $this->guzzle->expects($this->once())
            ->method('get')
            ->with('', $expectedOptions)
            ->willReturn($this->response)
        ;
        $this->body->expects($this->once())
            ->method('getContents')
            ->willReturn(Fixtures::cnamesFileContent());

        $cnames = $this->server->getCNames();
        $this->assertInstanceOf(CNameCollection::class, $cnames);
    }

    public function testRemoveCName(): void
    {
        $expectedOptions = [
            'query' => [
                'customcname' => '',
                'action' => 'delete',
                'target' => 'target',
                'domain' => 'domain',
                'auth' => 'token',
            ],
        ];
        $this->guzzle->expects($this->once())
            ->method('post')
            ->with('', $expectedOptions)
            ->willReturn($this->response)
        ;

        $this->server->removeCName(new CName('domain', 'target'));
    }

    public function testAddCName(): void
    {
        $expectedOptions = [
            'query' => [
                'customcname' => '',
                'action' => 'add',
                'target' => 'target',
                'domain' => 'domain',
                'auth' => 'token',
            ],
        ];
        $this->guzzle->expects($this->once())
            ->method('post')
            ->with('', $expectedOptions)
            ->willReturn($this->response)
        ;

        $this->server->addCName('domain', 'target');
    }
}
