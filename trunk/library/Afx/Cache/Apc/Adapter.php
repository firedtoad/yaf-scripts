<?php
class Afx_Cache_Apc_Adapter implements Afx_Cache_Adapter
{

    /**
     * the only instance
     * @var Afx_Cache_Apc_Adapter
     */
    private static $__instance = NULL;

    /**
     * @param boolean $create
     * @return Afx_Cache_Apc_Adapter
     */
    public static function Instance ($create = FALSE)
    {
        if ($create)
        {
            self::$__instance = new self();
        }
        if (! self::$__instance instanceof Afx_Cache_Apc_Adapter)
        {
            self::$__instance = new self();
        }
        return self::$__instance;
    }

    public function length ($key)
    {
        return 0;
    }

    public function auth($pass)
    {
        
    }
    
    
    public function keys ($pattern)
    {
        return;
    }
    
    
    public function add ($key, $value, $expire = 0)
    {
        return apc_add($key, $value, $expire);
    }

    public function remove ($key, $value, $count)
    {
        return TRUE;
    }

    public function decrement ($key, $value)
    {
        $success = TRUE;
        apc_dec($key, $value, $success);
        return $success;
    }

    public function delete ($key, $expire)
    {
        return apc_delete($key);
    }

    public function get ($key)
    {
        $success = TRUE;
        $data = apc_fetch($key, $success);
        return $data;
    }

    public function increment ($key, $value)
    {
        $success = TRUE;
        apc_inc($key, $value, $success);
        return $success;
    }

    public function replace ($key, $value, $expire = 0)
    {
        $success = TRUE;
        $old = apc_fetch($key);
        $ret = apc_cas($key, $old, $value);
        return $ret;
    }

    public function set ($key, $value, $expire = 0)
    {
        return apc_store($key, $value, $expire);
    }

    public function cacheFile ($file)
    {
        return apc_compile_file($file);
    }

    public function flush ()
    {
        return apc_clear_cache();
    }

    public function exists ($key)
    {
        return apc_exists($key);
    }
}