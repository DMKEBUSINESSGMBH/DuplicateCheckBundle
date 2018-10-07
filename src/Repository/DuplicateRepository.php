<?php
declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Repository;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityRepository;

class DuplicateRepository extends EntityRepository
{
    public function getDuplicates($object): iterable
    {
        $id = $this->_em->getClassMetadata(ClassUtils::getClass($object))
            ->getIdentifierValues($object);

        $qb = $this->createQueryBuilder('d');
        $qb->where($qb->expr()->andX(
            $qb->expr()->eq('d.class', ':class'),
            $qb->expr()->eq('d.objectId', ':id')
        ));
        $qb->setParameters([
            'class' => ClassUtils::getClass($object),
            'id' => $id
        ]);

        return $qb->getQuery()->iterate();
    }
}