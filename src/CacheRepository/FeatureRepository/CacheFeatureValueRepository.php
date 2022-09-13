<?php

namespace App\CacheRepository\FeatureRepository;

use App\Repository\FeatureRepository\FeatureValueRepository;
use App\Interface\Cache\CacheInterface;
use App\Abstract\CacheRepository\BaseCacheRepository;

class CacheFeatureValueRepository extends BaseCacheRepository
{


    public function __construct(FeatureValueRepository $repository, CacheInterface $cache)
    {
        parent::__construct($repository, $cache, 'featureValue', 36000);
    }

    public static function getCacheKeys(): array
    {
        return [
            'id',
            'active'
        ];
    }

    public static function getNamePrefix(): string
    {
        return 'featureValue';
    }
}