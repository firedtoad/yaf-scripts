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
 * @package Afx_Db
 * @version $Id Redis.php
 * The Redis Class wrapper Redis class
 * @author Afx TEAM && firedtoad@gmail.com && dietoad@gmail.com
 * Notice that this class is not implement
 */
class Afx_Cache_Redis_Adapter implements Afx_Cache_Adapter
{
    const REDIS_NOT_FOUND = 0;
    const REDIS_STRING = 1;
    const REDIS_SET = 2;
    const REDIS_LIST = 3;
    const REDIS_ZSET = 4;
    const REDIS_HASH = 5;
    const ATOMIC = 0;
    const MULTI = 1;
    const PIPELINE = 2;
    const OPT_SERIALIZER = 1;
    const OPT_PREFIX = 2;
    const SERIALIZER_NONE = 0;
    const SERIALIZER_PHP = 1;
    const SERIALIZER_IGBINARY = 2;
    const AFTER = 'after';
    const BEFORE = 'before';

    /**
     * @var array
     */
    protected $__config = array();

    /**
     *
     * @var Afx_Cache_Redis_Adapter
     */
    protected static $__instance = NULL;

    /**
     * the real redis client object
     * @var Redis
     */
    private $__redis;

    private $__host;

    private $__port;

    private $__timeout;

    private $__persist;

    private $__cache = array();
    /**
     * @var boolean
     */
    private static $__debug;
    /**
     * @var boolean
     */
    private static $__local_cache=TRUE;
    
    /**
     * set debug on|off
     * @param boolean $bool
     */
    public static function debug ($bool)
    {
        self::$__debug = $bool;
    }
    public static function set_local_debug($local_cache)
    {
        self::$__local_cache=$local_cache;
    }
    /**
     */
    public function add ($key, $value, $expire = 3600)
    {
        $v = $value;
        if (is_array($value) || is_object($value))
        {
            $value = serialize($value);
        }

        $ret = $this->__redis->setex($key, $expire, $value);
        if ($ret)
        {
            self::$__local_cache&&$this->__cache[$key] = $v;
        }
        return $ret;
    }

    /**
     */
    public function decrement ($key, $value = 1)
    {
        $val = $this->__redis->get($key);
        return $this->__redis->set($key, $val + $value);
    }

    /**
     */
    public function increment ($key, $value)
    {
        $val = $this->__redis->get($key);
        return $this->__redis->set($key, $val - $value);
    }

    /**
     * @return the $__config
     */
    public function getOptions ()
    {
        return $this->__config;
    }

    /**
     * @param array $__config
     */
    public function setConfig ($__config)
    {
        $this->__config = $__config;
        $this->__host = $__config['host'];
        $this->__port = $__config['port'];
        $this->__timeout = $__config['timeout'];
        $this->__persist = $__config['persist'];
    }

    private function __construct ()
    {
    }

    public function init ()
    {
        $this->__redis = new Redis();
        $this->__persist ? $this->pconnect() : $this->connect();
         //        ReflectionClass::export($redis);
    //        $redis->addServer();
    }

    public static function Instance ($create = FALSE)
    {
        if ($create)
        {
            return new self();
        }
        if (! self::$__instance instanceof Afx_Cache_Redis_Adapter)
        {
            self::$__instance = new self();
        }
        return self::$__instance;
    }

    public function connect ()
    {
        //        ReflectionParameter::export('redis::connect', 1);
        //        ReflectionMethod::export('redis', 'connect');
        //        echo $this->__host.$this->__port;
        //        ReflectionParameter::export($function, $parameter)
        $this->__redis->connect($this->__host, $this->__port, $this->__timeout);
    }

    public function pconnect ()
    {
        $this->__redis->pconnect($this->__host, $this->__port, $this->__timeout);
    }

    public function close ()
    {
        return $this->__redis->close();
    }

    public function ping ()
    {
        return $this->__redis->ping();
    }

