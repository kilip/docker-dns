<?php

/*
 * This file is part of the DockerDNS project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DockerDNS\Bridge\Docker;

use DockerDNS\Bridge\Docker\DTO\Container;
use DockerDNS\Bridge\Docker\Serializer\PropertyNameConverter;
use GuzzleHttp\Client as GuzzleClient;
use Symfony\Component\PropertyInfo\Extractor\ConstructorExtractor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class Client
{
    private GuzzleClient $guzzle;

    public function __construct(
        ?GuzzleClient $guzzle = null
    ) {
        // @codeCoverageIgnoreStart
        if (is_null($guzzle)) {
            $guzzle = new GuzzleClient([
                'base_uri' => 'http://localhost/v1.46',
                'curl' => [
                    CURLOPT_UNIX_SOCKET_PATH => '/var/run/docker.sock',
                ],
            ]);
        }
        // @codeCoverageIgnoreEnd

        $this->guzzle = $guzzle;
    }

    /**
     * @return array<string, Container>
     */
    public function getContainers(): array
    {
        $response = $this->guzzle->request('GET', '/containers/json?all=true');
        $json = $response->getBody()->getContents();

        $phpDocExtractor = new PhpDocExtractor();
        $nameConverter = new PropertyNameConverter();
        $typeExtractor = new PropertyInfoExtractor(
            typeExtractors: [
                new ConstructorExtractor([$phpDocExtractor]),
                $phpDocExtractor,
            ],
        );

        $encoders = [new JsonEncoder()];
        $normalizers = [
            new ObjectNormalizer(propertyTypeExtractor: $typeExtractor, nameConverter: $nameConverter),
            new ArrayDenormalizer(),
        ];
        $serializer = new Serializer($normalizers, $encoders);

        /** @var array<int, Container> $containers */
        $containers = $serializer->deserialize($json, Container::class . '[]', 'json');

        $mapped = [];
        foreach ($containers as $container) {
            $mapped[$container->id] = $container;
        }

        return $mapped;
    }
}
