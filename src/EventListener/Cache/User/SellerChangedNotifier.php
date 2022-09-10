<?php

namespace App\EventListener\Cache\User;


use App\CacheEntityManager\CacheEntityManager;
use App\Entity\User\Seller;
use App\Interface\Cache\CacheInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\Abstract\ChangeNotifier\EntityChangeNotifier;

class SellerChangedNotifier extends EntityChangeNotifier
{
    public function __construct(CacheEntityManager $em, CacheInterface $cache)
    {
        $this->repository = $em->getRepository(Seller::class);
        $this->cache = $cache;
    }

    public function postUpdate(
        Seller $seller,
        LifecycleEventArgs $event,
    ): void
    {
        $this->repository->deleteFromCache($seller);
    }

    public function postPersist(LifecycleEventArgs $event)
    {
        $this->repository->deleteAllFromCache();
    }

}