    public function get ($key)
    {
        $value=NULL;
        if(isset($this->__cache[$key]))
        {
            $value=$this->__cache[$key];
        }
        !$value&&$value = $this->__redis->get($key);
        if (gettype($value) == 'string')
        {
            $value = unserialize($value);
        }
        self::$__local_cache&&$this->__cache[$key] = $value;
        return $value;
    }
    
    public function length($key)
    {
        return $this->__redis->lLen($key);
    }
    
    public function remove($key, $value, $count)
    {
        $ret=FALSE;
        if($key&&$value&&$count>=1)
        {
            $ret=$this->__redis->lRem($key, $value, $count);
        }
        return $ret;
    }
    
    public function set ($k, $v, $expire = 3600)
    {
        if (is_array($v) || is_object($v))
        {
            $v1 = serialize($v);
        }
        $ret = $this->__redis->set($k, $v1, $expire);
        if ($ret)
        {
            self::$__local_cache&&$this->__cache[$k] = $v;
        }
        return $ret;
    }

    public function setex ($k, $ttl, $value)
    {
        $old_cache = isset($this->__cache[$k]) ? $this->__cache[$k] : array();
        $value = Afx_Common::cachePreUpdate($value, $old_cache);
        $ret = TRUE;
        if (is_array($value) || is_object($value))
        {
            $value1 = serialize($value);
        } else
        {
            $ret = FALSE;
        }
        $ret && $ret = $this->__redis->setex($k, $ttl, $value1);
        if ($ret)
        {
            self::$__local_cache&&$this->__cache[$k] = $value;
            $ret=TRUE;
        }
        return $ret;
    }

    public function replace ($key, $value, $expire = 3600)
    {
        $old_cache = isset($this->__cache[$key]) ? $this->__cache[$key] : array();
        $value = Afx_Common::cachePreUpdate($value, $old_cache);
        $ret = TRUE;
        if (is_array($value) || is_object($value))
        {
            $value1 = serialize($value);
        } else
        {
            $ret = FALSE;
        }
        $ret && $ret = $this->__redis->getSet($key, $value1);
        if ($ret)
        {
            $this->__redis->expire($key, $expire);
            self::$__local_cache&&$this->__cache[$key] = $value;
            $ret=TRUE;
        }
        
        return $ret;
    }

    public function setnx ($k, $ttl = 3600, $v)
    {
        $old_cache = isset($this->__cache[$k]) ? $this->__cache[$k] : array();
        $value = Afx_Common::cachePreUpdate($value, $old_cache);
        $ret = TRUE;
        if (is_array($v) || is_object($v))
        {
            $v = serialize($v);
        }else{
            $ret = FALSE;
        }
        $ret&&$ret = $this->__redis->setex($k, $ttl, $v);
        if ($ret)
        {
            self::$__local_cache&&$this->__cache[$k] = $value;
            $ret=TRUE;
        }
        return $ret;
    }

    public function getSet ($k, $v)
    {
        if (is_array($v) || is_object($v))
        {
            $v = serialize($v);
        }
        return $this->__redis->getSet($k, $v);
    }

    public function randomKey ()
    {
        return $this->__redis->randomKey();
    }

    public function renameKey ($key, $newname)
    {
        return $this->__redis->randomKey($key, $newname);
    }

    public function renameNx ($key, $newname)
    {
        return $this->__redis->renameNx($key, $newname);
    }

    public function getMultiple ($keys = array())
    {
        return $this->__redis->getMultiple($keys);
    }

    public function exists ($key)
    {
        return $this->__redis->exists($key);
    }

    public function delete ($key, $expire = 0)
    {
        return $this->__redis->delete($key, $expire);
    }

    public function incr ($key)
    {
        return $this->__redis->incr($key);
    }

    public function incrBy ($key, $value)
    {
        return $this->__redis->incrBy($key, $value);
    }

    public function decr ($key)
    {
        return $this->__redis->decr($key);
    }

    public function decrBy ($key, $value)
    {
        return $this->__redis->incrBy($key, $value);
    }

