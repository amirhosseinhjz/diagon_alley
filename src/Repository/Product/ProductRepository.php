<?php

namespace App\Repository\Product;

use App\Entity\Brand\Brand;
use App\Entity\Product\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
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

    public function findOneById(int $id): ?Product
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findBrandsByCategoryId(int $id): array
    {
        $qb = $this->createQueryBuilder('product');

        return $qb->select('brand.name')
            ->innerJoin(Brand::class, 'brand', Join::WITH, 'product.brand = brand.id')
            ->andWhere('product.category = :category')
            ->setParameter('category', $id)
            ->getQuery()
            ->getResult();
    }

    public function findProductsByCategoryId(int $id, array $options): array
    {
        $qb = $this->createQueryBuilder('product');
        $qb = Filters::addBaseFilters($qb, $options);
        $qb = Filters::addBrandsFilter($qb, $options);
        $qb = Filters::addFeaturesFilter($qb, $options);
        return $qb->getQuery()->getResult();
    }

    public function findProductsByBrandId(int $id, array $options): array
    {
        $qb = $this->createQueryBuilder('product');
        $qb = Filters::addBaseFilters($qb, $options);
//        $qb = Filters::addCategoriesFilter($qb, $options);
        //TOdo add brand id filter
        return $qb->getQuery()->getResult();
    }
}
