<?php

namespace App\Repository\Product;

use App\Entity\Feature\FeatureValue;
use App\Entity\Variant\Variant;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

class Filters
{
    static function addBaseFilters(QueryBuilder $qb, array $options): QueryBuilder
    {
        $qb->select('product.name, MIN(variant.price) AS price, product.viewCount, SUM(variant.soldNumber) as sold')
            ->innerJoin(Variant::class, 'variant', Join::WITH, 'product.id = variant.product')
            ->groupBy('product.id')
            ->andWhere('product.active = 1');
        if (array_key_exists('offset', $options) == false) $qb->setFirstResult(0);
        if (array_key_exists('limit', $options) == false) $qb->setMaxResults(10);
        if (array_key_exists('availableOnly', $options) == false) $options['availableOnly'] = false;
        if ($options['availableOnly']) $qb->andWhere('variant.quantity > 0');
        if (array_key_exists('minPrice', $options)) $qb->andWhere('variant.price > :minPrice')->setParameter('minPrice', $options['minPrice']);
        if (array_key_exists('maxPrice', $options)) $qb->andWhere('variant.price < :maxPrice')->setParameter('maxPrice', $options['maxPrice']);
        if (array_key_exists('sortedBy', $options)) $qb = self::addSortFilter($qb, $options['sortedBy']);
        return $qb;
    }

    static function addSortFilter(QueryBuilder $qb, string $sort): QueryBuilder
    {
        switch ($sort) {
            case 'view':
                $qb->orderBy('product.viewCount', 'DESC');
                break;
            case 'sold':
                $qb->orderBy('sold', 'DESC');
                break;
            case 'price_low':
                $qb->orderBy('price', 'ASC');
                break;
            case 'price_high':
                $qb->orderBy('price', 'DESC');
                break;
            case 'newest':
                $qb->orderBy('product.createdAt', 'DESC');
                break;
        }
        return $qb;
    }

    static function addBrandIdFilter(QueryBuilder $qb, int $id): QueryBuilder
    {
        return $qb->andWhere('product.brand = :brandId')->setParameter('brandId', $id);
    }

    static function addCategoryIdFilter(QueryBuilder $qb, int $id): QueryBuilder
    {
        return $qb->andWhere('product.category = :categoryId')->setParameter('categoryId', $id);
    }

    static function addCategoriesFilter(QueryBuilder $qb, array $options): QueryBuilder
    {
        if (array_key_exists('categories', $options)) $qb->andWhere($qb->expr()->in('product.category', $options['categories']));
        return $qb;
    }

    static function addBrandsFilter(QueryBuilder $qb, array $options): QueryBuilder
    {
        if (array_key_exists('brands', $options)) $qb->andWhere($qb->expr()->in('product.brand', $options['brands']));
        return $qb;
    }

    static function addFeaturesFilter(QueryBuilder $qb, array $options): QueryBuilder
    {
        if (array_key_exists('features', $options) == false) return $qb;
        $qb->innerJoin('product.featureValues', 'fv', Join::WITH);
        $qb->andWhere($qb->expr()->in('fv.id', $options['features']));
        $qb->having('COUNT(DISTINCT fv.id) = :featuresCount')->setParameter('featuresCount', count($options['features']));
        return $qb;
    }
}