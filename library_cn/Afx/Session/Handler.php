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
 * @package Afx_Session
 * @version $Id Handler.php
 * The Session Handler Class Wrapper Provider Seperator Read And Write
 * @author Afx team && firedtoad@gmail.com &&dietoad@gmail.com
 * this class is not work properly
 */
class Afx_Session_Handler
{
    /**
     * @var Afx_Db_Memcache
     */
    private static $_memcache = NULL;
    /**
     * @var Afx_Session_Handler
     */
    private static $_instance = NULL;
    private $path;
    private $id;
    private function __construct ()
    {
        self::$_memcache = Afx_Db_Memcache::Instance();
    }
    public static function Instance ()
    {
        if (self::$_instance === NULL) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    public static function read ($id)
    {
        echo "read\n";
        if ($id)
            return self::$_memcache->get($id);
    }
    public static function open ()
    {
        echo "open\n";
        return TRUE;
    }
    public static function close ()
    {
        echo "close\n";
        return (TRUE);
    }
    public static function write ($id, $data)
    {
        echo "write\n";
        //          print_r(func_get_args());
        if ($id)
            return self::$_memcache->set($id, $data);
    }
    public static function destroy ($id)
    {
        //        echo "destroy\n";
        if ($id)
            return self::$_memcache->delete($id);
    }
    public static function gc ()
    {
        //        echo "gc\n";
    }
    public function __call ($m, $arg)
    {}
}