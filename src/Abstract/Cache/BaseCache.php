<?php
namespace App\Abstract\Cache;

use App\Service\CacheService\RedisCache;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;

abstract class BaseCache
{
    protected static ?TagAwareAdapter $adapter = null;
    protected string $tagName;
    protected string $namePrefix;
    protected TagAwareAdapter $cache;

    public function __construct(string $tagName, string $namePrefix)
    {
        $this->tagName = $tagName;
        $this->namePrefix = $namePrefix;
        $this->cache = $this->getAdapter();
    }

    private static function getAdapter()
    {
        if (!self::$adapter) {
            self::$adapter = new TagAwareAdapter(
                static::adapter()
            );
        }
        return self::$adapter;
    }

    abstract public static function adapter();
}