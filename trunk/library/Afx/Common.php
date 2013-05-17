<?php
class Afx_Common
{
    
    /**
     * 加密解密函数
     * @param string $str  明文
     * @param string $key  密钥 必须选择中文字符串
     * @param int $factor  干扰因子
     * @return string
     */
    public static function EncOrDec ($str, $key,$factor)
    {
        $str = str_split($str);
        $len = count($str);
        $klen = strlen($key);
        for ($i = 0; $i < $len; $i ++)
        {
            $str[$i]^=$key[($i+$factor)%$klen];
        }
        $str = implode($str);
        return $str;
    }

    /**
     * 检查数组是否为标准二维数组
     * @param array $a
     * @return boolean
     */
    public static function isStandard2DArray ($a = array())
    {
        $count = count($a);
        if ($count > 0)
        {
            $first = current($a);
            $count_all = count($a, COUNT_RECURSIVE);
            $first_len = count($first);
            return $count_all - ($count * $first_len + $count) == 0;
        }
        return FALSE;
    }

    /**
     * 缓存更新前对比版本号
     * @param array $new_cache
     * @param array $old_cache
     * @return array
     */
    public static function cachePreUpdate ($new_cache = array(), $old_cache = array())
    {
        $modify = FALSE;
        $add = FALSE;
        if (is_array($new_cache))
        {
            if (self::__two_demision($new_cache))
            {
                foreach ($new_cache as $k => &$value)
                {
                    if (isset($old_cache[$k]))
                    {
                        /**
                         * @notice 以旧数据做蓝本会忽略旧数据中不存在的键值
                         */
                        $change = array_diff_assoc($value, $old_cache[$k]);
                        if (count($change))
                        {
                            $modify = TRUE;
                            $value['current_version'] = isset($value['current_version']) ? ++ $value['current_version'] : $value['ver'] + 1;
                        }
                    } else
                    {
                        $value['ver'] = 0;
                        $value['current_version'] = 1;
                        $add = TRUE;
                    }
                }
            } else
            {
                //                $value['current_version'] = isset($value['current_version']) ? ++ $value['current_version'] : 1;
                /**
                 * @notice 以旧数据做蓝本会忽略旧数据中不存在的键值
                 */
                $change = array_diff_assoc($new_cache, $old_cache);
                if (count($change) && isset($new_cache['ver']))
                {
                    $modify = TRUE;
                    $new_cache['current_version'] = isset($new_cache['current_version']) ? ++ $new_cache['current_version'] : $new_cache['ver'] + 1;
                }
            }
        }
        if ($modify == FALSE && $add = FALSE)
        {
            $new_cache = NULL;
        }
        return $new_cache;
    }

    /**
     * 数据库更新前过滤掉未更新过的字段
     * @param array $new_data
     * @return array
     */
    public static function dbPreUpdate ($new_data = array())
    {
        foreach ($new_data as $k => &$value)
        {
            if (is_array($value) && count($value))
            {
               
                if (static::__two_demision($value))
                {

                    foreach ($value as $k1 => &$value1)
                    {
                        $k1 !== 'current_version' && $value1['ver'] = isset($value1['ver']) ? $value1['ver'] : 0;
                         if($value1['ver']==0)
                         {
                            $value1['current_version']=1;
                         }
                        if (!isset($value1['current_version'])||(isset($value1['current_version']) && $value1['current_version'] == $value1['ver']))
                        {
                            unset($value[$k1]);
                        } else 
                            if (isset($value1['current_version']) && $value1['current_version'] != $value1['ver'])
                            {
                                $value1['ver'] = $value1['current_version'];
                                unset($value1['current_version']);
                            }
                    }
                } else
                {
                    $value['ver'] = isset($value['ver']) ? $value['ver'] : 0;
                    if($value['ver']==0)
                    {
                        $value['current_version']=1;
                    }
                    if (! isset($value['current_version']) || (isset($value['current_version']) && $value['current_version'] == $value['ver']))
                    {
                        unset($new_data[$k]);
                    } else if (isset($value['current_version']) && $value['current_version'] != $value['ver'])
                    {
                        $value['ver'] = $value['current_version'];
                        unset($value['current_version']);
                    }
                }
            }
            if (! count($value))
            {
                unset($new_data[$k]);
            }
        }
        return $new_data;
    }

    /**
     * 检查是否二维数组
     * @param array $array
     */
    private static function __two_demision ($array = array())
    {
        $ret = FALSE;
        if (count($array))
        {
            $key = current($array);
            $ret = is_array($key);
        }
        return $ret;
    }

    /**
     * 获取缓存数据添加版本号
     * @param array $cache
     */
    public static function cacheAfterGet ($cache = array())
    {
        if (! isset($cache['version']))
        {
            $cache['version'] = 0;
        }
        return $cache;
    }

