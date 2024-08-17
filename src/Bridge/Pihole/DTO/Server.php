<?php

/*
 * This file is part of the DockerDNS project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DockerDNS\Bridge\Pihole\DTO;

use GuzzleHttp\Client as GuzzleClient;

class Server
{
    public string $name;

    public function __construct(
        public string $url,
        public string $token,
        ?string $name = null,
        private ?GuzzleClient $guzzle = null
    ) {
        if (is_null($this->guzzle)) {
            $this->guzzle = new GuzzleClient([
                'base_uri' => $url.'/admin/api.php',
            ]);
        }

        if (is_null($name)) {
            $parsed = parse_url($url);
            $name = $parsed['host'];
        }
        $this->name = $name;
    }

    public function getCNames(): CNameCollection
    {
        $guzzle = $this->guzzle;
        $response = $guzzle->get('', [
            'query' => [
                'customcname' => '',
                'action' => 'get',
                'auth' => $this->token,
            ],
        ]);

        $body = $response->getBody()->getContents();

        return CNameCollection::fromJson($body);
    }

    public function removeCName(CName $cname): void
    {
        $guzzle = $this->guzzle;
        $guzzle->post('', [
            'query' => [
                'customcname' => '',
                'action' => 'delete',
                'target' => $cname->target,
                'domain' => $cname->domain,
                'auth' => $this->token,
            ],
        ]);
    }

    public function addCName(string $domain, string $target): void
    {
        $guzzle = $this->guzzle;
        $guzzle->post('', [
            'query' => [
                'customcname' => '',
                'action' => 'add',
                'target' => $target,
                'domain' => $domain,
                'auth' => $this->token,
            ],
        ]);

        return;
    }
}
