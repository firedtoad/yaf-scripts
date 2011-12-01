<?php
/**
 * Afx Framework
 * A Light Framework Provider Basic Communication With 
 * Databases Like Mysql Memcache Mongo and more
 * LICENSE
 * This source file is part of the Afx Framework
 * You can copy or distibute this file but don't delete the LICENSE content here
 * @copyright  Copyright (c) 2011 Banggo Technologies China Inc. (http://www.banggo.com)
 * @license Free
 */

/**
 * @package Afx_Session
 * @version $Id Handler.php
 * The Session Handler Class Wrapper Provider Seperator Read And Write
 * @author Afx team && firedtoad@gmail.com &&dietoad@gmail.com
 */

class Afx_Session_Handler{
    
    /**
     * @var Afx_Db_Memcache
     */
    private static $_memcache=NULL;
    
    /**
     * @var Afx_Session_Handler
     */
    private static $_instance=NULL;
    
    private $path;
    
    private $id;
    
    private function __construct(){
        self::$_memcache=Afx_Db_Memcache::Instance();
    }
    public static function Instance(){
        if(self::$_instance===NULL){
           self::$_instance=new self();   
        }
        return self::$_instance;
    }
    public static  function read($id){
         echo "read\n";
        if($id)return self::$_memcache->get($id);
    }
    public static function open(){
        echo "open\n";
        return TRUE;
    }
    public static function close(){
         echo "close\n";
        return(TRUE);
    }
    public static function write($id,$data){
        
         echo "write\n";
//          print_r(func_get_args());
        if($id)return self::$_memcache->set($id, $data);
    }
    public static function destroy($id){
//        echo "destroy\n";
        if($id)return self::$_memcache->delete($id);
    }
    public static function gc(){
//        echo "gc\n";
    }
    public  function __call($m,$arg){
        
    }
}