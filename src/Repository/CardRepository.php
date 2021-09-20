<?php

namespace App\Repository;

use ApiPlatform\Core\Bridge\Doctrine\Orm\AbstractPaginator;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use App\Entity\Card;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Card|null find($id, $lockMode = null, $lockVersion = null)
 * @method Card|null findOneBy(array $criteria, array $orderBy = null)
 * @method Card[]    findAll()
 * @method Card[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Card::class);
    }

    public function getAllBySub(int $id){
        $qb = $this->createQueryBuilder('c');
        $qb = $qb->innerJoin("c.subCollection", "subc")
                ->where("subc.id = :idsub")
                ->setParameter("idsub", $id);

        $items_count = count($qb->getQuery()->getResult());

        $criteria = Criteria::create()
            ->setFirstResult(0)
            ->setMaxResults($items_count);
        $qb->addCriteria($criteria);

        $doctrinePaginator = new DoctrinePaginator($qb);
        return new Paginator($doctrinePaginator);
    }

    /**
     * @param $user
     * @param $id
     * @param $idcard
     * @return Card
     * @throws NonUniqueResultException
     */
    public function getByIdSubAndOwner($user, $id, $idcard): Card
    {
        return $this->createQueryBuilder('c')
                ->innerJoin('c.subCollection', 'scol')
                ->innerJoin('scol.collection', 'col')
                ->andWhere('c.id = :cid')
                ->setParameter('cid', $idcard)
                ->andWhere('scol.id = :sid')
                ->setParameter('sid', $id)
                ->andWhere('col.owner = :user')
                ->setParameter('user', $user->getId())
                ->getQuery()
                ->getOneOrNullResult();
    }
    // /**
    //  * @return Collection[] Returns an array of Collection objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Collection
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
