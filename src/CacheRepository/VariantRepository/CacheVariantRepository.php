<?php

namespace App\CacheRepository\VariantRepository;

use App\Repository\VariantRepository\VariantRepository;
use App\Interface\Cache\CacheInterface;
use App\Abstract\CacheRepository\BaseCacheRepository;
use App\Service\VariantService\VariantManagement;

class CacheVariantRepository extends BaseCacheRepository
{


    public function __construct(VariantRepository $repository, CacheInterface $cache)
    {
        parent::__construct($repository, $cache, 'variant', 60);
    }

    public static function getCacheKeys(): array
    {
        return [
            'id',
            'serial',
            'quantity',
            'valid'
        ];
    }

    public static function getNamePrefix(): string
    {
        return 'variant';
    }

//    public function showVariant($criteria, $cache=true , array $orderBy = null, $limit = null, $offset = null)
//    {
//        $key = 'my_key';
//        if ($cache)
//        {
//            return parent::getCache()->remember($key, $this->exp, function () use ($criteria, $orderBy, $limit, $offset) {
//                return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
//            });
//        } else {
//            $result = $this->repository->findBy($criteria, $orderBy, $limit, $offset);
//            parent::saveToCache($key, $result);
//            return $result;
//        }
//    }
}