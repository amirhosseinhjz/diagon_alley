<?php
namespace App\Abstract\CacheRepository;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Interface\Cache\CacheInterface;

class BaseCacheRepository
{
    private ServiceEntityRepository $repository;
    private CacheInterface $cache;
    private int $exp;

    public function __construct(ServiceEntityRepository $repository, CacheInterface $cache, int $exp, string $tagName, string $namePrefix)
    {
        $this->repository = $repository;
        $this->cache = new $cache($tagName, $namePrefix);
        $this->exp = $exp;
    }

    public function find($id)
    {
        return $this->cache->remember($id, $this->exp, function () use ($id) {
            return $this->repository->find($id);
        });
    }

    public function findOneBy(array $criteria, array $orderBy = null)
    {
        $key = $this->getKey($criteria, $orderBy);
        return $this->cache->remember($key, $this->exp, function () use ($criteria, $orderBy) {
            return $this->repository->findOneBy($criteria, $orderBy);
        });
    }

    private function getKey(array $criteria, array $orderBy = null)
    {
        $key = '';
        foreach ($criteria as $key => $value) {
            $key .= $key . $value;
        }
        if ($orderBy) {
            foreach ($orderBy as $key => $value) {
                $key .= $key . $value;
            }
        }
        return $key;
    }

    public function findAll()
    {
        return $this->cache->remember('all', $this->exp, function () {
            return $this->repository->findAll();
        });
    }
}