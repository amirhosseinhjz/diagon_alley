<?php
namespace App\Abstract\Cache;

use App\Service\CacheService\RedisCache;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

abstract class BaseCache
{
    protected static ?TagAwareAdapter $adapter = null;
    protected string $tagName;
    protected string $namePrefix;
    protected TagAwareAdapter $cache;
    protected $expNull;

    public function __construct(string $tagName, string $namePrefix)
    {
        $this->tagName = $tagName;
        $this->namePrefix = $namePrefix;
        $this->cache = $this->getAdapter();
        $this->expNull = env('NULL_CACHE_TTL');
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