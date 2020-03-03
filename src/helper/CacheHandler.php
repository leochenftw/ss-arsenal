<?php

namespace Leochenftw\Util;
use Psr\SimpleCache\CacheInterface;
use SilverStripe\Core\Injector\Injector;

class CacheHandler
{
    public static function read($key, $factory = 'CacheHandler')
    {
        $cache  =   Injector::inst()->get(CacheInterface::class . '.' . $factory);

        if (!$cache->has($key)) {
            return null;
        }

        $data   =   $cache->get($key);

        return $data;
    }

    public static function delete($key = null, $factory = 'CacheHandler')
    {
        $cache  =   Injector::inst()->get(CacheInterface::class . '.' . $factory);

        if (empty($key)) {
            $cache->clear();
        } else {
            $cache->delete($key);
        }
    }

    public static function save($key, $data, $factory = 'CacheHandler')
    {
        $cache  =   Injector::inst()->get(CacheInterface::class . '.' . $factory);
        $cache->set($key, $data);
    }
}
