<?php

namespace App\EventListener\Cache\User;


use App\Entity\User\Seller;
use App\Interface\Cache\CacheInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\CacheRepository\UserRepository\CacheSellerRepository;
use App\Abstract\ChangeNotifier\EntityChangeNotifier;
class SellerChangedNotifier extends EntityChangeNotifier
{
    public function postUpdate(
        Seller $seller,
        LifecycleEventArgs $event,
        CacheSellerRepository $repository,
        CacheInterface $cache
    ): void
    {
        $this->deleteFromCache($cache, $repository, $seller);
    }


    private function deleteFromCache(
        CacheInterface $cache,
        CacheSellerRepository $repository,
        Seller $seller
        )
    {
        $keys = $repository::getCacheKeys();
        foreach ($keys as $key)
        {
            $value = $seller->{'get'.ucfirst($key)}();
            $key = $repository->_getKey($key, $value);
            $cache->forget($key);
        }
        $cache->forget($repository::getKeyAll());
    }

}