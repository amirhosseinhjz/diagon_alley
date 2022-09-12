<?php
namespace App\Abstract\Cache;

use Symfony\Component\Cache\Adapter\TagAwareAdapter;

abstract class BaseCache
{
    protected static ?TagAwareAdapter $_adapter = null;
    protected string $tagName;
    protected TagAwareAdapter $adapter;
    protected $expNull;

    public function __construct(string $tagName)
    {
        $this->tagName = $tagName;
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

    public function forget(string $key)
    {
        return $this->adapter->delete($key);
    }

    abstract public static function adapter();
}