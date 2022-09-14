<?php

namespace App\Repository\VariantRepository;

use App\Entity\Variant\Variant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Variant>
 *
 * @method Variant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Variant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Variant[]    findAll()
 * @method Variant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VariantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Variant::class);
    }

    public function add(Variant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Variant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function showVariant($criteria){
        return $this->matching($criteria)->toArray();
    }

    /**
     * @return Variant[] Returns an array of Variant objects
     */
    public function findVariantsByProduct($value): array
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();
        $query = $connection->prepare
        (
            "select v.id,v.price,v.quantity,v.seller_id,v.serial,v.description
                from variant v
                where v.product_id =:value and v.quantity > 0 and v.valid = 1
                order by v.quantity DESC"
        );
        $query->bindValue('value',$value,ParameterType::INTEGER);
        return $query->executeQuery()->fetchAllAssociative();
    }

    public function findInValidVariantsBySeller($value): array
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();
        $query = $connection->prepare
        (
            "select v.id,v.price,v.quantity,v.seller_id,v.serial,v.description
                from variant v
                where v.seller_id =:value and v.quantity > 0 and v.valid = 0
                order by v.quantity DESC"
        );
        $query->bindValue('value',$value,ParameterType::INTEGER);
        return $query->executeQuery()->fetchAllAssociative();
    }

    public function findVariantsByValidation($value): array
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();
        $query = $connection->prepare
        (
            "select v.id,v.price,v.quantity,v.seller_id,v.serial,v.description
                from variant v
                where v.valid =:value and v.quantity > 0
                order by v.quantity DESC"
        );
        $query->bindValue('value',$value,ParameterType::INTEGER);
        return $query->executeQuery()->fetchAllAssociative();
    }
}
