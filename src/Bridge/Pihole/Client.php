<?php

/*
 * This file is part of the DockerDNS project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DockerDNS\Bridge\Pihole;

use GuzzleHttp\Client as GuzzleClient;

class Client
{
    private GuzzleClient $guzzle;

    public function __construct(
        private string $url,
        private string $apiToken,

        ?GuzzleClient $guzzle = null
    ) {
        if (is_null($guzzle)) {
            $guzzle = $this->createGuzzle();
        }
        $this->guzzle = $guzzle;
    }

    private function createGuzzle(): GuzzleClient
    {
        $guzzle = new GuzzleClient([
            'base_uri' => $this->url.'/admin/api.php',
            'defaults' => [
                'query' => [
                    'auth' => $this->apiToken,
                ],
            ],
        ]);

        return $guzzle;
    }

    public function getCustomDNS(): array
    {
        $guzzle = $this->guzzle;
        $response = $guzzle->get('', [
            'query' => [
                'customcname' => '',
                'action' => 'get',
                'auth' => $this->apiToken,
            ],
        ]);
        $json = $response->getBody()->getContents();

        return json_decode($json, true);
    }
}
