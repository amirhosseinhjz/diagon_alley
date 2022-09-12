<?php

namespace App\Repository\Portal;

use App\Entity\Portal\Portal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Portal>
 *
 * @method Portal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Portal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Portal[]    findAll()
 * @method Portal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PortalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Portal::class);
    }

    public function add(Portal $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Portal $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