    public function type ($key)
    {
        return $this->__redis->type($key);
    }

    public function append ($key, $value)
    {
        return $this->__redis->append($key, $value);
    }

    public function getRange ($key, $start, $end)
    {
        return $this->__redis->getRange($key, $start, $end);
    }

    public function setRange ($key, $offset, $value)
    {
        return $this->__redis->setRange($key, $offset, $value);
    }

    public function getBit ($key, $offset)
    {
        return $this->__redis->getBit($key, $offset);
    }

    public function setBit ($key, $offset, $bool)
    {
        return $this->__redis->setBit($key, $offset, $bool);
    }

    public function strlen ($key)
    {
        return $this->__redis->strlen($key);
    }

    public function getKeys ($pattern)
    {
        return $this->__redis->getKeys($pattern);
    }

    /**
     * @param string $key
     * @param array $option
     * 'by' => 'some_pattern_*',
     * 'limit' => array(0, 1),
     * 'get' => 'some_other_pattern_*' or an array of patterns,
     * 'sort' => 'asc' or 'desc',
     * 'alpha' => TRUE,
     * 'store' => 'external-key'
     * 
     */
    public function sort ($key, $option)
    {
        return $this->__redis->sort($key, $option);
    }

    public function sortAsc ($key)
    {
        return $this->__redis->sort($key, array(
            'sort'=>'asc'
        ));
    }

    public function sortAscAlpha ($key)
    {
        return $this->__redis->sort($key, array(
            'sort'=>'asc','alpha'=>TRUE
        ));
    }

    public function sortDesc ($key)
    {
        return $this->__redis->sort($key, array(
            'sort'=>'desc'
        ));
    }

    public function sortDescAlpha ($key)
    {
        return $this->__redis->sort($key, array(
            'sort'=>'desc','alpha'=>TRUE
        ));
    }

    public function lPush ($key, $value)
    {
        return $this->__redis->lPush($key, $value);
    }

    public function rPush ($key, $value)
    {
        return $this->__redis->rPush($key, $value);
    }

    public function lPushx ($key, $value)
    {
        return $this->__redis->lPushx($key, $value);
    }

    public function rPushx ($key, $value)
    {
        return $this->__redis->rPushx($key, $value);
    }

    public function lPop ($key)
    {
        return $this->__redis->lPop($key);
    }

    public function rPop ($key)
    {
        return $this->__redis->rPop($key);
    }

    public function blPop ()
    {
        return $this->__redis->blPop(func_get_args());
    }

    public function brPop ()
    {
        return $this->__redis->brPop(func_get_args());
    }

    public function lSize ($key)
    {
        return $this->__redis->lSize($key);
    }

    public function lRemove ($key, $value, $count = 0)
    {
        return $this->__redis->lRemove($key, $value, $count);
    }

    public function listTrim ($key, $value, $count)
    {
        return $this->__redis->listTrim($key, $value, $count);
    }

    public function lGet ($key, $index)
    {
        return $this->__redis->lGet($key, $index);
    }

    public function lGetRange ($key, $start, $end)
    {
        return $this->__redis->lGetRange($key, $start, $end);
    }

    public function lSet ($key, $index, $value)
    {
        return $this->__redis->lSet($key, $index, $value);
    }

    /**
     * @param string $key
     * @param string $pos  BEFORE|AFTER
     * @param string $pivot      
     * @param string $value
     */
    public function lInsert ($key, $pos, $pivot, $value)
    {
        return $this->__redis->lInsert($key, $pos, $pivot, $value);
    }

    /**
     * @param  $key,$value1,$value2,$value3
     */
    public function sAdd ()
    {
        return $this->__redis->sAdd(func_get_args());
    }

    public function sSize ($key)
    {
        return $this->__redis->sSize($key);
    }

    /**
     * @param  $key,$value1,$value2,$value3
     */
    public function sRemove ()
    {
        return $this->__redis->sRemove(func_get_args());
    }

