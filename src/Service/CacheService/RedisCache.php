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

    public function remember($key, $exp, $callable, $tagName = null, $expNull=null)
    {
        if (!$tagName) {
            $tagName = $this->tagName;
        }
        if (!$expNull)
        {
            $expNull = $this->expNull;
        }

        $key = $this->namePrefix . '.' . $key;
        return $this->adapter->get($key, function (ItemInterface $item) use ($tagName, $exp, $callable, $expNull) {
            $value = $callable();
            $exp = $value ? $exp : $expNull;
            $item->expiresAfter($exp);
            $item->tag([$tagName]);
            return $value;
        });
    }
}


