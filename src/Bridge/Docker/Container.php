<?php

namespace DockerDNS\Bridge\Docker;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class Container
{
    /**
     * @param array<string,int|string|bool|float> $labels
     */
    public function __construct(
        public string $id,
        public array $names,
        public string $image,
        public string $imageID,
        public int $created,
        public array $ports,
        public array $labels,
    )
    {
    }

    public function hasLabel(string $label): bool
    {
        return array_key_exists($label, $this->labels);
    }

    /**
     * @return string|int|bool|float
     */
    public function getLabelValue(string $label, $default = null): mixed
    {
        if($this->hasLabel($label)){
            return $this->labels[$label];
        }

        return $default;
    }
}
