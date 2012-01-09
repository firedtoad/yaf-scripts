<?php
/**
 * Check检测类
 * 版本:0.4.1
 * 作者:Lazy
 * Email:o0lazy0o at gmail dot com
 * Log:根据自己的需要添加了几个检测功能IP,DSN,身份证
 * 修复身份证的一个小小bug...判断x大小写的问题...
 * -___-!!
 */
class check
{
    /**
     * 构造函数
     * 默认:空,不做任何处理.
     */
    Function check ()
    {}
    /**
     * IsUsername函数:检测是否符合用户名格式
     * $Argv是要检测的用户名参数
     * $RegExp是要进行检测的正则语句
     * 返回值:符合用户名格式返回用户名,不是返回false
     */
    Function is_username ($Argv)
    {
        $RegExp = '/^[a-zA-Z0-9_]{3,16}$/'; //由大小写字母跟数字组成并且长度在3-16字符直接
        return preg_match($RegExp, $Argv) ? $Argv : false;
    }
    /**
     * IsMail函数:检测是否为正确的邮件格式
     * $Argv是要检测的用户名参数
     * $RegExp是要进行检测的正则语句
     * 返回值:是正确的邮件格式返回邮件,不是返回false
     */
    Function is_mail ($Argv)
    {
        $RegExp = '/^[a-z0-9][a-z\.0-9-_]+@[a-z0-9_-]+(?:\.[a-z]{0,3}\.[a-z]{0,2}|\.[a-z]{0,3}|\.[a-z]{0,2})$/i';
        return preg_match($RegExp, $Argv) ? $Argv : false;
    }
    /**
     * IsSmae函数:检测参数的值是否相同
     * $ArgvOne,$ArgvTwo是要进行检测的值
     * $Force是否进行增强行检测,是同时检测变量类型,默认为否
     * 返回值:相同返回true,不相同返回false
     */
    Function is_same ($ArgvOne, $ArgvTwo, $Force = false)
    {
        return $Force ? $ArgvOne === $ArgvTwo : $ArgvOne == $ArgvTwo;
    }
    /**
     * IsQQ函数:检测参数的值是否符合QQ号码的格式
     * $Argv是要检测的QQ参数
     * $RegExp是要进行检测的正则语句
     * 返回值:是正确的QQ号码返回QQ号码,不是返回false
     */
    Function is_qq ($Argv)
    {
        $RegExp = '/^[1-9][0-9]{5,11}$/';
        return preg_match($RegExp, $Argv) ? $Argv : false;
    }
    /**
     * IsMobile函数:检测参数的值是否为正确的中国手机号码格式
     * $Argv是要检测的手机号码参数
     * $RegExp是要进行检测的正则语句
     * 返回值:是正确的手机号码返回手机号码,不是返回false
     */
    Function is_mobile ($Argv)
    {
        $RegExp = '/^(?:13|15|18)[0-9]{9}$/';
        return preg_match($RegExp, $Argv) ? $Argv : false;
    }
    /**
     * IsTel函数:检测参数的值是否为正取的中国电话号码格式包括区号
     * $Argv是要检测的电话号码参数
     * $RegExp是要进行检测的正则语句
     * 返回值:是正确的电话号码返回电话号码,不是返回false
     */
    Function is_tel ($Argv)
    {
        $RegExp = '/[0-9]{3,4}-[0-9]{7,8}$/';
        return preg_match($RegExp, $Argv) ? $Argv : false;
    }
    /**
     * IsNickname函数:检测参数的值是否为正确的昵称格式(Beta)
     * $Argv是要检测的昵称格式参数
     * $RegExp是要进行检测的正则语句
     * 返回值:是正确的昵称格式返回昵称格式,不是返回false
     */
    Function is_nickname ($Argv)
    {
        $RegExp = '/^\s*$|^c:\\con\\con$|[%,\*\"\s\t\<\>\&\'\(\)]|\xA1\xA1|\xAC\xA3|^Guest|^\xD3\xCE\xBF\xCD|\xB9\x43\xAB\xC8/is'; //Copy From DZ
        return preg_match($RegExp, $Argv) ? false : $Argv;
    }
    /**
     * IsChinese函数:检测参数是否为中文
     * $Argv是要检测的字符内码范围
     * $RegExp是要进行检测的正则语句
     * 返回值:是返回参数,不是返回false
     */
    function is_chinese ($Argv, $Encoding = 'utf8')
    {
        $RegExp = $Encoding == 'utf8' ? '/^[\x{4e00}-\x{9fa5}]+$/u' : '/^([\x80-\xFF][\x80-\xFF])+$/';
        Return preg_match($RegExp, $Argv) ? $Argv : False;
    }
    /**
     * IsDSN函数:简单的检测是否符合DSN规格
     * DSN格式如下:scheme://username:password@hostname:port/path
     */
    function is_dsn ($Argv)
    {
        $RegExp = '/^(?:[a-z]{3,12}:\/\/[^:]+:[^@]+@[^:]+:[^\/]+\/[a-z_][a-z0-9_]+|[a-z]{5,12}:\/\/[^:]+:[^@]+@[^\/]+\/[a-z_][a-z0-9_]+|[a-z]{5,12}:\/\/[^@]+@[^:]+:[^\/]+\/[a-z_][a-z0-9_]+|[a-z]{5,12}:\/\/[^@]+@[^\/]+\/[a-z_][a-z0-9_]+)$/'; //有bug,不想修复 ^^!
        return preg_match($RegExp, $Argv) ? $Argv : false;
    }
    /**
     * 检测参数是否为数值
     */
    function is_number ($Argv)
    {
        return is_numeric($Argv) ? intval($Argv) : false;
    }
    /**
     * 检测参数是否为字母
     */
    function is_alpha ($Argv)
    {
        return ctype_alpha($Argv) ? $Argv : false;
    }
    /**
     * 检测参数长度是否超过一个值
     */
    function is_length ($Argv, $Size)
    {
        return (strlen($Argv) > $Size) ? false : $Argv;
    }
    /**
     * 简单的检测参数是否为IP
     */
    function is_ip ($IP)
    {
        $Result = ip2long($this->Server);
        return ($Result == - 1 || $Result == false) ? false : $IP;
    }
    /**
     * 检测参数是否为正确的身份证号码,只支持18位身份证,不做区域/年份/性别判断,只做检验.
     */
    function is_id ($ID)
    {
        $Weighting = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2,
        1);
        $Verify = array(1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2);
        //$Length = strlen($ID);
        $Sum = 0;
        $Regexp = '/[1-6][1-6][0-9]{15}[0-9x]/i';
        if (! preg_match($Regexp, $ID)) {
            return false;
        }
        $Last = substr($ID, 17, 1);
        for ($I = 0; $I < 17; $I ++) {
            $Sum = $Sum + intval(substr($ID, $I, 1)) * $Weighting[$I];
        }
        $Y = $Sum % 11;
        if (strtoupper($Last) != $Verify[$Y]) {
            return false;
        }
        return true;
    }
}
/*
作用：取得随机字符串
参数：
    1、(int)$length = 32 #随机字符长度，默认为32
    2、(int)$mode = 0 #随机字符类型，0为大小写英文和数字，1为数字，2为小写子木，3为大写字母，4为大小写字母，5为大写字母和数字，6为小写字母和数字
返回：取得的字符串
使用：
    $code = new activeCodeObj;
    $str = $code->getCode($length, $mode);
*/
class activeCodeObj
{
    function getCode ($length = 32, $mode = 0)
    {
        switch ($mode) {
            case '1':
                $str = '1234567890';
                break;
            case '2':
                $str = 'abcdefghijklmnopqrstuvwxyz';
                break;
            case '3':
                $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case '4':
                $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
                break;
            case '5':
                $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
                break;
            case '6':
                $str = 'abcdefghijklmnopqrstuvwxyz1234567890';
                break;
            default:
                $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
                break;
        }
        $result = '';
        $l = strlen($str);
        for ($i = 0; $i < $length; $i ++) {
            $num = rand(0, $l);
            $result .= $str[$num];
        }
        return $result;
    }
}
/*
作用：取得客户端信息
参数：
返回：指定的资料
使用：
    $code = new clientGetObj;
    1、浏览器：$str = $code->getBrowse();
    2、IP地址：$str = $code->getIP();
    4、操作系统：$str = $code->getOS();
*/
class clientGetObj
{
    function getBrowse ()
    {
        global $_SERVER;
        $Agent = $_SERVER['HTTP_USER_AGENT'];
        $browser = '';
        $browserver = '';
        $Browsers = array('Lynx', 'MOSAIC', 'AOL', 'Opera', 'JAVA', 'MacWeb',
        'WebExplorer', 'OmniWeb');
        for ($i = 0; $i <= 7; $i ++) {
            if (strpos($Agent, $Browsers[$i])) {
                $browser = $Browsers[$i];
                $browserver = '';
            }
        }
        if (ereg('Mozilla', $Agent) && ! ereg('MSIE', $Agent)) {
            $temp = explode('(', $Agent);
            $Part = $temp[0];
            $temp = explode('/', $Part);
            $browserver = $temp[1];
            $temp = explode(' ', $browserver);
            $browserver = $temp[0];
            $browserver = preg_replace('/([d.]+)/', '\1', $browserver);
            $browser = 'Netscape Navigator';
        }
        if (ereg('Mozilla', $Agent) && ereg('Opera', $Agent)) {
            $temp = explode('(', $Agent);
            $Part = $temp[1];
            $temp = explode(')', $Part);
            $browserver = $temp[1];
            $temp = explode(' ', $browserver);
            $browserver = $temp[2];
            $browserver = preg_replace('/([d.]+)/', '\1', $browserver);
            $browser = 'Opera';
        }
        if (ereg('Mozilla', $Agent) && ereg('MSIE', $Agent)) {
            $temp = explode('(', $Agent);
            $Part = $temp[1];
            $temp = explode(';', $Part);
            $Part = $temp[1];
            $temp = explode(' ', $Part);
            $browserver = $temp[2];
            $browserver = preg_replace('/([d.]+)/', '\1', $browserver);
            $browser = 'Internet Explorer';
        }
        if ($browser != '') {
            $browseinfo = $browser . ' ' . $browserver;
        } else {
            $browseinfo = false;
        }
        return $browseinfo;
    }
    function getIP ()
    {
        global $_SERVER;
        if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } else
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $ip = getenv('HTTP_X_FORWARDED_FOR');
            } else
                if (getenv('REMOTE_ADDR')) {
                    $ip = getenv('REMOTE_ADDR');
                } else {
                    $ip = $_SERVER['REMOTE_ADDR'];
                }
        return $ip;
    }
    function getOS ()
    {
        global $_SERVER;
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $os = false;
        if (eregi('win', $agent) && strpos($agent, '95')) {
            $os = 'Windows 95';
        } else
            if (eregi('win 9x', $agent) && strpos($agent, '4.90')) {
                $os = 'Windows ME';
            } else
                if (eregi('win', $agent) && ereg('98', $agent)) {
                    $os = 'Windows 98';
                } else
                    if (eregi('win', $agent) && eregi('nt 5.1', $agent)) {
                        $os = 'Windows XP';
                    } else
                        if (eregi('win', $agent) && eregi('nt 5', $agent)) {
                            $os = 'Windows 2000';
                        } else
                            if (eregi('win', $agent) && eregi('nt', $agent)) {
                                $os = 'Windows NT';
                            } else
                                if (eregi('win', $agent) && ereg('32', $agent)) {
                                    $os = 'Windows 32';
                                } else
                                    if (eregi('linux', $agent)) {
                                        $os = 'Linux';
                                    } else
                                        if (eregi('unix', $agent)) {
                                            $os = 'Unix';
                                        } else
                                            if (eregi('sun', $agent) &&
                                             eregi('os', $agent)) {
                                                $os = 'SunOS';
                                            } else
                                                if (eregi('ibm', $agent) &&
                                                 eregi('os', $agent)) {
                                                    $os = 'IBM OS/2';
                                                } else
                                                    if (eregi('Mac', $agent) &&
                                                     eregi('PC', $agent)) {
                                                        $os = 'Macintosh';
                                                    } else
                                                        if (eregi('PowerPC',
                                                        $agent)) {
                                                            $os = 'PowerPC';
                                                        } else
                                                            if (eregi('AIX',
                                                            $agent)) {
                                                                $os = 'AIX';
                                                            } else
                                                                if (eregi(
                                                                'HPUX', $agent)) {
                                                                    $os = 'HPUX';
                                                                } else
                                                                    if (eregi(
                                                                    'NetBSD',
                                                                    $agent)) {
                                                                        $os = 'NetBSD';
                                                                    } else
                                                                        if (eregi(
                                                                        'BSD',
                                                                        $agent)) {
                                                                            $os = 'BSD';
                                                                        } else
                                                                            if (ereg(
                                                                            'OSF1',
                                                                            $agent)) {
                                                                                $os = 'OSF1';
                                                                            } else
                                                                                if (ereg(
                                                                                'IRIX',
                                                                                $agent)) {
                                                                                    $os = 'IRIX';
                                                                                } else
                                                                                    if (eregi(
                                                                                    'FreeBSD',
                                                                                    $agent)) {
                                                                                        $os = 'FreeBSD';
                                                                                    } else
                                                                                        if (eregi(
                                                                                        'teleport',
                                                                                        $agent)) {
                                                                                            $os = 'teleport';
                                                                                        } else
                                                                                            if (eregi(
                                                                                            'flashget',
                                                                                            $agent)) {
                                                                                                $os = 'flashget';
                                                                                            } else
                                                                                                if (eregi(
                                                                                                'webzip',
                                                                                                $agent)) {
                                                                                                    $os = 'webzip';
                                                                                                } else
                                                                                                    if (eregi(
                                                                                                    'offline',
                                                                                                    $agent)) {
                                                                                                        $os = 'offline';
                                                                                                    } else {
                                                                                                        $os = 'Unknown';
                                                                                                    }
        return $os;
    }
}
class cnStrObj
{
    function substrGB ($str = '', $start = '', $len = '')
    {
        if ($start == 0 || $start == '') {
            $start = 1;
        }
        if ($str == '' || $len == '') {
            return false;
        }
        for ($i = 0; $i < $start + $len; $i ++) {
            $tmpstr = (ord($str[$i]) >= 161 && ord($str[$i]) <= 247 &&
             ord($str[$i + 1]) >= 161 && ord($str[$i + 1]) <= 254) ? $str[$i] .
             $str[++ $i] : $tmpstr = $str[$i];
            if ($i >= $start && $i < ($start + $len)) {
                $tmp .= $tmpstr;
            }
        }
        return $tmp;
    }
    function isGB ($str)
    {
        $strLen = strlen($str);
        $length = 1;
        for ($i = 0; $i < $strLen; $i ++) {
            $tmpstr = ord(substr($str, $i, 1));
            $tmpstr2 = ord(substr($str, $i + 1, 1));
            if (($tmpstr <= 161 || $tmpstr >= 247) &&
             ($tmpstr2 <= 161 || $tmpstr2 >= 247)) {
                $legalflag = false;
            } else {
                $legalflag = true;
            }
        }
        return $legalflag;
    }
}