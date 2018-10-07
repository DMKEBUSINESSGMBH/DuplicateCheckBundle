<?php
declare(strict_types=1);

namespace DMK\DuplicateCheckBundle;

use DMK\DuplicateCheckBundle\Model\DuplicateInterface;
use Doctrine\ORM\EntityManager;

/**
 * This class is only a facade which search
 * for duplicates and the persist them.
 */
final class Facade
{
    private $manager;

    private $finder;

    public function __construct(EntityManager $manager, FinderInterface $finder)
    {
        $this->manager = $manager;
        $this->finder = $finder;
    }

    /**
     * This class search for the duplicates and saves them afterwards.
     * Please beware that this method can lead to memory issues, if you have a
     * large database!
     *
     * @param object $object
     *
     * @return DuplicateInterface[]
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function perform($object)
    {
        $duplicates = [];
        foreach ($this->finder->search($object) as $duplicate) {
            $duplicate[] = $duplicate;
            $this->manager->persist($duplicate);
        }

        $this->manager->flush($duplicate);

        return $duplicates;
    }
}