    /**
     * @param string $ksrc
     * @param string $kdst
     * @param string $member
     */
    public function sMove ($ksrc, $kdst, $member)
    {
        return $this->__redis->sMove($ksrc, $kdst, $member);
    }

    public function sPop ($key)
    {
        return $this->__redis->sPop($key);
    }

    public function sRandMember ($key)
    {
        return $this->__redis->sRandMember($key);
    }

    public function sContains ($key, $member)
    {
        return $this->__redis->sContains($key, $member);
    }

    public function sMembers ($key)
    {
        return $this->__redis->sMembers($key);
    }

    /**
     * $key1,$key2,$key3
     */
    public function sInter ()
    {
        return $this->__redis->sInter(func_get_args());
    }

    /**
     * $dest_key,$key1,$key2,$key3
     */
    public function sInterStore ()
    {
        return $this->__redis->sInterStore(func_get_args());
    }

    /**
     * $key1,$key2,$key3
     */
    public function sUnion ()
    {
        return $this->__redis->sUnion(func_get_args());
    }

    /**
     * $dest_key,$key1,$key2,$key3
     */
    public function sUnionStore ()
    {
        return $this->__redis->sUnionStore(func_get_args());
    }

    /**
     * $key1,$key2,$key3
     */
    public function sDiff ()
    {
        return $this->__redis->sDiff(func_get_args());
    }

    /**
     * $dest_key,$key1,$key2,$key3
     */
    public function sDiffStore ()
    {
        return $this->__redis->sDiffStore(func_get_args());
    }

    public function setTimeout ($key, $ttl = 0)
    {
        return $this->__redis->setTimeout($key, $ttl);
    }

    public function save ()
    {
        return $this->__redis->save();
    }

    public function bgSave ()
    {
        return $this->__redis->bgSave();
    }

    public function lastSave ()
    {
        return $this->__redis->lastSave();
    }

    public function flushDB ()
    {
        return $this->__redis->flushDB();
    }

    public function flush ()
    {
        return $this->__redis->flushAll();
    }

    public function flushAll ()
    {
        return $this->__redis->flushAll();
    }

    public function dbSize ()
    {
        return $this->__redis->dbSize();
    }

    public function auth ($pass)
    {
        return $this->__redis->auth($pass);
    }

    public function ttl ($key)
    {
        return $this->__redis->ttl($key);
    }

    public function persist ($key)
    {
        return $this->__redis->persist($key);
    }

    public function info ()
    {
        return $this->__redis->info();
    }

    public function resetStat ()
    {
        return $this->__redis->resetStat();
    }

    public function select ($index)
    {
        return $this->__redis->select($index);
    }

    public function move ($key, $index)
    {
        return $this->__redis->move($key, $index);
    }

    public function bgrewriteaof ()
    {
        return $this->__redis->bgrewriteaof();
    }

    /**
     */
    public function slaveof ($host, $port)
    {
        if ($host && $port)
        {
            return $this->__redis->slaveof($host, $port);
        }
        return $this->__redis->slaveof();
    }

    /**
     * @param string $info "encoding"|"refcount"|"idletime"
     * @param string $key
     */
    public function object ($info, $key)
    {
        return $this->__redis->object($info, $key);
    }

    /**
     * @param array $array must be an associative array 
     */
    public function mset ($array)
    {
        return $this->__redis->mset($array);
    }

    /**
     * @param array $array must be an associative array 
     * @return true if all the keys were set
     */
    public function msetnx ($array)
    {
        return $this->__redis->msetnx($array);
    }

    public function rpoplpush ($ksrc, $kdst)
    {
        return $this->__redis->rpoplpush($ksrc, $kdst);
    }

    public function brpoplpush ($ksrc, $kdst, $timeout)
    {
        return $this->__redis->brpoplpush($ksrc, $kdst, $timeout);
    }

    public function zAdd ($key, $score, $value)
    {
        return $this->__redis->zAdd($key, $score, $value);
    }

