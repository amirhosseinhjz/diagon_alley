<?php

namespace App\Service\CacheService;

use App\Interface\Cache\CacheInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;

class RedisCache implements CacheInterface
{

    private static ?TagAwareAdapter $adapter = null;
    private string $tagName;
    private string $namePrefix;
    private TagAwareAdapter $redis;

    public function __construct(string $tagName, string $namePrefix)
    {
        $this->tagName = $tagName;
        $this->namePrefix = $namePrefix;
        $this->redis = $this->getAdapter();
    }

    private static function getAdapter()
    {
        if (!RedisCache::$adapter) {
            RedisCache::$adapter = new TagAwareAdapter(
                new RedisAdapter(RedisAdapter::createConnection("redis://redis:6379")));
        }
        return RedisCache::$adapter;
    }

    public function remember($key, $exp, $callable, $tagName = null)
    {
        if (!$tagName) {
            $tagName = $this->tagName;
        }
        $key = $this->namePrefix . $key;
        return $this->redis->get($key, function (ItemInterface $item) use ($tagName, $exp, $callable) {
            $item->expiresAfter($exp);
            $item->tag([$tagName]);
            return $callable();
        });
    }
}
//    public function test()
//    {
//        $this->remember('Email.32', 30, function () {
//            return $userService->getUserById($jwt->authenticatedUser()->getId());
//        }, 'hhuu');

//    }


