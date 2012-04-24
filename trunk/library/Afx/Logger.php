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
 * @package Afx
 * @version $Id Logger.php
 * The Logger Class record the operator
 * @author Afx team && firedtoad@gmail.com &&dietoad@gmail.com
 */
class Afx_Logger
{
    public static $_logpath;
    public static $_logfile = 'Afx_Log';
    public static $_logfilename = 'Afx_log';
    public static $_filenum = 0;
    //128M 一个文件
    public static $_log_size = 134217728;
    public static function cmp ($a, $b)
    {
        $a = array_pop(explode('/', $a));
        $b = array_pop(explode('/', $b));
        return substr($a, 7) - substr($b, 7);
    }
    public static function log ($msg = 'success')
    {
        $debug = debug_backtrace();
        $d_file=$file = $debug[0]['file'];
        $line = $debug[0]['line'];
        $type = $debug[0]['type'];
        $class = $debug[1]['class'];
        $function = $debug[1]['function'];
        $time = date('Y-m-d h:i:s', time());
        $d_file=str_ireplace('\\', '/', $d_file);
        $logmsg = $time . " " . $file . " " . $line . " " . $class . "" . $type .
         "" . $function . " " . $msg . "\n";
        if (! file_exists(self::$_logpath)) {
            mkdir(self::$_logpath, 0777, true);
        }
        $files = glob(self::$_logpath . '/*');
        usort($files, 'Afx_Logger::cmp');
        $file = array_pop($files);
        if ($file) {
            $temp=explode('/', $file);
            $file = array_pop($temp);
            self::$_logfile = $file;
            $mc = array();
            preg_match_all('/\d+/', $file, $mc);
            if (isset($mc[0][0]) && is_numeric($mc[0][0])) {
                self::$_filenum = $mc[0][0];
            }
        }

        if (file_exists(self::$_logpath . '/' . self::$_logfile))
            if (filesize(self::$_logpath . '/' . self::$_logfile) >
             self::$_log_size) {
                self::$_logfile = self::$_logfilename . ++ self::$_filenum;
            }
             //            echo filesize(self::$_logpath . '/' . self::$_logfile);
       //file_put_contents(self::$_logpath . '/' . self::$_logfile,$logmsg, FILE_APPEND);
        $point_log=PointLog::Instance();
        $point_log->file=$d_file;
        $point_log->line=$line;
        $point_log->class=$class;
        $point_log->function=$function;
        $point_log->msg=stripslashes($msg);
        //must set the debug flag to false
        //or it will be recursion
        $debug_flag=Afx_Db_Adapter::$debug;
        Afx_Db_Adapter::$debug=FALSE;
        $point_log->save();
        Afx_Db_Adapter::$debug=$debug_flag;

    }
}