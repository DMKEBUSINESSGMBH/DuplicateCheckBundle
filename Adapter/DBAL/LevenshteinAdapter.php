<?php

declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Adapter\DBAL;

use Doctrine\ORM\QueryBuilder;

class LevenshteinAdapter extends AbstractORMAdapter
{
    /**
     * {@inheritdoc}
     */
    public function supports($object): bool
    {
        if (false === parent::supports($object)) {
            return false;
        }

        static $check;

        if (null === $check) {
            try {
                $this->registry->getConnection()->exec('SELECT levenshtein("test", "test");');
                $check = true;
            } catch (\PDOException $e) {
                $check = false;
            }
        }

        return $check;
    }

    /**
     * {@inheritdoc}
     */
    protected function walkWhereExpression(QueryBuilder $qb, string $fieldName, $value): void
    {
        $qb->andWhere($qb->expr()->eq(
            sprintf('levenshtein(e.%s, :param_%s)', $fieldName, $fieldName),
            4
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function getWeight($item): float
    {
        return 0.4;
    }
}
