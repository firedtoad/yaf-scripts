<?php
interface Afx_Cache_Adapter
{
    public static function Instance();
    public function add($key,$value,$expire);
    public function get($key);
    public function set($key,$value,$expire);
    public function delete($key,$expire);
    public function remove($key,$value,$count);
    public function length($key);
    public function replace($key,$value,$expire);
    public function decrement($key,$value);
    public function increment($key,$value);
    public function exists($key);
    public function flush();
}