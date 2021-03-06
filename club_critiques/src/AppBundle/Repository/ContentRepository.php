<?php

namespace AppBundle\Repository;

/**
 * BookRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ContentRepository extends \Doctrine\ORM\EntityRepository
{
    public function getByFilters($category, $sub_category, $author, $title, $orderBy, $limit = 12, $offset = 0) {
        $qb = $this->createQueryBuilder('c');
        $qb->join('AppBundle:Category', 'cat', 'WITH', 'c.category = cat');
        $qb->where('1 = 1');
        if ($sub_category) {
            $qb->andWhere('c.category = :sub_category');
            $qb->setParameter('sub_category', $sub_category);
        }
        if ($category) {
            $qb->andWhere('cat.parentCategory = :category');
            $qb->setParameter('category', $category);
        }
        if ($author) {
            $qb->andWhere(':author MEMBER OF c.authors');
            $qb->setParameter('author', $author);
        }
        if ($title) {
            $qb->andWhere('c.title LIKE :title');
            $qb->setParameter('title', '%'.$title.'%');
        }
       $qb->andWhere('c.status = :status')
        ->setMaxResults($limit)
        ->setFirstResult($offset);
        if($orderBy == 0)
        {
            $qb->orderBy('c.title','ASC');
        }
        else if($orderBy == 1)
        {
            $qb->orderBy('c.title', 'DESC');
        }
        else if($orderBy == 2)
        {
            $qb->orderBy('c.publishedDate', 'DESC');
        }
        else if($orderBy == 3)
        {
            $qb->orderBy('c.publishedDate', 'ASC');
        }
        $qb->setParameter('status', 1);

        return $qb->getQuery()
            ->getResult();
    }

    public function getSuggestions($content)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->where('c.category = :category')
            ->andWhere('c.status = :status')
            ->andWhere('c.id != :content_id')
            ->setParameter('category', $content->category)
            ->setParameter('content_id', $content->id)
            ->setParameter('status', 1);

        return $qb->getQuery()
            ->getResult();
    }

    /*public function getUsersContentWanted($content,$user)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->join('AppBundle:User', 'usr', 'WITH', 'c.usr = usr');
        $qb->where('c.content = :content')
            ->andWhere('c.status = :status')
            ->andWhere('c.id != :content_id')

        return $qb->getQuery()
            ->getResult();
    }*/
}
