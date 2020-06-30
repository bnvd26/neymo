<?php

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    /**
     * @return Transaction[] Returns an array of Transaction objects
     */
    public function findAllTransactions($value)
    {
        return $this->createQueryBuilder('t')
            ->where('t.beneficiary = :val')
            ->orWhere('t.emiter = :val')
            ->setParameter('val', $value)
            ->orderBy('t.date', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findTransactionsByDate($date, $value)
    {
        return $this->createQueryBuilder('t')
            ->where('t.beneficiary = :value')
            ->orWhere('t.emiter = :value')
            ->andWhere('t.date = :date')
            ->setParameter('value', $value)
            ->setParameter('date', $date)
            ->orderBy('t.date', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Transaction
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
