<?php

namespace PHPResqueBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;


class ResqueSchedulerCommand extends ContainerAwareCommand
{
    protected function configure() {
        $this->setName('resque:scheduler')
             ->setDescription('Queues scheduled resque jobs')
             ->addOption('log', 'l', InputOption::VALUE_OPTIONAL, 'Verbose level [0|1|2]')
             ->addOption('interval', 'i', InputOption::VALUE_OPTIONAL, 'Daemon check interval (in seconds)', 5)
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output) {
        $worker = new \ResqueScheduler_Worker();
        $worker->logLevel = $input->getOption('log');
        $worker->work($input->getOption('interval'));
    }
}
