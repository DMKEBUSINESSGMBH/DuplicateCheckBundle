<?php

declare(strict_types=1);

namespace DMK\DuplicateCheckBundle;

use DMK\DuplicateCheckBundle\Model\DuplicateInterface;

/**
 * This class can be used to find
 * duplicates for your entities.
 */
interface FinderInterface
{
    /**
     * This method runs the duplicate check against all
     * matching adapters.
     *
     * @param object $object
     *
     * @return DuplicateInterface[]
     */
    public function search($object): iterable;
}
