<?php

declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Model;

use DMK\DuplicateCheckBundle\Entity\BaseDuplicate;

class ExtendDuplicate extends BaseDuplicate
{
    public function __construct($object, int $objectId, float $weight = 0.5)
    {
        parent::__construct($object, $objectId, $weight);
    }
}
