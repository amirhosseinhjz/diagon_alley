<?php
namespace App\Abstract\Cache;

use Symfony\Component\Cache\Adapter\TagAwareAdapter;

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
        $this->expNull = $_ENV['NULL_CACHE_TTL'];
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