<?php

declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Adapter\DBAL;

use Doctrine\ORM\QueryBuilder;

class SoundExAdapter extends AbstractORMAdapter
{
    /**
     * @param QueryBuilder $qb
     *
     * @return void
     */
    protected function walkWhereExpression(QueryBuilder $qb)
    {
        $qb->andWhere($qb->expr()->eq(
            sprintf('SOUNDEX(e.%s)', $fieldName),
            sprintf('SOUNDEX(:param_%s)', $fieldName)
        ));

        $qb->setParameter('param_'.$fieldName, $value);
    }

    /**
     * @param object $item
     *
     * @return float
     */
    protected function getWeight($item): float
    {
        return 0.2;
    }
}
