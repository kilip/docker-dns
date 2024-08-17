<?php

/*
 * This file is part of the DockerDNS project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DockerDNS\Bridge\Pihole\Listener;

use DockerDNS\Bridge\Docker\DTO\Container;
use DockerDNS\Bridge\Docker\Docker;
use DockerDNS\Bridge\Pihole\DTO\Server;
use DockerDNS\Bridge\Pihole\Pihole;
use DockerDNS\Bridge\Pihole\Repository\CNameRepository;
use DockerDNS\Bridge\Pihole\ServerRegistry;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: Docker::EVENT_PROCESS)]
#[WithMonologChannel('pihole')]
class ProcessContainerListener
{
    public function __construct(
        private ServerRegistry $registry,
        private CNameRepository $repository,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(Container $container): void
    {
        if ($container->hasLabel(Pihole::LABEL_CNAME_DOMAIN)) {
            $this->process($container);
        }
    }

    private function process(Container $container): void
    {
        $servers = $this->registry->servers;
        $targets = [];

        if ($container->hasLabel(Pihole::LABEL_CNAME_DOMAIN) && $container->hasLabel(Pihole::LABEL_CNAME_TARGET)) {
            $targets[] = [
                $container->getLabelValue(Pihole::LABEL_CNAME_DOMAIN),
                $container->getLabelValue(Pihole::LABEL_CNAME_TARGET),
            ];
        }

        $process = true;
        $index = 0;
        while ($process) {
            $labelDomain = "dockerdns.pihole.cname.{$index}.domain";
            $labelTarget = "dockerdns.pihole.cname.{$index}.target";
            if ($container->hasLabel($labelDomain) && $container->hasLabel($labelTarget)) {
                $targets[] = [
                    $container->getLabelValue($labelDomain),
                    $container->getLabelValue($labelTarget),
                ];
                ++$index;
            } else {
                $process = false;
            }
        }

        foreach ($targets as $definition) {
            list($domain, $target) = $definition;
            foreach ($servers as $server) {
                $this->processServer($server, $container, $domain, $target);
            }
        }
    }

    private function processServer(Server $server, Container $container, string $domain, string $target): void
    {
        $logger = $this->logger;
        $cnames = $server->getCNames();
        $repository = $this->repository;

        if ($cnames->hasDomain($domain) && $cnames->get($domain)->target != $target) {
            // need to delete domain first
            $server->removeCName($cnames->get($domain));
            $logger->notice('{0}: removed cname {1} target {2}', [
                $server->name,
                $cnames->get($domain)->domain,
                $cnames->get($domain)->target,
            ]);
            $cnames->remove($domain);
            $repository->remove(
                $container->id,
                $domain,
                $target
            );
        }

        if (!$cnames->hasDomain($domain)) {
            $server->addCName($domain, $target);
            $logger->notice('{0}: added cname domain: {1} target: {2}', [
                $server->name,
                $domain,
                $target,
            ]);
        }
        $repository->update($container->id, $domain, $target);
    }
}
