<?php

namespace App\Repository\ProductItem;

use App\Entity\ProductItem\ItemFeature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Criteria;

/**
 * @extends ServiceEntityRepository<ItemFeature>
 *
 * @method ItemFeature|null find($id, $lockMode = null, $lockVersion = null)
 * @method ItemFeature|null findOneBy(array $criteria, array $orderBy = null)
 * @method ItemFeature[]    findAll()
 * @method ItemFeature[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemFeatureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ItemFeature::class);
    }

    public function add(ItemFeature $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ItemFeature $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ItemFeature[] Returns an array of ItemFeature objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ItemFeature
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function readFeature($id){
        $feature = $this->findBy(['id' => $id]);
        if(count($feature) == 0){
            return null;
        }
        return $feature[0];
    }

    public function showFeature(array $filters_eq){
        $criteria = Criteria::create();
        $expr = array();
        foreach($filters_eq as $filter => $value){
            $expr[] = $criteria->expr()->eq($filter,$value);
        }
        $criteria->where(call_user_func_array(array( $criteria->expr(), 'andX' ),$expr));
        return $this->matching($criteria)->toArray();
    }
}
