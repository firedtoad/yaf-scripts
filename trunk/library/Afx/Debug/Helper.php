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
    public static function print_r ()
    {
        // static $colors=array('red','yellow','green');
        $color = dechex(rand(100, 200)) . dechex(rand(100, 200)) .
         dechex(rand(100, 200));
        if (strlen($color) != 6) {
            $color .= str_repeat(0, 6 - strlen($color));
        }
        echo "<pre style=\"background-color:#$color;\">";
        $arr = func_get_args();
        foreach ($arr as $k => $v) {
            if (is_object($v)) {
                 print_r($v);
                $ref = new ReflectionClass($v);
                $methods = $ref->getMethods();
                $consts = $ref->getConstants();
                $prosps=$ref->getProperties();
                $statics=$ref->getStaticProperties();

                $name=$ref->getName();
                echo 'name='.$name."\n";
                echo 'consts={';
                if ($consts && is_array($consts)) {
                    foreach ($consts as $k2 => $v2) {
                        if (method_exists($v2, '__toString')) {
                            echo $v2->__toString();
                        }
                    }
                }
                echo "}\n";
                echo "Prosp={\n";
                if ($prosps && is_array($prosps)) {
                    foreach ($prosps as $k3 => $v3) {
                        if (method_exists($v3, '__toString')) {
                            echo $v3->__toString();
                        }
                    }
                }
                echo "}\n";
                echo "static prosp={\n";
               if ($statics && is_array($statics)) {
                    foreach ($statics as $k4 => $v4) {
                        if (method_exists($v4, '__toString')) {
                            echo $v4->__toString();
                        }
                    }
                }
                 echo "}\n";
                 echo "methods={\n";
                if ($methods && is_array($methods)) {
                    foreach ($methods as $k1 => $v1) {
                        if (method_exists($v1, '__toString')) {
                            echo $v1->__toString();
                        }
                    }
                }
                 echo "}\n";
            } else {
                print_r($v);
            }
        }
        echo '</pre>';
    }
    public static function var_dump ()
    {
        $color = dechex(rand(0, 255)) . dechex(rand(0, 255)) .
         dechex(rand(0, 255));
        if (strlen($color) != 6) {
            $color .= str_repeat(0, 6 - strlen($color));
        }
        echo "<pre style=\"background-color:#$color;\">";
        $arr = func_get_args();
        foreach ($arr as $k => $v) {
            var_dump($v);
        }
        echo '</pre>';
    }
    public static function export ()
    {
        $color = dechex(rand(0, 255)) . dechex(rand(0, 255)) .
         dechex(rand(0, 255));
        if (strlen($color) != 6) {
            $color .= str_repeat(0, 6 - strlen($color));
        }
        echo "<pre style=\"background-color:#$color;\">";
        $arr = func_get_args();
        foreach ($arr as $k => $v) {
            ReflectionClass::export($v);
        }
        echo '</pre>';
    }
    public function exportExtension ()
    {
        $color = dechex(rand(0, 255)) . dechex(rand(0, 255)) .
         dechex(rand(0, 255));
        if (strlen($color) != 6) {
            $color .= str_repeat(0, 6 - strlen($color));
        }
        echo "<pre style=\"background-color:#$color;\">";
        $arr = func_get_args();
        foreach ($arr as $k => $v) {
            ReflectionExtension::export($v);
        }
        echo '</pre>';
    }
}
?>