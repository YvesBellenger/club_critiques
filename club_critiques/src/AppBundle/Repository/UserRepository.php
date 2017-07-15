<?php

namespace AppBundle\Repository;

class UserRepository extends \Doctrine\ORM\EntityRepository
{
    public function getByFilters($limit = 8, $offset = 0) {
        $qb = $this->createQueryBuilder('c');
        $qb->where('1 = 1');
        $qb->andWhere('c.status = :status')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('c.username')
            ->setParameter('status', 1);

        return $qb->getQuery()
            ->getResult();
    }

    public function getUsersContentWanted($content)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->andWhere(':content MEMBER OF c.contentsWanted');
        $qb->setParameter('content',$content);
        return $qb->getQuery()->getResult();
    }

    public function getUsersContentToShare($content)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->andWhere(':content MEMBER OF c.contentsToShare');
        $qb->setParameter('content',$content);
        return $qb->getQuery()->getResult();
    }
}
