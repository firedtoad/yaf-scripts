<?php
/**
 * @version $Id: Config.php 1540 2013-07-17 03:24:21Z zhangwenhao $
 * The Afx_Config Class Encapsulation
 * @author zhangwenhao 
 */
class Afx_Config extends Afx_Module_Abstract
{

    /**
     * cache files path
     * @var string
     */
    public static $cache_path;
    const CONFIG_PREFIX = 'CONFIG:';

    /**
     * the only instance
     * @var Afx_Config
     */
    private static $__instance = NULL;

    private static $__cache;

    private static $__seg = 100;

    private static $__hash_cache = array();

    private $array_cache = array();
    
    private  static $__config;

    private static $__allow_backend = array(
        'apc','redis','memcache'
    );

    private static $__cache_backend = 'apc';

    public static function setBackend ($backend)
    {
        if (in_array($backend, self::$__allow_backend))
        {
            self::$__cache_backend = $backend;
        }
    }

    public function __construct ()
    {
        $this->_init(TRUE);
        
        $config = Yaf_Registry::get('config');
        $this->getAdapter()->selectDatabase($config['config_db_name']);
        /*
         * 务必保证 seg参数大于100，以大约确保每个slot下条目数目大约为100左右。如果每个槽下条目数较小的话，那么将会出现某个槽下没有条目。导致time33 hash分slot存储时，
         * 实际存储slots数目小于计划的数目(见__saveCacheData方法的 $slots变量)，导致再取cache时，找不到对应的key，导致cache命中率下降
         */
        self::$__seg = $config['seg'] > 100 ? $config['seg'] : 100;
        $cache = NULL;
        switch (self::$__cache_backend)
        {
            case 'apc':
                $cache = Afx_Cache_Factory::getCache(Afx_Cache_Factory::CACHE_APC);
                break;
            case 'redis':
                $cache = Afx_Cache_Factory::getCache(Afx_Cache_Factory::CACHE_REDIS);
                break;
            case 'memcache':
                $cache = Afx_Cache_Factory::getCache(Afx_Cache_Factory::CACHE_MEMCACHE);
                break;
            default:
                break;
        }
        self::$__cache = $cache;
    }

    public function getConfig ($cache_name, $index = NULL, $index_name = 'name')
    {
        $result = $this->__getCacheData($cache_name, $index,$index_name);
        if (! $result)
        {
            $result = $this->from($cache_name)
                ->limit(0)
                ->get()
                ->result();
            if ($result)
            {
                $result = $this->__filter($result, $index_name);
                $this->__saveCacheData($cache_name, $result, $index_name);
            }
        }
        $ret = $result;
        
        if ($index!==NULL)
        {
            if (isset($result[$index]))
            {
                $ret = $result[$index];
            } else
            {
                $ret = array();
            }
        } else
        {
            $ret = $result;
        }
        return $ret;
    }

    
    public static function  setConfig($config)
    {
        self::$__config=$config;
    }
    
    /**
     * @param boolean $create
     * @return Afx_Config
     */
    public static function Instance ($create = FALSE)
    {
        if ($create)
        {
            return new self();
        }
        if (! self::$__instance instanceof Afx_Config)
        {
            self::$__instance = new self();
        }
        return self::$__instance;
    }

    public static function times33 ($string)
    {
        $string = strval($string);
        $len = strlen($string);
        $code = 0;
        for ($i = 0; $i < $len; $i ++)
        {
            $code = (int) (($code << 5) + $code) + ord($string[$i]) & 0x7fffffff;
        }
        return $code;
    }

    private function _times33 ($string)
    {
        $string = strval($string);
        $len = strlen($string);
        $code = 0;
        for ($i = 0; $i < $len; $i ++)
        {
            $code = (int) (($code << 5) + $code) + ord($string[$i]) & 0x7fffffff;
        }
        return $code;
    }

    private function __getCacheData ($cache_name, $index,$index_name)
    {
        $cache = array();
        $cache_name = self::CONFIG_PREFIX . $cache_name.':'.$index_name;
        $keys = self::$__cache->get($cache_name);
        $slot = count($keys);
        if ($index)
        {
            $cache = self::$__cache->get($cache_name);
            if (! $cache || ! isset($cache[$index]))
            {
                //$slot 获取...
                $hash = self::times33($index) % $slot;
                $hash_key = $cache_name . ':' . $hash;
                //                echo $hash_key;
                if (isset(self::$__hash_cache[$hash_key]))
                {
                    $cache = self::$__hash_cache[$hash_key];
                } else
                {
                    $cache = self::$__cache->get($hash_key);
                    self::$__hash_cache[$hash_key] = $cache;
                }
            }
        } else
        {
            if ($keys)
            {
                $first = current($keys);
                if ($keys && ! is_null($first) && is_array($first))
                {
                    $cache = $keys;
                } else
                {
                    if (is_array($keys) && count($keys))
                    {
                        foreach ($keys as $k)
                        {
                            $lcache = self::$__cache->get($cache_name . ':' . $k);
                            foreach ($lcache as $k => $v)
                            {
                                $cache[$k] = $v;
                            }
                            unset($lcache);
                        }
                    }
                }
            }
        }
        return $cache;
    }

