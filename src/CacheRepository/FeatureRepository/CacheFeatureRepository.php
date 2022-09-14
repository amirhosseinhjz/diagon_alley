<?php

namespace App\CacheRepository\FeatureRepository;

use App\Repository\FeatureRepository\FeatureRepository;
use App\Interface\Cache\CacheInterface;
use App\Abstract\CacheRepository\BaseCacheRepository;

class CacheFeatureRepository extends BaseCacheRepository
{


    public function __construct(FeatureRepository $repository, CacheInterface $cache)
    {
        parent::__construct($repository, $cache, 'feature', 36000);
    }

    public static function getCacheKeys(): array
    {
        return [
            'id',
            'active',
            'label'
        ];
    }

    public static function getNamePrefix(): string
    {
        return 'feature';
    }
}