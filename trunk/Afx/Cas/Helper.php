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
 * @package Afx_Cas
 * @version $Id Helper.php
 * The Pdo Class Adapter Provider Communication With The RelationShip Database
 * @author Afx team && firedtoad@gmail.com &&dietoad@gmail.com
 */
class Afx_Cas_Helper
{
    /**
     * 初始化Cas 客户端
     * initialize Cas Client
     */
    public static function initCAS ()
    {
        require_once 'application/library/CAS.php';
        phpCAS::setDebug();
        phpCAS::client(CAS_VERSION_2_0, 'www.test.com', 443, 'CASServer', false);
        phpCAS::setNoCasServerValidation();
        if (isset($_REQUEST['ticket'])) {
            $old_session = $_SESSION;
            session_destroy();
            $session_id = preg_replace('/[^\w]/', '', $_REQUEST['ticket']);
            session_id($session_id);
            setcookie('bg_sid', session_id(), time() + 1200, ('/store'),
            ('bgstore.com'));
            session_start();
            $_SESSION = $old_session;
        }
        phpCAS::setServerLoginURL(
        'https://www.test.com/CASServer/login?service=http://pstore/Index/Index/Login');
        phpCAS::setServerLogoutURL(
        'https://www.test.com/CASServer/logout?service=http://pstore/Index/Index/');
    }
    /**
     * 初始化Cas 代理
     * initialieze Cas Proxy
     */
    public static function initProxy ()
    {
        require_once 'application/library/CAS.php';
        if (isset($_REQUEST['ticket'])) {
            $old_session = $_SESSION;
            session_destroy();
            $session_id = preg_replace('/[^\w]/', '', $_REQUEST['ticket']);
            session_id($session_id);
            setcookie('bg_sid', session_id(), time() + 1200, ('/store'),
            ('bgstore.com'));
            session_start();
            $_SESSION = $old_session;
        }
        phpCAS::setServerLoginURL(
        'https://www.test.com/CASServer/login?service=http://pstore/Index/Index/Login');
        phpCAS::setServerLogoutURL(
        'https://www.test.com/CASServer/logout?service=http://pstore/Index/Index/');
         //		phpCAS::proxy(CAS_VERSION_2_0, 'www.test.com', 443, 'CASServer',FALSE);
    }
}