<?php
declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Adapter\DBAL;

class SoundExAdapter extends AbstractORMAdapter
{
    protected function getFunctionExpression(): string
    {
        return 'SOUNDEX';
    }

    protected function getWeight(object $item): float
    {
        return 0.2;
    }
}