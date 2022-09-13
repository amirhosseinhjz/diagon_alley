<?php

namespace App\EventListener\Cache\Feature;

use App\CacheEntityManager\CacheEntityManager;
use App\Entity\Feature\Feature;
use App\Interface\Cache\CacheInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\Abstract\ChangeNotifier\EntityChangeNotifier;
use App\CacheRepository\FeatureRepository\CacheFeatureRepository;

class FeatureChangedNotifier extends EntityChangeNotifier
{
    private $repository;

    public function __construct(CacheEntityManager $em)
    {
        $this->repository = $em->getRepository(Feature::class);
    }

    public function postUpdate(
        Feature $feature,
        LifecycleEventArgs $event,
    ): void
    {
        $this->repository->deleteFromCache($feature);
        $this->repository->deleteFromCacheByKey(CacheFeatureRepository::getNamePrefix().'.'.'active_1._all');
    }

    public function postPersist()
    {
        $this->repository->deleteFromCacheByKey(CacheFeatureRepository::getNamePrefix().'.'.'active_1._all');
        $this->repository->deleteAllFromCache();
    }
}