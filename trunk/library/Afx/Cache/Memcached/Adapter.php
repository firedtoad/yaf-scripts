<?php
class Afx_Cache_Memcached_Adapter implements Afx_Cache_Adapter
{

    private static $__debug;

    //    private $__host;
    //    private $__port;
    //    private $__persist;
    private $__servers = array();

    private $__mem;

    private $__config;

    private $__dump_keys = array();

    /**
     * 
     * Afx_Cache_Memcached_Adapter
     * @var unknown_type
     */
    private static $__instance = NULL;

    /**
     * The singleton method
     * @return Afx_Cache_Memcache_Adapter
     */
    public static function Instance ($create = FALSE)
    {
        if ($create)
        {
            return new self();
        }
        if (! (self::$__instance instanceof Afx_Cache_Memcache_Adapter))
        {
            self::$__instance = new self();
        }
        return self::$__instance;
    }

    public function auth ($pass)
    {
    }

    /**
     * set debug on|off
     * @param boolean $bool
     */
    public static function debug ($bool)
    {
        self::$__debug = $bool;
    }

    /**
     * init the configuration 
     * @param array $config
     */
    public function setConfig ($config)
    {
        //        $this->__host=$config['host'];
        //        $this->__port=$config['port'];
        //        $this->__persist=$config['persist'];
        $this->__config = $config;
        $this->__servers = $config['servers'];
    }

    /**
     * @return Afx_Cache_Memcache_Adapter
     */
    public function __construct ()
    {
        //        $this->__init();
    }

    /**
     * do not connect it will connect automaticly  when  first time operator perform
     * @return Afx_Cache_Memcache_Adapter
     */
    public function init ()
    {
        $mem = new Memcached();
        //        $mem->addServer($this->__host,$this->__port,$this->__persist,$this->__timeout);
        foreach ($this->__servers as $value)
        {
            //            $mem->connect($value['host'],$value['port'],$value['timeout']);
            $mem->addServer($value['host'], $value['port'], $value['weight']);
            $this->__dump_keys[] = $value['host'] . ':' . $value['port'];
        }
        $this->__mem = $mem;
        return $this;
    }

    /**
     * dercrement the key in cache
     * @param string $key
     * @param int $value
     * @return boolean 
     */
    public function decrement ($key, $value = 1)
    {
        return $this->__mem->decrement($key, $value);
         //        return $this;
    }

    public function remove ($key, $value, $count)
    {
        return TRUE;
    }

    public function length ($key)
    {
        return 0;
    }

    /**
     * delete the key in cache
     * @param string $key
     * @param int $expire delete after seconds or zero delete immediately
     * @return boolean 
     */
    public function delete ($key, $expire = 0)
    {
        return $this->__mem->delete($key, $expire = 0);
         //        return $this;
    }

    /**
     * @param mixed $key string or array represents the key or keys to fetch
     * @return mixed 
     */
    public function get ($key)
    {
        return $this->__mem->get($key);
         //        return $this;
    }

    /**
     * increment the key in cache
     * @param string $key
     * @param int $value
     * @return boolean 
     */
    public function increment ($key, $value = 1)
    {
        return $this->__mem->increment($key, $value);
         //        return $this;
    }

    /**
     * replace the cache value with the specific key
     * @param $key 
     * @param $value
     * @param $expire 
     * @param $flag    the compress method
     * @return boolean 
     */
    public function replace ($key, $value, $expire = 3600, $flag = MEMCACHE_COMPRESSED)
    {
        return $this->__mem->replace($key, $value, $flag, $expire);
         //        return $this;
    }

    public function exists ($key)
    {
        return $this->__mem->get($key);
    }

    /**
     * update the cache value with the specific key
     * @param $key 
     * @param $value
     * @param $expire 
     * @param $flag    the compress method
     * @return boolean 
     */
    public function cas ($key, $value, $expire = 3600, $flag = MEMCACHE_COMPRESSED)
    {
        $cas = NULL;
        return $this->__mem->cas($cas, $key, $value, $flag, $expire);
         //        return $this;
    }