    /**
     * 通用返回接口内部实现
     * @param array $info
     * @param array $oldinfo
     */
    public static function __response ($info = array(), $oldinfo = array())
    {
        static $array = array(
            'changed'=>array(),'deleted'=>array(),'add'=>array()
        );
        if (count($info) && count($oldinfo))
        {
            $infokeys = array_keys($info);
            $oldkeys = array_keys($oldinfo);
            $diffkeys = array_diff_assoc($oldkeys, $infokeys);
            //键相同才比较
            if (! count($diffkeys))
            {
                foreach ($info as $k => $v)
                {
                    if (self::__two_demision($v))
                    {
                        foreach ($v as $k1 => $v1)
                        {
                            if (! isset($oldinfo[$k][$k1]))
                            {
                                ! isset($array['add'][$k]) && $array['add'][$k] = array();
                                $array['add'][$k][$k1] = $v1;
                                continue;
                            }
                            $diff = array_diff_assoc($v1, $oldinfo[$k][$k1]);
                            if (count($diff))
                            {
                                if (isset($v1['delete']) && $v1['delete'] == 1)
                                {
                                    ! isset($array['deleted'][$k]) && $array['deleted'][$k] = array();
                                    $array['deleted'][$k][$k1] = $v1;
                                } else
                                {
                                    ! isset($array['changed'][$k]) && $array['changed'][$k] = array();
                                    $array['changed'][$k][$k1] = $v1;
                                }
                            }
                        }
                    } else
                    {
                        if (! isset($oldinfo[$k]))
                        {
                            ! isset($array['add'][$k]) && $array['add'][$k] = array();
                            $array['add'][$k][$k1] = $v1;
                            continue;
                        }
                        $diff = array_diff_assoc($v, $oldinfo[$k]);
                        if (count($diff))
                        {
                            if (isset($v['delete']) && $v['delete'] == 1)
                            {
                                ! isset($array['deleted'][$k]) && $array['deleted'][$k] = array();
                                $array['deleted'][$k] = $v;
                            } else
                            {
                                ! isset($array['changed'][$k]) && $array['changed'][$k] = array();
                                $array['changed'][$k] = $v;
                            }
                        }
                    }
                }
            }
        }
        return $array;
    }

    /**
     * 通用返回接口 计算数据差异返回修改删除过的部分
     * 示例数据
     * Array
        (
            [success] => 1 //是否成功
            [data] => Array
                (
                    [main] => Array  //本次请求数据
                        (
                            [a] => 1
                        )
                    [addition] => Array //附加数据
                        (
                            [changed] => Array //修改过的数据
                                (
                                    [group] => Array//功能模块
                                        (
                                            [1] => Array
                                                (
                                                    [group_id] => 3
                                                    [role_id] => 1
                                                    [position] => 2
                                                    [is_leader] => 0
                                                    [version] => 0
                                                    [delete] => 0
                                                )
                                        )
                                )
                            [deleted] => Array //删除的数据
                                (
                                    [group] => Array//功能模块
                                        (
                                            [0] => Array
                                                (
                                                    [group_id] => 2
                                                    [role_id] => 1
                                                    [position] => 1
                                                    [is_leader] => 0
                                                    [version] => 0
                                                    [delete] => 1
                                                )
                                        )
                                )
                            [add] => Array//增加的数据
                                (
                                    [group] => Array//功能模块
                                        (
                                            [2] => Array
                                                (
                                                    [group_id] => 2
                                                    [role_id] => 1
                                                    [position] => 1
                                                    [is_leader] => 0
                                                    [version] => 0
                                                    [delete] => 0
                                                )
                                        )
                                )
                        )
                )
            [message] => ok
        )
     * @param boolean $success
     * @param string $message
     * @param array $info
     * @param array $oldinfo
     * @return array
     */
    public static function response ($success, $message, $main, $info = array(), $oldinfo = array())
    {
        $array = array(
            'success'=>$success,'data'=>array(),'message'=>$message
        );
        if ($success)
        {
            $array['data']['main'] = $main; //本次请求返回数据
            $array['data']['addition'] = self::__response($info, $oldinfo); //计算数据差异
        }
        return $array;
    }

    public static function dbArraytoXml ($data, $tname, $encoding = 'UTF-8')
    {
        $xml_header = sprintf('<?xml version="1.0" encoding="%s"?>' . "\n<%s>\n", $encoding, $tname);
        $xml = $xml_header;
        if (self::isStandard2DArray($data))
        {
            
            foreach ($data as $value)
            {
                $str='<item ';
                foreach ($value as $k=>$v) {
                    $str.=$k.'="'.$v.'" ';
                }
                $str.="/>\n";
                $xml.=$str;
            }
        }
        $xml .= sprintf('</%s>', $tname);
        return $xml;
    }
}