<?php

namespace App\Repository\FeatureRepository;

use App\Entity\Feature\DefineFeature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Criteria;

/**
 * @extends ServiceEntityRepository<DefineFeature>
 *
 * @method DefineFeature|null find($id, $lockMode = null, $lockVersion = null)
 * @method DefineFeature|null findOneBy(array $criteria, array $orderBy = null)
 * @method DefineFeature[]    findAll()
 * @method DefineFeature[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DefineFeatureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DefineFeature::class);
    }

    public function add(DefineFeature $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DefineFeature $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


//    /**
//     * @return DefineFeature[] Returns an array of DefineFeature objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DefineFeature
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function showFeature(array $filters_eq){
        $criteria = Criteria::create();
        $expr = array();
        foreach($filters_eq as $filter => $value){
            $expr[] = $criteria->expr()->eq($filter,$value);
        }
        $criteria->where(call_user_func_array(array( $criteria->expr(), 'andX' ),$expr));
        return ($this->matching($criteria)->toArray());
    }

    public function showOneFeature(array $filters_eq):DefineFeature{
        $criteria = Criteria::create();
        $expr = array();
        foreach($filters_eq as $filter => $value){
            $expr[] = $criteria->expr()->eq($filter,$value);
        }
        $criteria->where(call_user_func_array(array( $criteria->expr(), 'andX' ),$expr));
        return ($this->matching($criteria)->toArray())[0];
    }
}
