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
 * @package Afx_Request
 * @version $Id Helper.php
 * The Helper for Request
 * @author Afx team && firedtoad@gmail.com &&dietoad@gmail.com
 */
class Afx_Request_Helper
{
    /**
     * @var Afx_Request_Helper
     */
    protected static $_instance;
    protected static $ch;
    protected static $url;
    protected static $history = array();
    protected function __construct ()
    {
        //self::_init();
    }
    private function _init ()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        self::$ch = $ch;
    }
    private function _close ()
    {
        curl_close(self::$ch);
    }
    public static function Instance ()
    {
        if (! self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    private static function _get ($url)
    {
        $request = new CAS_CurlRequest();
        $request->setUrl($url);
         $request->setCurlOptions(array(CURLOPT_TIMEOUT=>1));
        $request->send();
        return json_decode($request->getResponseBody(), TRUE);
    }
    private static function _post ($url, array &$post = array())
    {
        $request = new CAS_CurlRequest();
        $request->setUrl($url);
        $request->setCurlOptions(array(CURLOPT_TIMEOUT=>1));
        $request->makePost();
        if (! isset($post['code']) || empty($post['code'])) {
            $post['code'] = md5(http_build_query($post));
        }
//        Afx_Debug_Helper::print_r(http_build_query($post));
        $request->setPostBody(http_build_query($post));
        $request->send();
        return json_decode($request->getResponseBody(), TRUE);
    }
    public static function service ($arr, array $post = array())
    {
        if (count($arr) <= 0)
            return;
        switch (strtoupper($arr[1])) {
            case 'GET':
                return self::_get($arr[0]);
                break;
            case 'POST':
                return self::_post($arr[0], $post);
                break;
        }
    }
}