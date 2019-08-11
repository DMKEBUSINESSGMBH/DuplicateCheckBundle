<?php

declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Async;

final class Topics
{
    const TOPIC_CHECK_SINGLE = 'dmk_duplicatecheck.check_single';
    const TOPIC_CHECK_CLASS = 'dmk_duplicatecheck.check_class';
    const TOPIC_CHECK_RANGE = 'dmk_duplicatecheck.check_range';

    private function __construct()
    {
    }
}
