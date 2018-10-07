<?php
declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Factory;


use DMK\DuplicateCheckBundle\Entity\Duplicate;
use DMK\DuplicateCheckBundle\Model\DuplicateInterface;
use Doctrine\ORM\EntityManager;

class EntityFactory implements FactoryInterface
{
    private $em;

    public function __construct(EntityManager $manager)
    {
        $this->em = $manager;
    }

    public function create($object, $weight): DuplicateInterface
    {
        return Duplicate::create($this->em, $object, $weight);
    }
}