<?php

declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Model;

interface DuplicateInterface
{
    /**
     * Returns the weighting of this duplicate.
     * The range is from 0.0 to 1.0, where 1.0 means it
     * is to an exact match.
     *
     * @return float
     */
    public function getWeight(): float;

    /**
     * Returns the object which is a duplicate.
     *
     * @return object
     */
    public function getObject();

    /**
     * Returns the class of the duplicate.
     * This is only a shortcut.
     *
     * @return string
     */
    public function getClass(): string;
}
