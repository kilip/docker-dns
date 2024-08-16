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

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\AbstractExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

class PiholeExtension extends AbstractExtension
{
    private function configureServers(ContainerConfigurator $container): void
    {
        $servers = [];
        if (isset($_ENV['DOCKERDNS_PIHOLE_URL']) && isset($_ENV['DOCKERDNS_PIHOLE_TOKEN'])) {
            $servers[] = [
                'url' => $_ENV['DOCKERDNS_PIHOLE_URL'],
                'token' => $_ENV['DOCKERDNS_PIHOLE_TOKEN'],
            ];
        }

        $scanEnv = true;
        $index = 0;
        while ($scanEnv) {
            $envUrl = "DOCKERDNS_PIHOLE_{$index}_URL";
            $envToken = "DOCKERDNS_PIHOLE_{$index}_TOKEN";
            if (isset($_ENV[$envUrl]) && isset($_ENV[$envToken])) {
                $servers[] = [
                    'url' => $_ENV[$envUrl],
                    'token' => $_ENV[$envToken],
                ];
                ++$index;
            } else {
                $scanEnv = false;
            }
        }

        $container->parameters()->set('dockerdns.pihole.servers', $servers);
    }

    public function configure(DefinitionConfigurator $definition): void
    {
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $this->configureServers($container);
    }

    public function getAlias(): string
    {
        return 'pihole';
    }
}
