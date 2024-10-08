<?php

/*
 * This file is part of the DockerDNS project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
    public function normalize(string $propertyName/* , ?string $class = null, ?string $format = null, array $context = [] */): string
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
    public function denormalize(string $propertyName/* , ?string $class = null, ?string $format = null, array $context = [] */): string
    {
        return lcfirst($propertyName);
    }
}
