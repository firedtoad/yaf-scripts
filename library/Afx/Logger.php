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
    //    public static $_log_size = 1024;
    public static $_log_size = 134217728;

    public static function cmp ($a, $b)
    {
        $ra = explode('/', $a);
        $rb = explode('/', $b);
        $a = array_pop($ra);
        $b = array_pop($rb);
        return substr($a, 7) - substr($b, 7);
    }

    public static function log ($msg = 'success', $level = E_ERROR, $no_trace = FALSE)
    {
        $debug = debug_backtrace();
        $time = date('Y-m-d h:i:s', time());
        $logmsg = array();
        $logmsg[]=str_repeat('=', 80)."\n";
        unset($debug[0]);
        foreach ($debug as $value)
        {
            $d_file = $file = isset($value['file']) ? $value['file'] : '';
            $line = isset($value['line']) ? $value['line'] : '';
            $type = $value['type'];
            $class = $value['class'];
            $function = $value['function'];
            $d_file = str_ireplace('\\', '/', $d_file);
            $logmsg[] = $time . "->" . $file . "->" . $class . $type . "" . $function . '--' . $line . "\n";
        }
        $logmsg[] = $msg;
        if (! file_exists(self::$_logpath))
        {
            mkdir(self::$_logpath, 0777, true);
        }
        $files = glob(self::$_logpath . '/*');
        usort($files, 'Afx_Logger::cmp');
        $file = array_pop($files);
        if ($file)
        {
            $temp = explode('/', $file);
            $file = array_pop($temp);
            self::$_logfile = basename($file);
            $mc = array();
            preg_match_all('/\d+/', $file, $mc);
            if (isset($mc[0][0]) && is_numeric($mc[0][0]))
            {
                self::$_filenum = $mc[0][0];
            }
        }
        if (file_exists(self::$_logpath . '/' . self::$_logfile)) if (filesize(self::$_logpath . '/' . self::$_logfile) > self::$_log_size)
        {
            self::$_logfile = self::$_logfilename . ++ self::$_filenum;
        }
        $logmsg[]="\n".str_repeat('=', 80)."\n";
        file_put_contents(self::$_logpath . '/' . self::$_logfile, join($logmsg, "") . "\n", FILE_APPEND | LOCK_EX);
    }
}