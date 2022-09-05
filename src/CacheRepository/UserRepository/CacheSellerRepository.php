<?php

namespace App\CacheRepository\UserRepository;

use App\Repository\UserRepository\SellerRepository;
use App\Interface\Cache\CacheInterface;
use App\Abstract\CacheRepository\BaseCacheRepository;

class CacheSellerRepository extends BaseCacheRepository
{
    public function __construct(SellerRepository $repository, CacheInterface $cache, int $exp)
    {
        parent::__construct($repository, $cache, $exp, 'seller', 'seller.');
    }
}