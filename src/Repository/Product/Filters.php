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
        $qb->select('p.name, MIN(v.price) AS price')
            ->innerJoin(Variant::class, 'v', Join::WITH, 'p.id = v.product_id')
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

    static function addSortFilter(QueryBuilder $qb, string $sort): QueryBuilder
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

    static function addCategoriesFilter(QueryBuilder $qb, array $options): QueryBuilder
    {
        $qb->andWhere($qb->expr()->in('p.category_id', $options['categories']));
        return $qb;
    }

    static function addBrandsFilter(QueryBuilder $qb, array $options): QueryBuilder
    {
        $qb->andWhere($qb->expr()->in('p.brand_id', $options['brands']));
        return $qb;
    }

    static function addFeaturesFilter(QueryBuilder $qb, array $options): QueryBuilder
    {
        $qb->innerJoin(FeatureValue::class, 'iv', Join::WITH, 'p.id = iv.product_id');
        foreach ($options['features'] as $featureValue) {
                $qb->andWhere('iv.id = :featureValueId')->setParameter('featureValueId', $featureValue);
        }
        return $qb;
    }
}