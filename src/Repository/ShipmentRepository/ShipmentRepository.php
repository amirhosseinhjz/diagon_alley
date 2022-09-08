<?php

namespace App\Repository\ShipmentRepository;

use App\Entity\Shipment\Shipment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Shipment>
 *
 * @method Shipment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shipment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shipment[]    findAll()
 * @method Shipment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShipmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Shipment::class);
    }

    public function add(Shipment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Shipment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findWithItems($id)
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.shipmentItems','si')
            ->addSelect('si')
            ->andWhere('s.id = :id')
            ->setParameter('id',$id)
            ->getQuery()
            ->getResult();
    }

    public function findWithSeller($seller)
    {
        return $this->createQueryBuilder('sh')
            ->innerJoin('sh.seller','s')
            ->addSelect('s')
            ->andWhere('s.id = :id')
            ->setParameter('id',$seller)
            ->getQuery()
            ->getResult();
    }
}