    private function __saveCacheData ($cache_name, $result, $index_name = 'name')
    {
        $rows = count($result);
        $cache_name = self::CONFIG_PREFIX . $cache_name.':'.$index_name;
        $arr = array();
        //是否需要分段
        $need_seg = $rows >= self::$__seg;
        if ($rows > 0)
        {
            $slots = ceil($rows / self::$__seg);
            foreach ($result as $key => $value)
            {
                $index = ! is_null($value[$index_name]) ? $value[$index_name] : $key;
                $hash = self::times33($index) % $slots;
                if ($need_seg)
                {
                    if (! isset($arr[$hash]))
                    {
                        $arr[$hash] = array();
                    }
                    $arr[$hash][$index] = $value;
                } else
                {
                    $arr[$index] = $value;
                }
            }
        }
        if (count($arr))
        {
            if ($need_seg)
            {
                $key_array = array();
                foreach ($arr as $k => $value)
                {
                    $key_array[] = $k;
                    self::$__cache->set($cache_name . ':' . $k, $value, 0);
                }
                sort($key_array);
                self::$__cache->set($cache_name, $key_array, 0);
            } else
            {
                self::$__cache->set($cache_name, $arr, 0);
            }
        }
        return TRUE;
    }

    private function __filter ($result = array(), $index_name = 'name')
    {
        if (is_null($index_name))
        {
            return $result;
        }
        $ret_array = array();
        if (count($result))
        {
            foreach ($result as $value)
            {
                if (isset($value[$index_name]))
                {
                    $ret_array[$value[$index_name]] = $value;
                }
            }
        }
        return $ret_array;
    }
}

function __times33 ($string)
{
    $string = strval($string);
    $len = strlen($string);
    $code = 0;
    for ($i = 0; $i < $len; $i ++)
    {
        $code = (int) ((($code * 33)) + ord($string[$i])) & 0x7fffffff;
         //        $code = (int) ((($code<<5)+$code) + ord($string[$i])) & 0x7fffffff;
    }
    return $code;
}
class CHash
{

    static function times33 ($string)
    {
        $string = strval($string);
        $code = 0;
        $len = strlen($string);
        for ($i = 0, $len = strlen($string); $i < $len; $i ++)
        {
            $code = (int) ((($code * 33)) + ord($string[$i])) & 0x7fffffff;
        }
        return $code;
    }
}
class CHashBit
{

    static function times33 ($string)
    {
        $string = strval($string);
        $code = 0;
        for ($i = 0, $len = strlen($string); $i < $len; $i ++)
        {
            $v = ord($string[$i]);
            $code = (int) ((($code * 33)) + $v) & 0x7fffffff;
             //            $code = (int) ((($code << 5) + $code) + ord($string[$i])) & 0x7fffffff;
        }
        return $code;
    }
}

/*
** 仿zend 的time33 算法，将结果取余之后，需要abs函数转化为正整数。稍微多了一步，但性能稍高。
** @return 整数，包括负数。
*/
function __times33Zend ($arKey)
{
    $nKeyLength = strlen($arKey);
    $hash = 5381;
    $m = 0;
    for (; $nKeyLength >= 8; $nKeyLength -= 8)
    {
        $hash = (($hash << 5) + $hash) + ord($arKey{$m});
        $m ++;
        $hash = (($hash << 5) + $hash) + ord($arKey{$m});
        $m ++;
        $hash = (($hash << 5) + $hash) + ord($arKey{$m});
        $m ++;
        $hash = (($hash << 5) + $hash) + ord($arKey{$m});
        $m ++;
        $hash = (($hash << 5) + $hash) + ord($arKey{$m});
        $m ++;
        $hash = (($hash << 5) + $hash) + ord($arKey{$m});
        $m ++;
        $hash = (($hash << 5) + $hash) + ord($arKey{$m});
        $m ++;
        $hash = (($hash << 5) + $hash) + ord($arKey{$m});
        $m ++;
    }
    switch ($nKeyLength)
    {
        case 7:
            $hash = (($hash << 5) + $hash) + ord($arKey{$m});
            $m ++;
        case 6:
            $hash = (($hash << 5) + $hash) + ord($arKey{$m});
            $m ++;
        case 5:
            $hash = (($hash << 5) + $hash) + ord($arKey{$m});
            $m ++;
        case 4:
            $hash = (($hash << 5) + $hash) + ord($arKey{$m});
            $m ++;
        case 3:
            $hash = (($hash << 5) + $hash) + ord($arKey{$m});
            $m ++;
        case 2:
            $hash = (($hash << 5) + $hash) + ord($arKey{$m});
            $m ++;
        case 1:
            $hash = (($hash << 5) + $hash) + ord($arKey{$m});
            $m ++;
            break;
        case 0:
            break;
    }
    return $hash;
}