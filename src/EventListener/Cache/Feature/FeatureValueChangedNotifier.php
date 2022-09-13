<?php

namespace App\EventListener\Cache\Feature;

use App\CacheEntityManager\CacheEntityManager;
use App\CacheRepository\FeatureRepository\CacheFeatureValueRepository;
use App\Entity\Feature\FeatureValue;
use App\Interface\Cache\CacheInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\Abstract\ChangeNotifier\EntityChangeNotifier;

class FeatureValueChangedNotifier extends EntityChangeNotifier
{
    private $repository;

    public function __construct(CacheEntityManager $em)
    {
        $this->repository = $em->getRepository(FeatureValue::class);
    }

    public function postUpdate(
        FeatureValue $featureValue,
        LifecycleEventArgs $event,
    ): void
    {
        $this->repository->deleteFromCache($featureValue);
        $this->repository->deleteFromCacheByKey(CacheFeatureValueRepository::getNamePrefix().'.'.'active_1._all');
    }

    public function postPersist()
    {
        $this->repository->deleteAllFromCache();
        $this->repository->deleteFromCacheByKey(CacheFeatureValueRepository::getNamePrefix().'.'.'active_1._all');
    }

}