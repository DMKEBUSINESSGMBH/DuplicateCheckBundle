<?php
declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Factory;

use DMK\DuplicateCheckBundle\Model\DuplicateInterface;

interface FactoryInterface
{
    public function create($object, $weight): DuplicateInterface;
}