<?php

namespace DMK\DuplicateCheckBundle\Exception;

use Throwable;

class ClassMetadataNotFoundException extends \RuntimeException
{
    public function __construct(string $class)
    {
        parent::__construct(sprintf(
            'Could not load class metadata for class "%s".',
            $class
        ));
    }
}
