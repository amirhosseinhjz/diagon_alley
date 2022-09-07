<?php
namespace App\Abstract\Cache;

use Symfony\Component\Cache\Adapter\TagAwareAdapter;

abstract class BaseCache
{
    protected static ?TagAwareAdapter $_adapter = null;
    protected string $tagName;
    protected string $namePrefix;
    protected TagAwareAdapter $adapter;
    protected $expNull;

    public function __construct(string $tagName, string $namePrefix)
    {
        $this->tagName = $tagName;
        $this->namePrefix = $namePrefix;
        $this->adapter = $this->_getAdapter();
        $this->expNull = (int)$_ENV['NULL_CACHE_TTL'];
    }

    public function getAdapter():TagAwareAdapter
    {
        return $this->adapter;
    }

    private static function _getAdapter():TagAwareAdapter
    {
        if (!self::$_adapter) {
            self::$_adapter = new TagAwareAdapter(
                static::adapter()
            );
        }
        return self::$_adapter;
    }

    abstract public static function adapter();
}