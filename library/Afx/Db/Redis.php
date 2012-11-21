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
class Afx_Db_Redis
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
    protected static $_slaves = array();

    protected static $_slave_options = array();

    protected static $_slave_num = 0;

    /**
     * @var Redis
     */
    protected static $_master = NULL;

    protected static $_master_host;

    protected static $_master_port;

    protected static $_options = array();

    /**
     *
     * @var Afx_Db_Redis
     */
    protected static $_instance = NULL;

    /**
     * @return the Redis
     */
    public static function getSlave ()
    {
        if (self::$_slave_num > 0)
        {
            $server_num = rand(0, self::$_slave_num) % self::$_slave_num;
            return self::$_slaves[$server_num];
        }
    }

    /**
     * @return the $_master
     */
    public static function getMaster ()
    {
        return self::$_master;
    }

    /**
     * @return the $_options
     */
    public static function getOptions ()
    {
        return self::$_options;
    }

    /**
     * @param field_type $_options
     */
    public static function setOptions ($_options)
    {
        self::$_options = $_options;
    }

    private function __construct ()
    {
        $this->_init();
    }

    private function _init ()
    {
        
    }

    public static function Instance ()
    {
        if (! self::$_instance)
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function connect ()
    {
    }

    public function pconnect ()
    {
    }

    public function close ()
    {
    }

    public function ping ()
    {
    }

    public function get ($key)
    {
        return self::getSlave()->get($key);
    }

    public function set ($k, $v)
    {
        return self::getMaster()->set($k, $v);
    }

    public function setex ($k, $v)
    {
        return self::getMaster()->setex($k, $v);
    }

    public function setnx ($k, $v)
    {
        return self::getMaster()->setnx($k, $v);
    }

    public function getSet ($k, $v)
    {
        return self::getMaster()->getSet($k, $v);
    }

    public function randomKey ()
    {
        return self::getMaster()->randomKey();
    }

    public function renameKey ($key, $newname)
    {
        return self::getMaster()->renameKey($key, $newname);
    }

    public function renameNx ($key, $newname)
    {
        return self::getMaster()->renameNx($key, $newname);
    }

    public function getMultiple ()
    {
        $args = func_get_args();
    }

    public function exists ()
    {
    }

    public function delete ()
    {
    }

    public function incr ()
    {
    }

    public function incrBy ()
    {
    }

    public function decr ()
    {
    }

    public function decrBy ()
    {
    }

    public function type ()
    {
    }

    public function append ()
    {
    }

    public function getRange ()
    {
    }

    public function setRange ()
    {
    }

    public function getBit ()
    {
    }

    public function setBit ()
    {
    }

    public function strlen ()
    {
    }

    public function getKeys ()
    {
    }

    public function sort ()
    {
    }

    public function sortAsc ()
    {
    }

    public function sortAscAlpha ()
    {
    }

    public function sortDesc ()
    {
    }

    public function sortDescAlpha ()
    {
    }

    public function lPush ()
    {
    }

    public function rPush ()
    {
    }

    public function lPushx ()
    {
    }

    public function rPushx ()
    {
    }

    public function lPop ()
    {
    }

    public function rPop ()
    {
    }

    public function blPop ()
    {
    }

    public function brPop ()
    {
    }

    public function lSize ()
    {
    }

    public function lRemove ()
    {
    }

    public function listTrim ()
    {
    }

    public function lGet ()
    {
    }

    public function lGetRange ()
    {
    }

    public function lSet ()
    {
    }

    public function lInsert ()
    {
    }

    public function sAdd ()
    {
    }

    public function sSize ()
    {
    }

    public function sRemove ()
    {
    }

    public function sMove ()
    {
    }

    public function sPop ()
    {
    }

    public function sRandMember ()
    {
    }

    public function sContains ()
    {
    }

    public function sMembers ()
    {
    }

    public function sInter ()
    {
    }

    public function sInterStore ()
    {
    }

    public function sUnion ()
    {
    }

    public function sUnionStore ()
    {
    }

    public function sDiff ()
    {
    }

    public function sDiffStore ()
    {
    }

    public function setTimeout ()
    {
    }

    public function save ()
    {
    }

    public function bgSave ()
    {
    }

    public function lastSave ()
    {
    }

    public function flushDB ()
    {
    }

    public function flushAll ()
    {
    }

    public function dbSize ()
    {
    }

    public function auth ()
    {
    }

    public function ttl ()
    {
    }

    public function persist ()
    {
    }

    public function info ()
    {
    }

    public function resetStat ()
    {
    }

    public function select ()
    {
    }

    public function move ()
    {
    }

    public function bgrewriteaof ()
    {
    }

    public function slaveof ()
    {
    }

    public function object ()
    {
    }

    public function mset ()
    {
    }

    public function msetnx ()
    {
    }

    public function rpoplpush ()
    {
    }

    public function brpoplpush ()
    {
    }

    public function zAdd ()
    {
    }

    public function zDelete ()
    {
    }

    public function zRange ()
    {
    }

    public function zReverseRange ()
    {
    }

    public function zRangeByScore ()
    {
    }

    public function zRevRangeByScore ()
    {
    }

    public function zCount ()
    {
    }

    public function zDeleteRangeByScore ()
    {
    }

    public function zDeleteRangeByRank ()
    {
    }

    public function zCard ()
    {
    }

    public function zScore ()
    {
    }

    public function zRank ()
    {
    }

    public function zRevRank ()
    {
    }

    public function zInter ()
    {
    }

    public function zUnion ()
    {
    }

    public function zIncrBy ()
    {
    }

    public function expireAt ()
    {
    }

    public function hGet ()
    {
    }

    public function hSet ()
    {
    }

    public function hSetNx ()
    {
    }

    public function hDel ()
    {
    }

    public function hLen ()
    {
    }

    public function hKeys ()
    {
    }

    public function hVals ()
    {
    }

    public function hGetAll ()
    {
    }

    public function hExists ()
    {
    }

    public function hIncrBy ()
    {
    }

    public function hMset ()
    {
    }

    public function hMget ()
    {
    }

    public function multi ()
    {
    }

    public function discard ()
    {
    }

    public function exec ()
    {
    }

    public function pipeline ()
    {
    }

    public function watch ()
    {
    }

    public function unwatch ()
    {
    }

    public function publish ()
    {
    }

    public function subscribe ()
    {
    }

    public function unsubscribe ()
    {
    }

    public function getOption ()
    {
    }

    public function setOption ()
    {
    }

    public function open ()
    {
    }

    public function popen ()
    {
    }

    public function lLen ()
    {
    }

    public function sGetMembers ()
    {
    }

    public function mget ()
    {
    }

    public function expire ()
    {
    }

    public function zunionstore ()
    {
    }

    public function zinterstore ()
    {
    }

    public function zRemove ()
    {
    }

    public function zRem ()
    {
    }

    public function zRemoveRangeByScore ()
    {
    }

    public function zRemRangeByScore ()
    {
    }

    public function zRemRangeByRank ()
    {
    }

    public function zSize ()
    {
    }

    public function substr ()
    {
    }

    public function rename ()
    {
    }

    public function del ()
    {
    }

    public function keys ()
    {
    }

    public function lrem ()
    {
    }

    public function ltrim ()
    {
    }

    public function lindex ()
    {
    }

    public function lrange ()
    {
    }

    public function scard ()
    {
    }

    public function srem ()
    {
    }

    public function sismember ()
    {
    }

    public function zrevrange ()
    {
    }
}
