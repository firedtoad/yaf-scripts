<?php
/**
 * @version $Id: Queue.php 0 2012-11-08 10:55:01Z zhangwenhao $
 * The Queue Class Encapsulation with Redis in background  
 * @author zhangwenhao 
 */
class Afx_Queue
{
    //使用数组实现
    const LIST_TYPE_STRING = 'string';
    //使用hash表实现
    const LIST_TYPE_HASH = 'hash';

    /**
     * @var Afx_Cache_Redis_Adapter
     */
    private $__cache = NULL;

    /**
     * @var Afx_Cache_Memcache_Adapter
     */
    private $__mem_cache = NULL;

    /**
     * the only instance
     * @var Afx_Queue
     */
    private static $__instance = NULL;

    /**
     * @Notice this is really private 
     * @return Afx_Queue
     */
    private function __construct ()
    {
        if (! $this->__cache)
        {
            $this->__cache = Afx_Cache_Redis_Adapter::Instance();
            $this->__mem_cache = Afx_Cache_Memcache_Adapter::Instance();
        }
    }

    /**
     * the singleton method
     * @param boolean $create whether create new instance
     * @return Afx_Queue
     */
    public static function Instance ($create = FALSE)
    {
        if ($create)
        {
            return new self();
        }
        if (! self::$__instance instanceof Afx_Queue)
        {
            self::$__instance = new self();
        }
        return self::$__instance;
    }

    /**
     * add a key to  list
     * @param string $key
     * @param string $value
     * @return string
     */
    public function push ($key, $value)
    {
        $cache = $this->__cache;
        //        键不是列表 删掉
        if ($cache->type($key) != 3)
        {
            $cache->delete($key);
        }
        return $cache->lPush($key, $value);
    }

    /**
     * multi add to list
     * @param string $key
     * @param array $values
     * @return boolean
     */
    public function pushArray ($key, $values)
    {
        $op_ok = TRUE;
        if (count($values))
        {
            $cache = $this->__cache;
            if ($cache->type($key) != 3)
            {
                $cache->delete($key);
            }
            $first = array_shift($values);
            $cache->lPush($key, $first);
            foreach ($values as $value)
            {
                if (! $cache->lPushx($key, $value))
                {
                    $op_ok = FALSE;
                }
            }
        }
        return $op_ok;
    }

    /**
     * get and remove the value from the list by the specific key
     * @param key $key
     * @return string
     */
    public function pop ($key)
    {
        $cache = $this->__cache;
        return $cache->rPop($key);
    }

    /**
     * @param key $key
     * @return array
     */
    public function popAll ($key)
    {
        $array = array();
        //        if()
        $cache = $this->__cache;
        if ($cache->type($key) == 3)
        {
            $lval = NULL;
            while (FALSE != ($lval = $cache->rPop($key)))
            {
                $array[] = $lval;
            }
        }
        return $array;
    }

    /** 
     * get all the list without remove them
     * @param string $key
     */
    public function getAll ($key)
    {
        $cache = $this->__cache;
        $size = $cache->lSize($key);
        $array = array();
        if ($size > 0)
        {
            $array = $cache->lrange($key, 0, $size - 1);
        }
        return $array;
    }

    /**
     * get part of the list with the $start and the $end offset by the specific key
     * @param string $key
     * @param int $start
     * @param int $end
     * @return array
     */
    public function getRange ($key, $start, $end)
    {
        $cache = $this->__cache;
        $size = $cache->lSize($key);
        if ($end < $start)
        {
            $t = $end;
            $end = $start;
            $start = $t;
        }
        $end = $end > $size ? $size : $end;
        return $cache->lrange($key, $start, $end);
    }

    /**
     * get part of the list with the $start offset and the $count by the specific key
     * @param string $key
     * @param int $start
     * @param int $count
     * @return array
     */
    public function getList ($key, $start = 0, $count = 100)
    {
        return $this->__cache->lrange($key, $start, $start + $count - 1);
    }

    /**
     * delete the list by the specific key 
     * @notice it can delete all the kind of types in redis cache server
     * @param string $key
     * @param int $expire
     * @return boolean
     */
    public function delete ($key, $expire = 0)
    {
        return $this->__cache->delete($key, $expire);
    }

    /**
     * delete all the cache entities in the cache server
     * @deprecated it can be used only when in debug mode 
     * @param string $key
     */
    public function clearAll ()
    {
        return $this->__cache->flushAll();
    }

    /**
     * set a hash table capable with memcache
     * @param string $key
     * @param array $value  associate array
     * @return boolean
     */
    public function set ($key, $value)
    {
        return $this->__cache->hMset($key, $value);
    }

    /**
     * get an array from redis cache server capable with memcache
     * @param string $key 
     * @param array $hkeys  an array contains the array keys
     * @return array
     */
    public function get ($key, $hkeys)
    {
        return $this->__cache->hMget($key, $hkeys);
    }

    private function __queue_add_with_hash ()
    {
    }
}