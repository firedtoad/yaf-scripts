<?php
class Afx_Cache_Factory
{
    const CACHE_MEMCACHE=1;
    const CACHE_MEMCACHED=2;
    const CACHE_REDIS=3;
    const CACHE_APC=4;
    /**
     * @return Afx_Cache_Redis_Adapter
     */
    public static function getCache($type=self::CACHE_REDIS)
    {
        $cache=NULL;
//        $type=self::CACHE_MEMCACHE;
        switch ($type)
        {
            case self::CACHE_MEMCACHE:
                $cache=Afx_Cache_Memcache_Adapter::Instance();
                break;
            case self::CACHE_MEMCACHED:
                 $cache=Afx_Cache_Memcached_Adapter::Instance();
                break;
            case self::CACHE_REDIS:
                $cache=Afx_Cache_Redis_Adapter::Instance();
                break;
            case self::CACHE_APC:
                $cache=Afx_Cache_Apc_Adapter::Instance();
                break;
            default:break;
        }
        return $cache;
    }
}