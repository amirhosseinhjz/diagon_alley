<?php

namespace App\Repository\Product;

use App\Entity\Brand\Brand;
use App\Entity\ItemValue;
use App\Entity\Product\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Variant;

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
        $qb = $this->createQueryBuilder('p');

        return $qb->select('b.name')
            ->innerJoin(Brand::class, 'b', Join::ON, 'p.brand = b.id')
            ->andWhere('p.category = :category')
            ->setParameter('category', $id)
            ->getQuery()
            ->getResult();
    }

//TODO separate filter file

    public function findProductsByCategoryId(int $categoryId, array $options): array
    {
        $qb = $this->createQueryBuilder('p');
        $qb = self::addBaseFilters($qb, $options);
        $qb = self::addBrandsFilter($qb, $options);
        $qb = self::addFeaturesFilter($qb, $options);
        return $qb->getQuery()->getResult();
    }

    public function findProductByBrandId(int $brandId, array $options): array
    {
        $qb = $this->createQueryBuilder('p');
        $qb = self::addBaseFilters($qb, $options);
        $qb = self::addCategoriesFilter($qb, $options);
        return $qb->getQuery()->getResult();
    }

    private function addBaseFilters($qb, array $options)
    {
        $qb->select('p.name, MIN(v.price) AS price')
            ->innerJoin(Variant::class, 'v', Join::ON, 'p.id = v.product_id')
            ->groupBy('p.id')
            ->andWhere('p.active = 1')//or true
            ->setFirstResult($options['offset'])
            ->setMaxResults($options['limit']);
        if ($options['availableOnly']) $qb->andWhere('v.quantity > 0');
        if (array_key_exists('minPrice', $options)) $qb->andWhere('v.price > :minPrice')->setParameter('minPrice', $options['minPrice']);
        if (array_key_exists('maxPrice', $options)) $qb->andWhere('v.price < :maxPrice')->setParameter('maxPrice', $options['maxPrice']);
        $qb = self::addSortFilter($qb, $options['sortedBy']);
        return $qb;
    }

    private function addSortFilter($qb, string $sort)
    {
        switch ($sort) {
            case 'view':
                $qb->orderBy('p.views', 'DESC');
                break;
            case 'sold':
                $qb->orderBy('v.sold', 'DESC');
                break;
            case 'price_low':
                $qb->orderBy('price', 'ASC');
                break;
            case 'price_high':
                $qb->orderBy('price', 'DESC');
                break;
            case 'newest':
                $qb->orderBy('p.created_at', 'DESC');
                break;
        }
        return $qb;
    }

    private function addCategoriesFilter($qb, array $options)
    {
        $qb->andWhere($qb->expr()->in('p.category_id', $options['categories']));
        return $qb;
    }

    private function addBrandsFilter($qb, array $options)
    {
        $qb->andWhere($qb->expr()->in('p.brand_id', $options['brands']));
        return $qb;
    }

    private function addFeaturesFilter($qb, array $options)
    {
        $qb->innerJoin(ItemValue::class, 'iv', Join::ON, 'p.id = iv.product_id');
        foreach ($options['features'] as $featureValue) {
                $qb->andWhere('iv.id = :featureValueId')->setParameter('featureValueId', $featureValue);
        }
        return $qb;
    }
}
