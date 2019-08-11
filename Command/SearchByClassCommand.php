<?php

declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Command;

use DMK\DuplicateCheckBundle\Async\Topics;
use DMK\DuplicateCheckBundle\Facade;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\EntityBundle\Provider\EntityNameProviderInterface;
use Oro\Bundle\EntityBundle\Provider\EntityNameResolver;
use Oro\Component\MessageQueue\Client\MessageProducerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SearchByClassCommand extends Command
{
    /**
     * @var MessageProducerInterface
     */
    private $producer;

    /**
     * @var DoctrineHelper
     */
    private $helper;

    /**
     * @var EntityNameResolver
     */
    private $nameProvider;

    /**
     * @var Facade
     */
    private $facade;

    /**
     * @param MessageProducerInterface $producer
     * @param EntityNameResolver       $nameProvider
     * @param DoctrineHelper           $helper
     * @param Facade                   $facade
     */
    public function __construct(
        MessageProducerInterface $producer,
        EntityNameResolver $nameProvider,
        DoctrineHelper $helper,
        Facade $facade
    ) {
        $this->helper = $helper;
        $this->nameProvider = $nameProvider;
        $this->facade = $facade;
        $this->producer = $producer;

        parent::__construct(null);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setName('dmk:duplicate-check:check-class');
        $this->addOption('scheduled', null, InputOption::VALUE_NONE);
        $this->addArgument('class', InputArgument::REQUIRED, 'The entity class to check');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $class = $input->getArgument('class');

        if ($input->getOption('scheduled')) {
            $this->producer->send(Topics::TOPIC_CHECK_CLASS, [
               'entityClass' => $class,
            ]);

            return null;
        }

        $em = $this->helper->getEntityManager($class);
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from($class, 'e');
        $result = $qb->getQuery()->iterate();

        $table = new Table($output);
        $table->setHeaders(['ID', 'Name', 'New Duplicates']);

        foreach ($result as $row) {
            $entity = $row[0];
            $duplicates = $this->facade->search($entity);

            $table->addRow([
                $entity->getId(),
                $this->nameProvider->getName($entity, EntityNameProviderInterface::SHORT, null),
                count($duplicates),
            ]);
        }

        $table->render();
    }
}
