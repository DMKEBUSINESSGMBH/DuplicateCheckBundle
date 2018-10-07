<?php
declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Command;

use Oro\Component\MessageQueue\Client\MessageProducerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SearchByClassCommand extends Command
{
    private $producer;

    public function __construct(MessageProducerInterface $producer)
    {
        $this->producer = $producer;

        parent::__construct('');
    }

    protected function configure()
    {
        $this->setName('dmk:duplicate-check:check-class');
        $this->addOption('scheduled', null, InputOption::VALUE_NONE);
        $this->addArgument('class', InputArgument::REQUIRED, 'The entity class to check');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $class = $input->getArgument('class');

        // To be implemented
    }
}