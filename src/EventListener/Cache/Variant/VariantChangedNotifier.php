<?php

namespace App\EventListener\Cache\Variant;

use App\CacheEntityManager\CacheEntityManager;
use App\Entity\Variant\Variant;
use App\Interface\Cache\CacheInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\Abstract\ChangeNotifier\EntityChangeNotifier;

class VariantChangedNotifier extends EntityChangeNotifier
{
    private $repository;

    public function __construct(CacheEntityManager $em, CacheInterface $cache)
    {
        $this->repository = $em->getRepository(Variant::class);
    }

    public function postUpdate(
        Variant $variant,
        LifecycleEventArgs $event,
    ): void
    {
        $this->repository->deleteFromCache($variant);
    }

    public function postPersist()
    {
        $this->repository->deleteAllFromCache();
    }

}