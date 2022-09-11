<?php
namespace App\CacheEntityManager;


use App\Interface\Cache\CacheInterface;
use Doctrine\ORM\EntityManager;
use App\Entity;
use App\CacheRepository;
use Doctrine\ORM\EntityManagerInterface;

class CacheEntityManager extends EntityManager
{
    private const Entities = [
        Entity\User\Seller::class =>
            CacheRepository\UserRepository\CacheSellerRepository::class,
        ];

    public function __construct(EntityManagerInterface $em, CacheInterface $cache)
    {
        parent::__construct($em->getConnection(), $em->getConfiguration());
        $this->cache = $cache;
    }

    public function getRepository($entityName)
    {
        $repository = parent::getRepository($entityName);
        if (!isset(self::Entities[$entityName]))
        {
            return $repository;
        }
        $cacheRepository = self::Entities[$entityName];
        return new $cacheRepository($repository, $this->cache);
    }
}