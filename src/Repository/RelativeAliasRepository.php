<?php

namespace App\Repository;

use App\Entity\RelativeAlias;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method RelativeAlias|null find($id, $lockMode = null, $lockVersion = null)
 * @method RelativeAlias|null findOneBy(array $criteria, array $orderBy = null)
 * @method RelativeAlias[]    findAll()
 * @method RelativeAlias[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RelativeAliasRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RelativeAlias::class);
    }

    // /**
    //  * @return RelativeAlias[] Returns an array of RelativeAlias objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RelativeAlias
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
