<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    //TODO: add indexes
    //TODO: show all products in a category with filters
    //TODO: get main cats

    public function add(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Category[]
     */
    public function findMainCategories(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.parent IS NULL')
            ->getQuery()
            ->getResult();
    }

    public function findOneByName(string $name): ?Category
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.name = :val')
            ->setParameter('val', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Category[]
     */
    public function findManyByQuery(string $q): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.name LIKE :pattern')
            ->setParameter('pattern', '%' . $q . '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Category[]
     */
    public function findUnused(): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.children IS EMPTY')
            ->andWhere('b.products IS EMPTY')
            ->getQuery()
            ->getResult();
    }
}
