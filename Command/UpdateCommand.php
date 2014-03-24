<?php

namespace PHPResqueBundle\Command;

use PHPResqueBundle\Resque\Status;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class UpdateCommand extends ContainerAwareCommand
{
    protected function configure() {
        $this->setName('resque:update')
             ->setDescription('Update a Job status')
             ->addArgument('job_id', InputArgument::REQUIRED, 'The Job ID')
             ->addArgument('new_status', InputArgument::REQUIRED, 'New Status')
             ->addOption('namespace', 'ns', InputOption::VALUE_OPTIONAL, 'Redis Namespace (prefix)')
             ->setHelp("Set a new status to a Job.");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        try {
            if (Status::update($input->getArgument('new_status'), $input->getArgument('job_id'), $input->getOption('namespace'))) {
                $output->write("Job updated!");
            } else {
                throw new \RuntimeException("Job could NOT updated.");
            }
        } catch (\RuntimeException $e) {
            $output->write("ERROR while update job: {$e->getMessage()}");
        }
    }
}