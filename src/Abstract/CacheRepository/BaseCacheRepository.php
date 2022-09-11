<?php
namespace App\Abstract\CacheRepository;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Interface\Cache\CacheInterface;

abstract class BaseCacheRepository
{
    private ServiceEntityRepository $repository;
    private CacheInterface $cache;
    private int $exp;

    public static abstract function getNamePrefix():string;

    public static abstract function getCacheKeys(): array;

    public function __construct(ServiceEntityRepository $repository, CacheInterface $cache, string $tagName, int $exp)
    {
        $this->repository = $repository;
        $this->cache = new $cache($tagName);
        $this->tagName = $tagName;
        $this->exp = $exp ?: (int)$_ENV['CACHE_TTL'];
    }

    public function find($id, $cache=true)
    {
        $key = $this->getKey(['id' => $id]);
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
        $key = $this->getKey($criteria);
        if (!$key)
        {
            if ($cache){
                throw new \Exception('Cache not allowed');
            } else {
                return $this->repository->findOneBy($criteria, $orderBy);
            }
        }
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

    public function findAll($cache=true)
    {
        $key = static::getKeyAll();
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

    private function getKey(array $criteria)
    {
        if (count($criteria) > 1) {
            return null;
        }
        $key = array_keys($criteria)[0];
        if (!in_array($key, static::getCacheKeys())) {
            return null;
        }
        return static::_getKey($key, $criteria[$key]);
    }

    public static function getKeyAll()
    {
        return static::getNamePrefix().'.__all__';
    }

    public static function _getKey(string $key, string $value)
    {
        $result =  static::getNamePrefix() . '.' . $key . '_' . $value;
        return static::removeSpecialCharacters($result);
    }

    private function saveToCache($key, $value)
    {
        $item = $this->cache->getAdapter()->getItem($key);
        $item->tag([$this->tagName]);
        $item->expiresAfter($this->exp);
        $item->set($value);
        $this->cache->getAdapter()->save($item);
    }

    public function deleteAllFromCache()
    {
        $this->cache->forget(static::getKeyAll());
    }

    public function deleteFromCache(
        $entityObject
    )
    {
        $keys = static::getCacheKeys();
        foreach ($keys as $key)
        {
            $value = $entityObject->{'get'.ucfirst($key)}();
            $key = $this->_getKey($key, $value);
            $this->cache->forget($key);
        }
        $this->deleteAllFromCache();
    }

    private static function removeSpecialCharacters($string) {

        $specChars = array(
            ' ' => '_',
            '#' => '',    '$' => '',    '%' => '',
            '&' => '',    '\'' => '',   '(' => '',
            ')' => '',    '*' => '',     ';' => '',
            '--' => '-',   ',' => '',
            '/-' => '',    ':' => '',   
            '@' => '',    '[' => '',
            '\\' => '',   ']' => '',
            '`' => '',    '{' => '',
            '}' => '',
            '/' => '',
        );

        foreach ($specChars as $k => $v) {
            $string = str_replace($k, $v, $string);
        }
        return $string;
    }
}