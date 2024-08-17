<?php

/*
 * This file is part of the DockerDNS project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DockerDNS\Command;

use DockerDNS\Constants;
use DockerDNS\Event\UpdateEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class StartCommand extends Command
{
    public function __construct(
        private EventDispatcherInterface $dispatcher,
        private LoggerInterface $logger
    ) {
        parent::__construct('start');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        pcntl_async_signals(true);
        $event = new UpdateEvent();
        pcntl_signal(SIGTERM, function () use (&$event) {
            $event->interrupt = true;
        });

        while (!$event->interrupt) {
            try {
                $this->dispatcher->dispatch($event, Constants::EVENT_START);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
            sleep(5);
        }

        return static::SUCCESS;
    }
}
