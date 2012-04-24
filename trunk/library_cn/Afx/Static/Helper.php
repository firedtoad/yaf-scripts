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
 * @package Afx_Static
 * @version $Id Helper.php
 * The Static Helper output some static content
 * @author Afx team && firedtoad@gmail.com &&dietoad@gmail.com
 */
/**
 * 输出一些静态内容
 * @author dell
 *
 */
class Afx_Static_Helper{
   /**
    * output some url
    * @param string $type
    */
   public static function output($type='url'){
       switch ($type)
       {
           case 'url':
               self::outputUrl();
               break;
           default:break;
       }
   }
   public static function outputUrl(){
        $conf=Yaf_Registry::get('conf');
        $js_str="<script type='text/javascript'>\n";
        $content=FALSE;
        if(isset($conf['staticUrls'])&&is_array($conf['staticUrls']))
        {
          foreach ($conf['staticUrls'] as $k=>$v) {
             if(!$content)
             {
               $content=TRUE;
             }
             $js_str.="var ".strtolower($k)."='$v';\n";
          }
        }
       echo $js_str.="</script>\n";
   }
   /**
    * set view variables
    * @param Yaf_Controller_abstract $controller
    * @param string $key the key in the conf.php where can locate an array
    */
   public static function setViewVars($controller,$key){
        $conf=Yaf_Registry::get('conf');
        if(isset($conf[$key])&&is_array($conf[$key]))
        {
           foreach ($conf[$key] as $k=>$v) {
               $controller->getView()->$k=$v;
           }
        }
   }
}