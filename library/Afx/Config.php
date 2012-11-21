<?php
/**
 * @version $Id: Config.php 0 2012-11-08 10:55:01Z zhangwenhao $
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

    /**
     * the only instance
     * @var Afx_Config
     */
    private static $__instance = NULL;

    private static $__cache;

    private static $__seg = 100;

    private $array_cache = array();

    public function __construct ()
    {
        $this->getAdapter()->selectDatabase('jv06');
        $config = Yaf_Registry::get('config');
        self::$__seg = $config['seg'];
        self::$__cache = Afx_Cache_Factory::getCache(Afx_Cache_Factory::CACHE_APC);
    }

    public function getConfig ($cache_name, $index = NULL)
    {
        $result = $this->__getCacheData($cache_name, $index);
        if (! $result)
        {
            $result = $this->from($cache_name)
                ->limit(0)
                ->get()
                ->result();
            if ($result)
            {
                $this->__saveCacheData($cache_name, $result);
            }
        }
        $ret = $result;
        if (isset($result[$index]))
        {
            $ret = $result[$index];
        }
        return $ret;
    }

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

    private function __getCacheData ($cache_name, $index)
    {
        $cache = NULL;
        if ($index)
        {
            $cache = self::$__cache->get($cache_name);
            if (! $cache || ! isset($cache[$index]))
            {
                $hash = self::times33($index) % self::$__seg;
                $cache = self::$__cache->get($cache_name . $hash);
            }
        } else
        {
            $cache = array();
            $keys = self::$__cache->get($cache_name);
            if ($keys)
            {
                $first = array_shift($keys);
                if ($keys && ! is_null($first) && is_array($first))
                {
                    array_push($keys, $first);
                    $cache = $keys;
                } else
                {
                    array_push($keys, $first);
                    if (is_array($keys) && count($keys))
                    {
                        foreach ($keys as $k)
                        {
                            $lcache = self::$__cache->get($cache_name . $k);
                            $cache = array_merge($cache, $lcache);
                        }
                    }
                }
            }
        }
        return $index ? (isset($cache[$index]) ? $cache[$index] : '') : $cache;
    }

    private function __saveCacheData ($cache_name, $result)
    {
        $rows = count($result);
        $arr = array();
        //是否需要分段
        $need_seg = $rows >= self::$__seg;
        if ($rows > 0)
        {
            foreach ($result as $value)
            {
                $hash = self::times33($value['name']) % self::$__seg;
                if ($need_seg)
                {
                    if (! isset($arr[$hash]))
                    {
                        $arr[$hash] = array();
                    }
                    $arr[$hash][$value['name']] = $value;
                } else
                {
                    $arr[$value['name']] = $value;
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
                    self::$__cache->set($cache_name . $k, $value);
                }
                sort($key_array);
                self::$__cache->set($cache_name, $key_array);
            } else
            {
                self::$__cache->set($cache_name, $arr);
            }
        }
        return TRUE;
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
