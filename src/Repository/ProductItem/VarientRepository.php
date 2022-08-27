<?php

namespace App\Repository\ProductItem;

use App\Entity\ProductItem\Varient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Criteria;

/**
 * @extends ServiceEntityRepository<Varient>
 *
 * @method Varient|null find($id, $lockMode = null, $lockVersion = null)
 * @method Varient|null findOneBy(array $criteria, array $orderBy = null)
 * @method Varient[]    findAll()
 * @method Varient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VarientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Varient::class);
    }

    public function add(Varient $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Varient $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Varient[] Returns an array of Varient objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Varient
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function showVarient(array $filters_eq,array $filters_gt){
        $criteria = Criteria::create();
        $expr = array();
        foreach($filters_eq as $filter => $value){
            $expr[] = $criteria->expr()->eq($filter,$value);
        }
        foreach($filters_gt as $filter => $value){
            $expr[] = $criteria->expr()->gt($filter,$value);
        }
        $criteria->where(call_user_func_array(array( $criteria->expr(), 'andX' ),$expr));
        return $this->matching($criteria)->toArray();
    }
}
