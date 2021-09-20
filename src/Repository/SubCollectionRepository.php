<?php

namespace App\Repository;

use App\Entity\SubCollection;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SubCollection|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubCollection|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubCollection[]    findAll()
 * @method SubCollection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubCollectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubCollection::class);
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getByIdAndOwner(User $user, int $id){
        return $this->createQueryBuilder('s')
                ->innerJoin("s.collection", "col")
                ->andWhere("s.id = :sid")
                ->setParameter("sid", $id)
                ->andWhere("col.owner = :userid")
                ->setParameter("userid", $user->getId())
                ->getQuery()
                ->getOneOrNullResult();
    }
    // /**
    //  * @return SubCollection[] Returns an array of SubCollection objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SubCollection
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