    /**
     * add a value to the cache use the key
     * @param $key 
     * @param $value
     * @param $expire 
     * @param $flag    the compress method
     * @return boolean 
     */
    public function add ($key, $value, $expire = 3600, $flag = MEMCACHE_COMPRESSED)
    {
        return $this->__mem->add($key, $value, $flag, $expire);
         //        return $this;
    }

    /**
     * set the value of the  key
     * @param $key 
     * @param $value
     * @param $expire 
     * @param $flag    the compress method
     * @return boolean 
     */
    public function set ($key, $value, $expire = 3600, $flag = MEMCACHE_COMPRESSED)
    {
        return $this->__mem->set($key, $value, $flag, $expire);
         //        return $this;
    }

    /**
     * get the cache version
     */
    public function getVersion ()
    {
        return $this->__mem->getVersion();
    }

    /**
     * get the cache stats
     * @param string $type slabs|items
     * @param int $slab slabs|items
     * @param int $limit 
     */
    public function getStats ($type, $slab, $limit)
    {
        return $this->__mem->getStats($type, $slab = 0, $limit = 100);
    }

    /**
     * get the cache server status
     * @param string $host
     * @param int $port 
     */
    public function getServerStatus ($host, $port)
    {
        return $this->__mem->getServerStatus($host, $port);
    }

    /**
     * get the cache stats
     * @param string $type slabs|items
     * @param int $slab slabs|items
     * @param int $limit 
     */
    public function getExtendedStats ($type, $slab = 0, $limit = 1000)
    {
        return $this->__mem->getExtendedStats($type, $slab, $limit);
    }

    /**
     * flush all the data 
     * notice this function is only called when in debug mode
     * @deprecated
     * @return boolean
     */
    public function flush ()
    {
        return $this->__mem->flush();
    }

    /**
     * @param int $threshold
     * @param float $min_savings
     * @return boolean
     */
    public function setCompressThreshold ($threshold, $min_savings)
    {
        return $this->__mem->setCompressThreshold($threshold, $min_savings);
    }

    /**
     * @param string $host
     * @param int $port
     * @param int $timeout
     * @param int $retry
     * @param boolean $status
     * @param function $failure_callback
     * @return boolean
     */
    public function setServerParams ($host, $port, $timeout, $retry, $status, $failure_callback)
    {
        return $this->__mem->setServerParams($host, $port, $timeout, $retry, $status, $failure_callback);
    }

    /**
     * dump the all cache data or the specific by the prefix 
     * @param string $prefix
     * @param boolean $output
     */
    public function dump ($prefix = '', $output = FALSE)
    {
        $return_array = array();
        //$allSlabs = $this->getExtendedStats('slabs');
        $allItems = $this->getExtendedStats('items');
        $key = $this->__dump_keys[0];
        $len = strlen($prefix);
        foreach ($allItems as $server => $slabs)
        {
            $item = array();
            foreach ($slabs as $slabId => $slabMeta)
            {
                foreach ($slabMeta as $k => $v)
                {
                    $cdump = $this->getExtendedStats('cachedump', $k, 10000);
                    if (is_array($cdump[$key]) && count($cdump[$key]))
                    {
                        $keys = array_keys($cdump[$key]);
                        if ($prefix)
                        {
                            foreach ($keys as $k => $value)
                            {
                                if (strncasecmp($value, $prefix, $len) != 0)
                                {
                                    unset($keys[$k]);
                                }
                            }
                        }
                        if (count($keys))
                        {
                            $data = $this->get($keys);
                            $data = array_combine($keys, $data);
                            $output && print_r($data);
                            ! $prefix && $item[] = array(
                                'count'=>count($data),'data'=>$data
                            );
                            $prefix && $return_array = array_merge($return_array, $data);
                        }
                    }
                }
            }
            ! $prefix && $return_array[$server] = $item;
        }
        return $return_array;
    }
}
