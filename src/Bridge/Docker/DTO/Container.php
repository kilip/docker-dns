<?php

/*
 * This file is part of the DockerDNS project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DockerDNS\Bridge\Docker\DTO;

class Container
{
    /**
     * @param array<string,int|string|bool|float> $labels
     * @param array<int,string>                   $names
     */
    public function __construct(
        public string $id,
        public array $names,
        public string $image,
        public string $imageID,
        public int $created,
        public array $ports,
        public array $labels,
    ) {
    }

    public function getName(): string
    {
        $name = $this->names[0];

        return trim($name, '/');
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
        if ($this->hasLabel($label)) {
            return $this->labels[$label];
        }

        return $default;
    }
}
