<?php
declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Adapter\DBAL;


use DMK\DuplicateCheckBundle\Adapter\AdapterInterface;
use DMK\DuplicateCheckBundle\Factory\FactoryInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Oro\Bundle\EntityConfigBundle\Config\ConfigInterface;
use Oro\Bundle\EntityConfigBundle\Provider\ConfigProvider;

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
     * @param ManagerRegistry $registry
     * @param ConfigProvider $config
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
        $class = ClassUtils::getClass($object);
        $configs = $this->config->getConfigs($class);
        /** @var ClassMetadata $metadata */
        $metadata = $this->registry->getManagerForClass($class)->getClassMetadata($class);
        /** @var QueryBuilder $qb */
        $qb = $this->registry->getRepository($class)
            ->createQueryBuilder('e');
        $qb->andWhere($qb->expr()->not(
            $qb->expr()->exists('SELECT d FROM DMK\\DuplicateCheckBundle\\Entity\\Duplicate d WHERE d.class = :class AND d.objectId = :objectId')
        ));
        $qb->setParameter('class', get_class($object));
        $qb->setParameter('id', $metadata->getIdentifierValues($object));

        foreach ($configs as $config) {
            $this->walkQuery($qb, $config, $object);
        }

        $result = $qb->getQuery()->iterate();

        foreach ($result as $item) {
            yield $this->factory->create($item, $this->getWeight($item));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($object): bool
    {
        /** @var EntityManager $em */
        if (null === $em = $this->registry->getManagerForClass(ClassUtils::getClass($object))) {
            return false;
        }

        if (null === $em->getConfiguration()->getCustomStringFunction($this->getFunctionExpression())) {
            return false;
        }

        $config = $this->config->getConfig(ClassUtils::getClass($object), 'duplicate');

        return $config['enabled'] ?? false;
    }

    /**
     * Returns the weight for the item.
     *
     * @param object $item
     *
     * @return float
     */
    abstract protected function getWeight(object $item): float;

    /**
     * Returns the DQL Function expression.
     *
     * @return string
     */
    abstract protected function getFunctionExpression(): string;

    /**
     * This method will be called for each enabled field.
     *
     * @param QueryBuilder $qb
     * @param ConfigInterface $config
     * @param object $entity
     */
    protected function walkQuery(QueryBuilder $qb, ConfigInterface $config, object $entity): void
    {
        $fieldName = $config->getId()->getFieldName();
        $value = $this->registry->getManagerForClass(get_class($config))
            ->getClassMetadata(get_class($entity))
            ->getFieldValue($entity, $fieldName);

        $qb->andWhere($qb->expr()->eq(
            sprintf('%s(e.%s))', $this->getFunctionExpression(), $fieldName),
            ':param_' . $fieldName
        ));

        $qb->setParameter('param_' . $fieldName, $value);
    }
}