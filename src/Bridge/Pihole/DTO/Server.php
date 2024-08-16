<?php

namespace DockerDNS\Bridge\Pihole\DTO;

use GuzzleHttp\Client as GuzzleClient;

class Server
{
    public function __construct(
        public string $url,
        public string $token,
        private ?GuzzleClient $guzzle = null
    )
    {
        if(is_null($this->guzzle)){
            $this->guzzle = new GuzzleClient([
                'base_uri' => $url.'/admin/api.php',
            ]);
        }
    }

    public function getCNames(): CNameCollection
    {
        $guzzle = $this->guzzle;
        $response = $guzzle->get('', [
            'query' => [
                'customcname' => '',
                'action' => 'get',
                'auth' => $this->token
            ]
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
                'auth' => $this->token
            ]
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
            ]
        ]);

        return;
    }
}