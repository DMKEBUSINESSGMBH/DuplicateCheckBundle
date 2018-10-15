<?php
declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Factory;


use DMK\DuplicateCheckBundle\Entity\Duplicate;
use DMK\DuplicateCheckBundle\Model\DuplicateInterface;
use Doctrine\ORM\EntityManagerInterface;

class EntityFactory implements FactoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->em = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function create($object, float $weight): DuplicateInterface
    {
        return Duplicate::create($this->em, $object, $weight);
    }
}