    public function zDelete ($key, $member)
    {
        return $this->__redis->zDelete($key, $member);
    }

    public function zRange ($key, $start, $end, $score = FALSE)
    {
        return $this->__redis->zRange($key, $start, $end, $score);
    }

    public function zReverseRange ($key, $start, $end, $score = FALSE)
    {
        return $this->__redis->zReverseRange($key, $start, $end, $score);
    }

    /**
     * @param string $key
     * @param int $start
     * @param int $end
     * @param array $options array(withscores => TRUE, limit => array($offset, $count))
     */
    public function zRangeByScore ($key, $start, $end, $options = array())
    {
        return $this->__redis->zRangeByScore($key, $start, $end, options);
    }

    /**
     * @param string $key
     * @param int $start
     * @param int $end
     * @param array $options array(withscores => TRUE, limit => array($offset, $count))
     */
    public function zRevRangeByScore ($key, $start, $end, $options = array())
    {
        return $this->__redis->zRevRangeByScore($key, $start, $end, options);
    }

    public function zCount ($key, $start, $end)
    {
        return $this->__redis->zCount($key, $start, $end);
    }

    /**
     * @param string $key
     * @param int $start can be 'inf' or '-inf'
     * @param int $end   can be 'inf' or '-inf'
     */
    public function zDeleteRangeByScore ($key, $start, $end)
    {
        return $this->__redis->zDeleteRangeByScore($key, $start, $end);
    }

    public function zDeleteRangeByRank ($key, $start, $end)
    {
        return $this->__redis->zDeleteRangeByRank($key, $start, $end);
    }

    public function zCard ($key)
    {
        return $this->__redis->zCard($key);
    }

    public function zScore ($key, $member)
    {
        return $this->__redis->zScore($key, $member);
    }

    public function zRank ($key, $member)
    {
        return $this->__redis->zRank($key, $member);
    }

    public function zRevRank ($key, $member)
    {
        return $this->__redis->zRevRank($key, $member);
    }

    /**
     * @param string $key_out
     * @param array $zkeys
     * @param array $weights
     * @param string $func_name min|max|sum
     */
    public function zInter ($key_out, $zkeys, $weights, $func_name)
    {
        return $this->__redis->zInter($key_out, $zkeys, $weights, $func_name);
    }

    /**
     * @param string $key_out
     * @param array $zkeys
     * @param array $weights
     * @param string $func_name min|max|sum
     */
    public function zUnion ($key_out, $zkeys, $weights, $func_name)
    {
        return $this->__redis->zUnion($key_out, $zkeys, $weights, $func_name);
    }

    public function zIncrBy ($key, $value, $member)
    {
        return $this->__redis->zIncrBy($key, $value, $member);
    }

    public function expireAt ($key, $expire)
    {
        return $this->__redis->expireAt($key, $expire);
    }

    public function hGet ($key, $hash_key)
    {
        return $this->__redis->hGet($key, $hash_key);
    }

    public function hSet ($key, $hash_key, $value)
    {
        return $this->__redis->hSet($key, $hash_key, $value);
    }

    public function hSetNx ($key, $hash_key, $value)
    {
        return $this->__redis->hSet($key, $hash_key, $value);
    }

    public function hDel ($key, $hash_key)
    {
        return $this->__redis->hDel($key, $hash_key);
    }

    public function hLen ($key)
    {
        return $this->__redis->hLen($key);
    }

    public function hKeys ($key)
    {
        return $this->__redis->hKeys($key);
    }

    public function hVals ($key)
    {
        return $this->__redis->hVals($key);
    }

    public function hGetAll ($key)
    {
        return $this->__redis->hGetAll($key);
    }

    public function hExists ($key, $member)
    {
        return $this->__redis->hGetAll($key, $member);
    }

    public function hIncrBy ($key, $member, $value)
    {
        return $this->__redis->hIncrBy($key, $member, $value);
    }

    public function hMset ($key, $array)
    {
        return $this->__redis->hMset($key, $array);
    }

