<?php

namespace App\Repository;

use App\Entity\News;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method News|null find($id, $lockMode = null, $lockVersion = null)
 * @method News|null findOneBy(array $criteria, array $orderBy = null)
 * @method News[]    findAll()
 * @method News[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    public function getByCategory($category = NULL, $limit = NULL)
    {
        $news = $this->createQueryBuilder('n');
        if($category > 0){
            $news->innerJoin('n.categories', 'c', 'WITH', 'c.id = :category')->setParameter('category', $category);
        }else{
            $news->innerJoin('n.categories', 'c');
        }
      
        return $news->setMaxResults($limit)
        ->getQuery()->getResult();
    }

    public function getByCategoryQuery(?string $category)
    {
        $news = $this->createQueryBuilder('n');
        if($category > 0){
            $news->innerJoin('n.categories', 'c', 'WITH', 'c.id = :category')->setParameter('category', $category);
        }else{
            $news->innerJoin('n.categories', 'c');
        }
        return $news;
    }

    public function groupByCategory()
    {
        return $this->createQueryBuilder('n')
            ->innerJoin('n.categories', 'c')
            ->groupBy('n, c.title')
            ->getQuery()->getResult();
    }

    public function getMostPopular()
    {
        return $this->createQueryBuilder('n')
            ->select('n')
            ->orderBy('n.visitors', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->execute();
    }
}

