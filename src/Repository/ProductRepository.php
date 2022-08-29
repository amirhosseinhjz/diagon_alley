<?php

namespace App\Repository;

use App\Entity\Brand;
use App\Entity\Product;
use App\Entity\Variant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function add(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByName(string $name): ?Product
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.name = :val')
            ->setParameter('val', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findWithFilters(array $categories, array $brands, int $offset, int $limit, int $minPrice, int $maxPrice, bool $availableOnly)
    {
        //categories and brands are arrays of ids
        $qb = $this->createQueryBuilder('p');

        $qb->select('p.name, MIN(v.price) AS price')
            ->innerJoin(Variant::class, 'v', Join::ON, 'p.id = v.product_id')
            ->groupBy('p.id')
            ->andWhere('p.active = 1')//or true
            ->andWhere($qb->expr()->in('p.category_id', $categories))
            ->andWhere($qb->expr()->in('p.brand_id', $brands))
            ->andWhere($qb->expr()->between('price', $minPrice, $maxPrice))
            ->setFirstResult($offset)
            ->setMaxResults($limit);
        //feature value filters

        if ($availableOnly) $qb->andWhere('v.quantity > 0');

        return $qb->getQuery()->getResult();
    }

    public function findBrandsByCategoryName(string $categoryName): array
    {
        $qb = $this->createQueryBuilder('p');

        return $qb->select('b.name')
            ->innerJoin(Brand::class, 'b', Join::ON, 'p.brand = b.id')
            ->andWhere('p.category = :category')
            ->setParameter('category', $categoryName)
            ->getQuery()
            ->getResult();
    }
}
