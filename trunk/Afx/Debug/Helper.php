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
 * @package Afx_Db
 * @version $Id Adapter.php
 * The Pdo Class Adapter Provider Communication With The RelationShip Database
 * @author Afx team && firedtoad@gmail.com &&dietoad@gmail.com
 */
class Afx_Debug_Helper
{
    public static function print_r(){
        
       // static $colors=array('red','yellow','green');
        $color=dechex(rand(0,255)).dechex(rand(0,255)).dechex(rand(0,255));
       if(strlen($color)!=6){
            $color.=str_repeat(0, 6-strlen($color));
        }
        echo "<pre style=\"background-color:#$color;\">";
        $arr=func_get_args();
        foreach ($arr as $k=>$v) {
            print_r($v);
        }
        echo '</pre>';
    }
    public static function var_dump(){
        $color=dechex(rand(0,255)).dechex(rand(0,255)).dechex(rand(0,255));
        if(strlen($color)!=6){
            $color.=str_repeat(0, 6-strlen($color));
        }
        echo "<pre style=\"background-color:#$color;\">";
        $arr=func_get_args();
        foreach ($arr as $k=>$v) {
            var_dump($v);
        }
        echo '</pre>';
    }
    public static function export(){
        
    }
}
?>