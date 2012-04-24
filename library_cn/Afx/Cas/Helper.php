<?php
/**
 * AFX FRAMEWORK
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
 * OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
 * NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
 * THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * @copyright Copyright (c) 2012 BVISON INC.  (http://www.bvison.com)
 */
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
    /**
     * 初始化CAS客户端 
     */
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
    /**
     * 初始化cas代理 
     */
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