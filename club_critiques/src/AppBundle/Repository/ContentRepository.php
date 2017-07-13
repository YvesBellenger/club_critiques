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
    public function getByFilters($category, $sub_category, $author, $title, $publishedDate) {
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
        if ($publishedDate) {
            $qb->andWhere('c.publishedDate LIKE :publishedDate');
            $qb->setParameter('publishedDate', '%'.$publishedDate.'%');
        }
       $qb->andWhere('c.status = :status')
        ->orderBy('c.title')
        ->setParameter('status', 1);

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
}
