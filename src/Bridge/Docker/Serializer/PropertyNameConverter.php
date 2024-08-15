<?php

namespace DockerDNS\Bridge\Docker\Serializer;

use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class PropertyNameConverter implements NameConverterInterface
{
    /**
     * Converts a property name to its normalized value.
     *
     * @param class-string|null    $class
     * @param string|null          $format
     * @param array<string, mixed> $context
     */
    public function normalize(string $propertyName): string
    {
        return ucfirst($propertyName);
    }

    /**
     * Converts a property name to its denormalized value.
     *
     * @param class-string|null    $class
     * @param string|null          $format
     * @param array<string, mixed> $context
     */
    public function denormalize(string $propertyName): string
    {
        return lcfirst($propertyName);
    }
}