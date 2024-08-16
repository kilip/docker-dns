<?php

namespace DockerDNS\Command;

use DockerDNS\Constants;
use DockerDNS\Event\UpdateEvent;
use PHPUnit\TextUI\XmlConfiguration\Constant;
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
    )
    {
        parent::__construct('start');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        pcntl_async_signals(true);
        $needsToRun = true;
        pcntl_signal(SIGTERM, function () use ($needsToRun) {
            $needsToRun = false;
        });

        $event = new UpdateEvent();
        while($needsToRun){
            try{        
                $this->dispatcher->dispatch($event, Constants::UPDATE_START);
            }catch(\Exception $e){
                $this->logger->error($e->getMessage());
            }
            sleep(5);
        }
        
        return static::SUCCESS;
    }
}
