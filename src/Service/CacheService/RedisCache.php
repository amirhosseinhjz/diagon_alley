<?php

namespace App\Service\CacheService;

use App\Abstract\Cache\BaseCache;
use App\Interface\Cache\CacheInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class RedisCache extends BaseCache implements CacheInterface
{
    public static function adapter()
    {
        return new RedisAdapter(RedisAdapter::createConnection("redis://redis:6379"));
    }
    public function remember($key, $exp, $callable, $tagName = null)
    {
        if (!$tagName) {
            $tagName = $this->tagName;
        }
        $key = $this->namePrefix . $key;
        return $this->cache->get($key, function (ItemInterface $item) use ($tagName, $exp, $callable) {
            $item->expiresAfter($exp);
            $item->tag([$tagName]);
            return $callable();
        });
    }
}


