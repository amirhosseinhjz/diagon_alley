<?php

namespace App\EventListener\Cache\Feature;

use App\CacheEntityManager\CacheEntityManager;
use App\Entity\Feature\Feature;
use App\Interface\Cache\CacheInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\Abstract\ChangeNotifier\EntityChangeNotifier;

class FeatureChangedNotifier extends EntityChangeNotifier
{
    public function __construct(CacheEntityManager $em, CacheInterface $cache)
    {
        $this->repository = $em->getRepository(Feature::class);
        $this->cache = $cache;
    }

    public function postUpdate(
        Feature $feature,
        LifecycleEventArgs $event,
    ): void
    {
        $this->repository->deleteFromCache($feature);
    }

    public function postPersist(LifecycleEventArgs $event)
    {
        $this->repository->deleteAllFromCache();
    }

//    public function onFlush(){
//        $this->repository->deleteAllFromCache();
//    }
}