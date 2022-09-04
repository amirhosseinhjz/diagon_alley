<?php

namespace App\Repository\Address;

use App\Entity\Address\AddressProvince;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AddressProvince>
 *
 * @method AddressProvince|null find($id, $lockMode = null, $lockVersion = null)
 * @method AddressProvince|null findOneBy(array $criteria, array $orderBy = null)
 * @method AddressProvince[]    findAll()
 * @method AddressProvince[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AddressProvinceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AddressProvince::class);
    }

    public function add(AddressProvince $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AddressProvince $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return AddressProvince[] Returns an array of AddressProvince objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AddressProvince
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
