<?php
declare(strict_types=1);

namespace DMK\DuplicateCheckBundle;

use DMK\DuplicateCheckBundle\Model\DuplicateInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * This class is only a facade which search
 * for duplicates and the persist them.
 */
final class Facade
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var FinderInterface
     */
    private $finder;

    /**
     * Facade constructor.
     * 
     * @param EntityManagerInterface $manager
     * @param FinderInterface $finder
     */
    public function __construct(EntityManagerInterface $manager, FinderInterface $finder)
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
    public function search($object)
    {
        $duplicates = [];
        foreach ($this->finder->search($object) as $duplicate) {
            $duplicates[] = $duplicate;
            $this->manager->persist($duplicate);
        }

        $this->manager->flush();

        return $duplicates;
    }
}