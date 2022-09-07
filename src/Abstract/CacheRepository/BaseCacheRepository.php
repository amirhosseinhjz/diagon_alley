<?php
namespace App\Abstract\CacheRepository;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Interface\Cache\CacheInterface;

abstract class BaseCacheRepository
{
    private ServiceEntityRepository $repository;
    private CacheInterface $cache;
    private int $exp;

    public function __construct(ServiceEntityRepository $repository, CacheInterface $cache, string $tagName, string $namePrefix, int $exp)
    {
        $this->repository = $repository;
        $this->cache = new $cache($tagName, $namePrefix);
        $this->tagName = $tagName;
        $this->exp = $exp ?: (int)$_ENV['CACHE_TTL'];
    }

    public function find($id, $cache=true)
    {
        $key = 'id_' . $id;
        if ($cache)
        {
            return $this->cache->remember($key, $this->exp, function () use ($id) {
                return $this->repository->find($id);
            });
        } else {
            $result = $this->repository->find($id);
            $this->saveToCache($key, $result);
            return $result;
        }
    }

    public function findOneBy(array $criteria, array $orderBy = null, $cache=true)
    {
        $key = $this->getKey($criteria, $orderBy);
        if ($cache)
        {
        return $this->cache->remember($key, $this->exp, function () use ($criteria, $orderBy) {
            return $this->repository->findOneBy($criteria, $orderBy);
        });
        } else {
            $result = $this->repository->findOneBy($criteria, $orderBy);
            $this->saveToCache($key, $result);
            return $result;
        }
    }

    private function getKey(array $criteria, array $orderBy = null)
    {
        $key = '';
        foreach ($criteria as $_key => $value) {
            $key .=  '.' . $_key . '_' . $value;
        }
//        if ($orderBy) {
//            foreach ($orderBy as $key => $value) {
//                $key .= $key . $value;
//            }
//        }
        return $key;
    }

    public function findAll($cache=true)
    {
        $key = '__all__';
        if ($cache) {
            return $this->cache->remember($key, $this->exp, function () {
                return $this->repository->findAll();
            });
        } else {
            $result = $this->repository->findAll();
            $this->saveToCache($key, $result);
            return $result;
        }
    }

    private function saveToCache($key, $value)
    {
        $item = $this->cache->getAdapter()->getItem($key);
        $item->tag([$this->tagName]);
        $item->expiresAfter($this->exp);
        $item->set($value);
        $this->cache->getAdapter()->save($item);
    }
}