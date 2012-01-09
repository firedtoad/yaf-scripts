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
        $file = $debug[0]['file'];
        $line = $debug[0]['line'];
        $type = $debug[0]['type'];
        $class = $debug[1]['class'];
        $function = $debug[1]['function'];
        $time = date('Y-m-d h:i:s', time());
        $logmsg = $time . " " . $file . " " . $line . " " . $class . "" . $type .
         "" . $function . " " . $msg . "\n";
        if (! file_exists(self::$_logpath)) {
            mkdir(self::$_logpath, 0777, true);
        }
        $files = glob(self::$_logpath . '/*');
        usort($files, 'Afx_Logger::cmp');
        $file = array_pop($files);
        if ($file) {
            $file = array_pop(explode('/', $file));
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
        file_put_contents(self::$_logpath . '/' . self::$_logfile,
        $logmsg, FILE_APPEND);
    }
}