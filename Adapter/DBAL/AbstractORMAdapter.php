<?php

declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Adapter\DBAL;

use DMK\DuplicateCheckBundle\Adapter\AdapterInterface;
use DMK\DuplicateCheckBundle\Factory\FactoryInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use DMK\DuplicateCheckBundle\Provider\ConfigProvider;

abstract class AbstractORMAdapter implements AdapterInterface
{
    /**
     * @var ManagerRegistry
     */
    protected $registry;

    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @var ConfigProvider
     */
    protected $config;

    /**
     * AbstractORMAdapter constructor.
     *
     * @param ManagerRegistry  $registry
     * @param ConfigProvider   $config
     * @param FactoryInterface $factory
     */
    public function __construct(ManagerRegistry $registry, ConfigProvider $config, FactoryInterface $factory)
    {
        $this->registry = $registry;
        $this->factory = $factory;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function process($object): iterable
    {
        /** @var ClassMetadata $metadata */
        $metadata = $this->registry->getManagerForClass(get_class($object))->getClassMetadata(get_class($object));
        $class = $metadata->getName();

        /** @var QueryBuilder $qb */
        $qb = $this->registry->getRepository($class)
            ->createQueryBuilder('e');
        $qb->andWhere($qb->expr()->neq('e.id', ':objectId'));
        $qb->andWhere($qb->expr()->not(
            $qb->expr()->exists('SELECT d FROM DMK\\DuplicateCheckBundle\\Entity\\Duplicate d WHERE d.class = :class AND d.objectId = :objectId')
        ));
        $qb->setParameter('class', get_class($object));
        $qb->setParameter('objectId', current($metadata->getIdentifierValues($object)));

        foreach ($this->config->getEnabledFields($class) as $name) {
            $this->walkQuery($qb, $name, $object);
        }

        $result = $qb->getQuery()->iterate();

        foreach ($result as $item) {
            $item = $item[0];
            yield $this->factory->create($item, $this->getWeight($item));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($object): bool
    {
        /** @var EntityManager $em */
        if (null === $em = $this->registry->getManagerForClass(get_class($object))) {
            return false;
        }

        if (!$this->config->isEntityEnabled(get_class($object))) {
            return false;
        }

        return 0 !== count($this->config->getEnabledFields(get_class($object)));
    }

    /**
     * Returns the weight for the item.
     *
     * @param object $item
     *
     * @return float
     */
    abstract protected function getWeight($item): float;

    /**
     * Returns the DQL Function expression.
     *
     * @return string
     */
    abstract protected function walkWhereExpression(QueryBuilder $qb);

    /**
     * This method will be called for each enabled field.
     *
     * @param QueryBuilder $qb
     * @param string       $fieldName
     * @param object       $entity
     */
    protected function walkQuery(QueryBuilder $qb, string $fieldName, $entity): void
    {
        $value = $this->registry->getManagerForClass(get_class($entity))
            ->getClassMetadata(get_class($entity))
            ->getFieldValue($entity, $fieldName);

        $this->walkWhereExpression($qb);
    }
}
