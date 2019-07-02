<?php

namespace App\Repository;

use App\Entity\User;
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

    public function findOneByIdForCurrentUser(int $id, User $currentUser): ?RelativeAlias
    {
        $qb = $this->createQueryBuilder('ra');

        return $qb->andWhere($qb->expr()->eq('IDENTITY(ra.relativeUser)', $id))
            ->andWhere('ra.user = :currentUser')
            ->setParameter('currentUser', $currentUser)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
