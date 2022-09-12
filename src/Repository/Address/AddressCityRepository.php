<?php

namespace App\Repository\Address;

use App\Entity\Address\AddressCity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AddressCity>
 *
 * @method AddressCity|null find($id, $lockMode = null, $lockVersion = null)
 * @method AddressCity|null findOneBy(array $criteria, array $orderBy = null)
 * @method AddressCity[]    findAll()
 * @method AddressCity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AddressCityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AddressCity::class);
    }

    public function add(AddressCity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AddressCity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByName($value): ?AddressCity
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.name = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
