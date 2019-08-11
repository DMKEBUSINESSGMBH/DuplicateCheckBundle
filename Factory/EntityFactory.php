<?php

declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Factory;

use DMK\DuplicateCheckBundle\Entity\Duplicate;
use DMK\DuplicateCheckBundle\Model\DuplicateInterface;
use Doctrine\ORM\EntityManagerInterface;
use Oro\Bundle\EntityBundle\Provider\EntityNameResolver;

class EntityFactory implements FactoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var EntityNameResolver
     */
    private $resolver;

    public function __construct(EntityNameResolver $resolver, EntityManagerInterface $manager)
    {
        $this->resolver = $resolver;
        $this->em = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function create($object, float $weight): DuplicateInterface
    {
        $metadata = $this->em->getClassMetadata(get_class($object));
        $ids = $metadata->getIdentifierValues($object);
        $instance = new Duplicate($object, current($ids), $weight);
        $instance->setName($this->resolver->getName($object));

        return $instance;
    }
}
