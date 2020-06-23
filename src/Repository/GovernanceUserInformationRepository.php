<?php

namespace App\Repository;

use App\Entity\GovernanceUserInformation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GovernanceUserInformation|null find($id, $lockMode = null, $lockVersion = null)
 * @method GovernanceUserInformation|null findOneBy(array $criteria, array $orderBy = null)
 * @method GovernanceUserInformation[]    findAll()
 * @method GovernanceUserInformation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GovernanceUserInformationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GovernanceUserInformation::class);
    }


    public function findUserInformationByGovernanceId($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.user = :val')
            ->setParameter('val', $value)
            ->setMaxResults(100)
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?GovernanceUserInformation
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
