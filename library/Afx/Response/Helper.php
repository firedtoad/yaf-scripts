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
class Afx_Response_Helper
{
     /**
      * json输出
      * @param array $arr
      * @param string $message
      * @param string $code
      */
    public static function makeResponse ($arr, $message = NULL, $code = '0000')
    {
        $res = array('data' => $arr, 'message' => $message,
        'code' => $code);
        ob_clean();
        exit(json_encode($res));
    }
    private static function _responseJSON ()
    {}
    private static function _responseXML ()
    {}
    private static function _responseHTML ()
    {}
    private static function _responseRaw ()
    {}
}
?>