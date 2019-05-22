<?php
declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Adapter\DBAL;

use Doctrine\ORM\QueryBuilder;

class SoundExAdapter extends AbstractORMAdapter
{
    protected function walkWhereExpression(QueryBuilder $qb)
    {
        $qb->andWhere($qb->expr()->eq(
            sprintf('SOUNDEX(e.%s)', $fieldName),
            sprintf('SOUNDEX(:param_%s)', $fieldName)
        ));

        $qb->setParameter('param_' . $fieldName, $value);
    }

    protected function getWeight($item): float
    {
        return 0.2;
    }
}
