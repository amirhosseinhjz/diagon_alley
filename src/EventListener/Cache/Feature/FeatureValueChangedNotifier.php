<?php

namespace App\EventListener\Cache\Feature;

use App\CacheEntityManager\CacheEntityManager;
use App\Entity\Feature\FeatureValue;
use App\Interface\Cache\CacheInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\Abstract\ChangeNotifier\EntityChangeNotifier;

class FeatureValueChangedNotifier extends EntityChangeNotifier
{
    public function __construct(CacheEntityManager $em, CacheInterface $cache)
    {
        $this->repository = $em->getRepository(FeatureValue::class);
        $this->cache = $cache;
    }

    public function postUpdate(
        FeatureValue $featureValue,
        LifecycleEventArgs $event,
    ): void
    {
        $this->repository->deleteFromCache($featureValue);
    }

    public function postPersist(LifecycleEventArgs $event)
    {
        $this->repository->deleteAllFromCache();
    }

}