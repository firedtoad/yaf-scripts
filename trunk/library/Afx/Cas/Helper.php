<?php
/**
 *
 * Afx cas 封装
 * @author firedtoad
 *
 */
class Afx_Cas_Helper
{
    public static $CASSERVER='';
    public static $CASURL='';
    public static $CASLOGINURL='';
    public static $CASLOGOUTURL='';
    public static $INITLOCK=FALSE;
    public static function initCAS ()
    {   if(self::$INITLOCK==TRUE)return TRUE;
        require_once 'application/library/CAS.php';
        phpCAS::setDebug();
        phpCAS::client(CAS_VERSION_2_0,self::$CASSERVER, 443, self::$CASURL, false);
        phpCAS::setNoCasServerValidation();

//        if (isset($_REQUEST['ticket'])) {
//            $old_session = $_SESSION;
//            session_destroy();
//            $session_id = preg_replace('/[^\w]/', '', $_REQUEST['ticket']);
//            session_id($session_id);
//            setcookie('bg_sid', session_id(), time() + 1200, ('/store'),
//            ('bgstore.com'));
//            session_start();
//            $_SESSION = $old_session;
//        }
//        phpCAS::setCASSession_start();
        phpCAS::setServerLoginURL(self::$CASLOGINURL);
        phpCAS::setServerLogoutURL(self::$CASLOGOUTURL);
        self::$INITLOCK=TRUE;
    }
    public static function initProxy ()
    {
        require_once 'application/library/CAS.php';
//        if (isset($_REQUEST['ticket'])) {
//            $old_session = $_SESSION;
//            session_destroy();
//            $session_id = preg_replace('/[^\w]/', '', $_REQUEST['ticket']);
//            session_id($session_id);
//            setcookie('bg_sid', session_id(), time() + 1200, ('/store'),
//            ('bgstore.com'));
//            session_start();
//            $_SESSION = $old_session;
//        }
        phpCAS::setServerLoginURL(self::$CASLOGINURL);
        phpCAS::setServerLogoutURL(self::$CASLOGOUTURL);
    }
}