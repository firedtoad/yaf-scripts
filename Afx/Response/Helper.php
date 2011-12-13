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
 * @package Afx_Response
 * @version $Id Helper.php
 * The Helper for response
 * @author Afx team && firedtoad@gmail.com &&dietoad@gmail.com
 */
class Afx_Response_Helper {
   public static function makeResponse($arr,$message=NULL,$code='0000'){
     $str=json_encode($arr);
   	 exit("{\"data\"=>\"$str\",\"code\"=>\"$code\",\"message\"=>\"$message\"}");
   }
   private static function _responseJSON(){
   	
   }
   private static function _responseXML(){
   	
   }
   private static function _responseHTML(){
   	
   }
   private static function _responseRaw(){
   	
   }
}

?>