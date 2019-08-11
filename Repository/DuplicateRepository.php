<?php

declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Repository;

use DMK\DuplicateCheckBundle\Model\DuplicateInterface;
use Doctrine\ORM\EntityRepository;

class DuplicateRepository extends EntityRepository
{
    /**
     * Gets stored duplicates fo the given object.
     *
     * @param object $object
     *
     * @return DuplicateInterface[]
     */
    public function getDuplicates($object): iterable
    {
        $id = $this->_em->getClassMetadata(get_class($object))
            ->getIdentifierValues($object);
        $id = current($id);

        $qb = $this->createQueryBuilder('d');
        $qb->where($qb->expr()->andX(
            $qb->expr()->eq('d.class', ':class'),
            $qb->expr()->eq('d.objectId', ':id')
        ));
        $qb->setParameters([
            'class' => get_class($object),
            'id' => $id,
        ]);

        foreach ($qb->getQuery()->iterate() as $row) {
            yield $row[0];
        }
    }

    /**
     * Returns the number of duplicates which exists for the given object.
     *
     * @param object $object The object to check
     *
     * @return int
     */
    public function getDuplicatesCnt($object): int
    {
        $id = $this->_em->getClassMetadata(get_class($object))
            ->getIdentifierValues($object);
        $id = current($id);

        $conn = $this->_em->getConnection();
        $stmt = $conn->prepare('SELECT COUNT(*) FROM dmk_duplicate WHERE class = ? AND object_id = ?');
        $stmt->execute([
            get_class($object),
            $id,
        ]);

        return $stmt->fetchColumn();
    }
}
