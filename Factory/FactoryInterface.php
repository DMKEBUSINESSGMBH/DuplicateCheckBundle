<?php

declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Factory;

use DMK\DuplicateCheckBundle\Model\DuplicateInterface;

interface FactoryInterface
{
    /**
     * Creates an instance of the duplicate.
     *
     * @param object $object
     * @param float  $weight
     *
     * @return DuplicateInterface
     */
    public function create($object, float $weight): DuplicateInterface;
}
