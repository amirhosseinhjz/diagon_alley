<?php

namespace App\Repository\ShipmentRepository;

use App\Entity\Shipment\ShipmentItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<\App\Entity\Shipment\ShipmentItem>
 *
 * @method ShipmentItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShipmentItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShipmentItem[]    findAll()
 * @method ShipmentItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShipmentItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShipmentItem::class);
    }

    public function add(ShipmentItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ShipmentItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

}
