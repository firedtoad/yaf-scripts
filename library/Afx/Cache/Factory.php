<?php
class Afx_Cache_Factory
{
    const CACHE_MEMCACHE=1;
    const CACHE_MEMCACHED=2;
    const CACHE_REDIS=3;
    const CACHE_APC=4;
    
    private static $__cache=array();
    
    /**
     * @return Afx_Cache_Redis_Adapter
     */
    public static function getCache($type=self::CACHE_REDIS,$write=FALSE)
    {
        $cache=NULL;
        $key=$write?'write':'read';
//        $type=self::CACHE_MEMCACHE;
       if(isset(self::$__cache[$key])&& (self::$__cache[$key])instanceof Afx_Cache_Adapter)
       {
           $cache=self::$__cache[$key];
       }
       if(!$cache)
       {
           switch ($type)
            {
                case self::CACHE_MEMCACHE:
                    $cache=Afx_Cache_Memcache_Adapter::Instance($write);
                    break;
                case self::CACHE_MEMCACHED:
                     $cache=Afx_Cache_Memcached_Adapter::Instance($write);
                    break;
                case self::CACHE_REDIS:
                    $cache=Afx_Cache_Redis_Adapter::Instance($write);
                    if($write)
                    {
                        $cache_read=Afx_Cache_Redis_Adapter::Instance();
                        $config=$cache_read->getOptions();
                        $cache->setConfig($config);
                        $cache->init();
                    }
                    break;
                case self::CACHE_APC:
                    $cache=Afx_Cache_Apc_Adapter::Instance($write);
                    break;
                default:break;
            }
            self::$__cache[$key]=$cache;
       }
        return $cache;
    }
}