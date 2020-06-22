<?php

namespace App\Repository;

use App\Entity\Governance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Governance|null find($id, $lockMode = null, $lockVersion = null)
 * @method Governance|null findOneBy(array $criteria, array $orderBy = null)
 * @method Governance[]    findAll()
 * @method Governance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GovernanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Governance::class);
    }

    // /**
    //  * @return Governance[] Returns an array of Governance objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Governance
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
