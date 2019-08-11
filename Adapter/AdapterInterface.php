<?php

declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Adapter;

use DMK\DuplicateCheckBundle\Model\DuplicateInterface;

interface AdapterInterface
{
    /**
     * This method perform the duplicate check against the database or whatever.
     * You must return a Duplicate model.
     *
     * @param object $object
     *
     * @return DuplicateInterface[]
     */
    public function process($object): iterable;

    /**
     * This method returns true if the adapter is responsible for
     * this object, false otherwise. The process method will only be called
     * if this method returns true.
     *
     * @param object $object
     *
     * @return bool
     */
    public function supports($object): bool;
}
