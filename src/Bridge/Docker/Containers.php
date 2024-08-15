<?php

namespace DockerDNS\Bridge\Docker;

use ArrayAccess;
use Countable;
use DockerDNS\Bridge\Docker\Serializer\PropertyNameConverter;
use Symfony\Component\PropertyInfo\Extractor\ConstructorExtractor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class Containers implements ArrayAccess, Countable
{
    /**
     * @var Container[]
     */
    public function __construct(
        private array $containers,
    )
    {  
    }

    public function count(): int
    {
        return count($this->containers);
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->containers);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->containers[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->containers[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->containers[$offset]);
    }

    public static function fromJson(string $json): static
    {
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
            new ArrayDenormalizer()
        ];
        $serializer = new Serializer($normalizers, $encoders);
        $containers = $serializer->deserialize($json, Container::class.'[]', 'json');
        return new static($containers);
    }

}