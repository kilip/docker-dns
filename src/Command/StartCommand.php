<?php

namespace DockerDNS\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartCommand extends Command
{
    public function __construct()
    {
        parent::__construct('start');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return static::SUCCESS;
    }
}
