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
    /**
     * get 调用
     * @param string $url
     */
    private static function _get ($url)
    {
        $request = new CAS_CurlRequest();
        $request->setUrl($url);
         $request->setCurlOptions(array(CURLOPT_TIMEOUT=>1));
        $request->send();
        return json_decode($request->getResponseBody(), TRUE);
    }
    /**
     * post 调用
     * @param string $url
     */
    private static function _post ($url, array &$post = array())
    {
         Afx_Cas_Helper::initCAS();
        $request = new CAS_CurlRequest();
        $request->setUrl($url);
        $request->setCurlOptions(array(CURLOPT_TIMEOUT=>1,CURLOPT_USERAGENT=>'bgpstore'));
        $request->makePost();
        $req_str='';
//        $last=array_pop($post);
//        array_push($post, $last);
        if(is_array($post)){
            $keys=array_keys($post);
            $len=count($keys);
            foreach ($post as $k=>$v){
               if($k==='comment')continue;
               if($v===$post[$keys[$len-1]]){
                   $req_str.=$k."=".($v);
                   continue;
               }
               $req_str.=$k."=".($v)."&";
            }
        }
//        $req_str=urldecode($req_str);
        //$request->setPostBody(http_build_query($post));
        if (! isset($post['code']) || empty($post['code'])) {
            $req_str.='&code='.$post['code'] = md5(($req_str));
        }
       // echo $req_str;
//        Afx_Debug_Helper::print_r(($req_str));
//        Afx_Debug_Helper::print_r((http_build_query($post)));
        $request->setPostBody(http_build_query($post));
        $request->send();
//        Afx_Debug_Helper::print_r($request);
//echo $url;
//        Afx_Debug_Helper::print_r( $request->getResponseHeaders());
//

        if($request->getErrorMessage()){
           Afx_Logger::log(
           'CAS 请求失败 返回头'.$request->getResponseHeaders().
           '错误信息'.$request->getErrorMessage()
            );
        }
      //  Afx_Debug_Helper::print_r( $request->getResponseBody());
        $ret=json_decode($request->getResponseBody(), TRUE);
        if($ret)
        {
            return $ret;
        }else{
            return $request->getResponseBody();
        }
    }
    /**
     *  服务接口
     * @param array $arr
     * @param array $post
     */
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