    public function hMget ($key, $hkeys)
    {
        return $this->__redis->hMget($key, $hkeys);
    }

    /**
     *@param $multi Redis::MULTI
     */
    public function multi ($multi = 1)
    {
        return $this->__redis->multi($multi);
    }

    public function discard ()
    {
        return $this->__redis->discard();
    }

    public function exec ()
    {
        return $this->__redis->exec();
    }

    public function pipeline ()
    {
        return $this->__redis->pipeline();
    }

    /**
     * @param array $keys
     */
    public function watch ($keys)
    {
        return $this->__redis->watch($keys);
    }

    public function unwatch ($keys)
    {
        return $this->__redis->unwatch($keys);
    }

    public function publish ($channel, $message)
    {
        return $this->__redis->publish($channel, $message);
    }

    /**
     * @param array $channels
     * @param mixed $mixed
     */
    public function subscribe ($channels, $mixed)
    {
        return $this->__redis->subscribe($channels, $mixed);
    }

    public function unsubscribe ()
    {
        //        return $this->__redis->unsubscribe($channels,$mixed);
    }

    public function getOption ($name)
    {
        return $this->__redis->getOption($name);
    }

    public function setOption ($name, $value)
    {
        return $this->__redis->setOption($name, $value);
    }

    public function open ($host, $port, $timeout)
    {
        return $this->__redis->open($host, $port, $timeout);
    }

    public function popen ($host, $port, $timeout)
    {
        return $this->__redis->popen($host, $port, $timeout);
    }

    public function lLen ($key)
    {
        return $this->__redis->lLen($key);
    }

    public function sGetMembers ($key)
    {
        return $this->__redis->sGetMembers($key);
    }

    public function mget ($keys)
    {
        return $this->__redis->mget($$keys);
    }

    public function expire ($key, $ttl)
    {
        return $this->__redis->expire($key, $ttl);
    }

    public function zunionstore ()
    {
        //        return $this->__redis->zunionstore($key,$ttl);
    }

    public function zinterstore ()
    {
    }

    public function zRemove ($key, $member)
    {
        return $this->__redis->zRemove($key, $member);
    }

    public function zRem ($key, $member)
    {
        return $this->__redis->zRem($key, $member);
    }

    public function zRemoveRangeByScore ($key, $start, $end)
    {
        return $this->__redis->zRemoveRangeByScore($key, $start, $end);
    }

    public function zRemRangeByScore ($key, $start, $end)
    {
        return $this->__redis->zRemRangeByScore($key, $start, $end);
    }

    public function zRemRangeByRank ($key, $start, $end)
    {
        return $this->__redis->zRemRangeByRank($key, $start, $end);
    }

    public function zSize ($key)
    {
        return $this->__redis->zSize($key);
    }

    public function substr ($key, $start, $end)
    {
        return $this->__redis->substr($key, $start, $end);
    }

    public function rename ($ksrc, $kdst)
    {
        return $this->__redis->rename($ksrc, $kdst);
    }

    public function del ($key)
    {
        return $this->__redis->del($key);
    }

    public function keys ($pattern)
    {
        return $this->__redis->keys($pattern);
    }

    public function lrem ($key, $value, $count)
    {
        return $this->__redis->lrem($key, $value, $count);
    }

    public function ltrim ($key, $start, $stop)
    {
        return $this->__redis->ltrim($key, $start, $stop);
    }

    public function lindex ($key)
    {
        return $this->__redis->lindex($key);
    }

    public function lrange ($key, $start, $end)
    {
        return $this->__redis->lrange($key, $start, $end);
    }

    public function scard ($key)
    {
        return $this->__redis->scard($key);
    }

    public function srem ($key, $member)
    {
        return $this->__redis->srem($key, member);
    }

    public function sismember ($key, $member)
    {
        return $this->__redis->sismember($key, member);
    }

    public function zrevrange ($key, $start, $end, $score = false)
    {
        return $this->__redis->zrevrange($key, $start, $end, $score);
    }
}
    