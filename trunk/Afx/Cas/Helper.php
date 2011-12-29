<?php
class Afx_Cas_Helper
{
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