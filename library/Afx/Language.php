<?php
/**
 * @version $Id: Language.php 96 2012-12-10 06:49:16Z zhangwenhao $
 * The Language package Encapsulation 
 * @author zhangwenhao 
 */
class Afx_Language
{
    /**
     * the language pack path
     * @var string
     */
    private static $__pack_path='';
    /**
     * the packages already loaded
     * @var array
     */
    private $__packages = array();
    /**
     * @var string
     */
    private $__current_pack_name;
    /**
     * the only instance
     * @var Afx_Language
     */
    private static $__instance = NULL;
    /**
     * @Notice this is really private 
     * @return Afx_Language
     */
    private function __construct ()
    {
    }
    /**
     * the singleton method
     * @param boolean $create whether create new instance
     * @return Afx_Language
     */
    public static function Instance ($create = FALSE)
    {
        if ($create)
        {
            return new self();
        }
        if (! self::$__instance instanceof Afx_Language)
        {
            self::$__instance = new self();
        }
        return self::$__instance;
    }
    public static function setPackPath($path)
    {
        self::$__pack_path=$path;
    }
    public function load($pack_name)
    {
        $reload=FALSE;
        $pack_file=self::$__pack_path.'/'.$pack_name.'.php';
        //防止重复加载
        if(isset($this->__packages[$pack_name]))
        {
            $this->__current_pack_name=$pack_name;
            $reload=TRUE;
        }
        if(!$reload&&file_exists($pack_file))
        {
            $this->__current_pack_name=$pack_name;
            $tmp=include $pack_file;
            //兼容 W1 语言包写法
            if(!is_array($tmp))
            {
                $tmp=$$pack_name;
            }
             $this->__packages[$pack_name]=$tmp;
        }
        return TRUE;
    }
    public function get($key)
    {
        return isset($this->__packages[$this->__current_pack_name][$key])?$this->__packages[$this->__current_pack_name][$key]:'';
    }
}