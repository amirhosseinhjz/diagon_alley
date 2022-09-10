<?php

namespace App\Repository\PurchaseRepository;

use App\Entity\Order\PurchaseItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<\App\Entity\Order\PurchaseItem>
 *
 * @method PurchaseItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method PurchaseItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method PurchaseItem[]    findAll()
 * @method PurchaseItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchaseItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchaseItem::class);
    }

    public function add(PurchaseItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PurchaseItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PurchaseItem[] Returns an array of PurchaseItem objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PurchaseItem
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findBySellerIdAndPurchaseId(array $criteria): ?array
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();
        $query = $connection->prepare
        (
            "select pi.id as purchase_item_id, 
                v.type as type 
                from purchase_item pi 
                join variant v 
                on v.id = pi.variant_id 
                where pi.purchase_id=:purchaseId and v.seller_id=:sellerId;"
        );
        $query->bindValue('purchaseId',$criteria['purchaseId'],ParameterType::INTEGER);
        $query->bindValue('sellerId',$criteria['sellerId'],ParameterType::INTEGER);
        return $query->executeQuery()->fetchAllAssociative();
    }
}
