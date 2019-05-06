<?php
declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Command;

use DMK\DuplicateCheckBundle\Async\Topics;
use DMK\DuplicateCheckBundle\Facade;
use Doctrine\ORM\EntityManagerInterface;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\EntityConfigBundle\Provider\ConfigProvider;
use Oro\Component\MessageQueue\Client\MessageProducerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SearchByClassCommand extends Command
{
    private $producer;

    /**
     * @var DoctrineHelper
     */
    private $helper;

    private $facade;


    public function __construct(
        MessageProducerInterface $producer,
        DoctrineHelper $helper,
        Facade $facade
    )
    {
        $this->helper = $helper;
        $this->facade = $facade;
        $this->producer = $producer;

        parent::__construct(null);
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

        if ($input->getOption('scheduled')) {
            $this->producer->send(Topics::TOPIC_CHECK_CLASS, [
               'entityClass' => $class,
            ]);

            return;
        }

        $em = $this->helper->getEntityManager($class);
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from($class, 'e');
        $result = $qb->getQuery()->iterate();

        foreach ($result as $row) {
            $this->facade->search($row);
        }
    }
}
