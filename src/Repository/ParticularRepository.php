<?php

namespace App\Repository;

use App\Entity\Particular;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Particular|null find($id, $lockMode = null, $lockVersion = null)
 * @method Particular|null findOneBy(array $criteria, array $orderBy = null)
 * @method Particular[]    findAll()
 * @method Particular[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticularRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Particular::class);
    }

    // /**
    //  * @return Particular[] Returns an array of Particular objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Particular
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
