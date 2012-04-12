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
 * @version $Id Memcache.php
 * The Memcache Class Wrapper Provider Seperator Read And Write
 * @author Afx team && firedtoad@gmail.com &&dietoad@gmail.com
 *
 */
class Afx_Db_Memcache
{
    /**
     * 单例保存类本身
     * @var Afx_Db_Memcache
     */
    protected static $instance;
    /**
     * 保存memcache 配置
     * @var array store the configurations
     */
    protected static $options = array();
    /**
     * 保存从库链接
     * @var Memcache The slave Link
     */
    protected static $read_cache = array();
    protected $slave_options = array();
    protected static $slave_num = 0;
    /**
     * 主库链接
     * @var Memcache The Master Link
     */
    protected static $write_cache;
    /**
     * 私有构造函数
     * 初始化memcache链接
     * Notice! This is really protected
     * so this class was prevented be instance
     * by call global new Method
     */
    protected function __construct ()
    {
        $this->_initConnection();
    }
    /**
     * 设置memcache配置
     * set the configuration
     * @param array $options
     * @return Boolean
     */
    public static function setOptions ($options = array())
    {
        self::$options = $options;
        return TRUE;
    }
    /**
     * 初始化memcache配置
     * init The Configuration
     * same as setOptions()
     * @param array $options
     * @return Boolean
     */
    public static function initOption ($options = array())
    {
        self::$options = $options;
        return TRUE;
    }
    /**
     * 重新初始化memcache链接 
     */
    public static function reInitConnection ()
    {
        if (self::$instance) {
            self::$instance->_initConnection();
        }
    }
    /**
     * 获取memcache配置
     * get the Configuration
     * @return Array
     */
    public static function getOptions ()
    {
        return self::$options;
    }
    /**
     * 初始化memcache链接
     * Initialize the Read and Write Link
     * If No Memcache extension loaded Throw Afx_Db_Exception
     * @throws Afx_Db_Exception
     * @return Boolean
     */
    private function _initConnection ()
    {
        if (is_array(self::$options) && isset(self::$options['memcache'])) {
            static $keys_wanted = array('host' => 1, 'port' => 1);
            $arr = self::$options['memcache'];
            if (! isset($arr['master'])) {
                throw new Afx_Db_Exception(
                'no memcache master configuration found', '404');
            }
            $master = $arr['master'];
            if (isset($arr['slave'])) {
                $slave = $arr['slave'];
            }
            $nokeys = array_diff_key($keys_wanted, $master);
            if (count($nokeys)) {
                foreach ($nokeys as $k => $v) {
                    throw new Afx_Db_Exception(
                    "no memcache master $k configuration found", '404');
                }
            }
            if (! is_array($slave)) {
                throw new Afx_Db_Exception("memcache slave must be an array",
                '404');
            }
            foreach ($slave as $k => $v) {
                $nokeys = array_diff_key($keys_wanted, $v);
                if (count($nokeys)) {
                    foreach ($nokeys as $k => $v) {
                        throw new Afx_Db_Exception(
                        "no memcache slave $k configuration found", '404');
                    }
                }
            }
            if (class_exists('Memcache')) {
                //                self::$read_cache = new Memcache();
                //                self::$read_cache->addServer($slave['host'],
                //                (int) $slave['port']);
                //                self::$read_cache->pconnect($slave['host'], (int) $slave['port'],
                //                2);
                foreach ($slave as $k => $v) {
                    $mm = new Memcache();
                    $ret=@$mm->connect($v['host'], (int) $v['port'],1);
                    if(!$ret){
                       throw new Afx_Db_Exception('memcache was down away', '10062');
                    }
                    ++ self::$slave_num;
                    self::$read_cache[] = $mm;
                }
                self::$write_cache = new Memcache();
//                @self::$write_cache->addServer($master['host'],
//                (int) $master['port']);
                $ret=@self::$write_cache->connect($master['host'],
                (int) $master['port'], 1);
                if(!$ret){
                   throw new Afx_Db_Exception('memcache was down away', '10062');
                }

            } else {
                throw new Afx_Db_Exception(
                "no Memcache Class Found Please check the memcache installtion",
                '404');
            }
        } else {
            throw new Afx_Db_Exception('no memcache configuration found', '404');
        }
    }
    /**
     * 随机获取读链接 
     */
    public static function getReadCache ()
    {
        $server_num = rand(0, self::$slave_num) % self::$slave_num;
        if (isset(self::$read_cache[$server_num])) {
            return self::$read_cache[$server_num];
        }
    }
    /**
     * 获取写链接 
     */
    public static function getWriteCache ()
    {
        if (self::$write_cache)
            return self::$write_cache;
    }
    /**
     * memcache Add 包装
     * The Memcache add Wrapper
     * Write To The Master
     * @param string $key
     * @param mixed $value
     * @param int $timeout
     * @param int $flag
     * @return Boolean
     */
    public function add ($key, $value, $timeout = 60, $flag = MEMCACHE_COMPRESSED)
    {
        if (self::$write_cache) {
            return self::$write_cache->add($key, $value, $flag, $timeout);
        }
    }
    /**
     * memcache delete 包装
     * The Memcache delete Wrapper
     * Delete  The Master
     * @param string $key
     * @param int $timeout  default=0 means no expired
     * @return Boolean
     */
    public function delete ($key, $timeout = 0)
    {
        if (self::$write_cache) {
            return self::$write_cache->delete($key, $timeout);
        }
    }
    /**
     * memcache get 包装
     * The Memcache get Wrapper
     * Read from the Slave
     * @param string $key
     * @param Boolean $master can be true or false
     * @return mixed
     */
    public function get ($key, $master = FALSE)
    {
        if ($key != NULL && self::$read_cache) {
            if ($master == FALSE) {
                return self::getReadCache()->get($key);
            } elseif ($master == TRUE) {
                if (self::$write_cache) {
                    return self::getWriteCache()->get($key);
                }
            }
        }
    }
    /**
     * memcache set 包装
     * The Memcache set Wrapper
     * Write To The Master
     * @param string $key
     * @param mixed $value
     * @param int $timeout
     * @param int $flag
     * @return Boolean
     */
    public function set ($key, $value, $timeout = 60, $flag = MEMCACHE_COMPRESSED)
    {
        if (self::$write_cache) {
            return self::$write_cache->set($key, $value, $flag, $timeout);
        }
    }
    /**
     * memcache flush 包装
     * please Don't use this method
     * It will delete all the items on the master server
     * if you do really want to clean the master server uncomment this function body
     * @deprecated
     * @return Boolean
     */
    public function flush ()
    {
//        if (self::$write_cache) {
//            return self::$write_cache->flush();
//        }
    }
    /**
     * memcache replace 包装
     * The Memcache replace Wrapper
     * replace the master
     * @param string $key
     * @param mixed $value
     * @param int $timeout
     * @param int $flag
     * @return Boolean
     */
    public function replace ($key, $value, $timeout = 60,
    $flag = MEMCACHE_COMPRESSED)
    {
        if (self::$write_cache) {
            return self::$write_cache->replace($key, $value, $flag, $timeout);
        }
    }
    /** 
     * memcache increment 包装
     * The Memcache increment Wrapper
     * @param string $key
     * @param int $value
     * @return Boolean
     */
    public function increment ($key, $value = 1)
    {
        if (self::$write_cache) {
            return self::$write_cache->increment($key, $value);
        }
    }
    /**
     * memcache decrement 包装
     * The Memcache decrement Wrapper
     * @param string $key
     * @param int $value
     * @return Boolean
     */
    public function decrement ($key, $value = 1)
    {
        if (self::$write_cache) {
            return self::$write_cache->decrement($key, $value);
        }
    }
    /**
     * memcache getStatus 包装
     * The Memcache getStatus Wrapper
     * @param string $which can be master or slave or null means all
     * @return array
     */
    public function getStatus ($which = NULL)
    {
        if ($which == 'master') {
            if (self::$write_cache) {
                return self::$write_cache->getStats();
            }
        } elseif ($which == 'slave') {
            if (self::$read_cache) {
                return self::$read_cache->getStats();
            }
        }
        if (self::$read_cache && self::$write_cache)
            return array(self::$write_cache->getStats(),
            self::getReadCache()->getStats());
    }
    /**
     * memcache getVersion 包装
     * The Memcache getVersion Wrapper
     * @return array
     */
    public function getVersion ()
    {
        if (self::$read_cache && self::$write_cache) {
            return array(self::$read_cache->getVersion(),
            self::$write_cache->getVersion());
        }
    }
    /**
     * memcache getExtendsStatus 包装
     * @param string $type
     * @param int $id
     * @param int $limit
     */
    public function getExtendsStatus ($type, $id = 0, $limit = 1000)
    {
        return self::$write_cache->getExtendedStats($type, $id, $limit);
    }
    /**
     * 查看指定前缀的值 没有前缀查看所有
     * dump all the values in memcache prefix by the $prefix variable
     * @param string $prefix 前缀
     */
    public function dump ($prefix = NULL)
    {
        $arr = self::$options['memcache']['master'];
        $key = $arr['host'] . ":" . $arr['port'];
        $allSlabs = $this->getExtendsStatus('slabs');
        $allItems = $this->getExtendsStatus('items');
        foreach ($allItems as $server => $slabs) {
            foreach ($slabs as $slabId=>$slabMeta ) {
              foreach ($slabMeta as $k=>$v) {
              $cdump = $this->getExtendsStatus('cachedump', (int) $k,10000);
                if (is_array($cdump[$key])) {
                    foreach ($cdump[$key] as $k => $v) {
                        if ($prefix &&strncasecmp($prefix, $k, strlen($prefix)) == 0) {
                            $data = $this->get($k);
                            if(is_array($data)){
                            echo count(array_keys($data));
                            Afx_Debug_Helper::print_r(array($k => $data));
                            }
                        } else
                            if (! $prefix) {
                                $data = $this->get($k);
                                if(is_array($data)){
                                echo count(array_keys($data));
                                Afx_Debug_Helper::print_r(array($k => $data));
                            }
                            }
                    }
                }
              }

            }
        }
    }
    /**
     * Memcache getMulti 包装
     * The Memcache getMulti Wrapper
     * @param  array $arr
     * @param Boolean $master can be true|false default false
     * @return array
     */
    public function getMulti ($arr = array(), $master = FALSE)
    {
        if (is_array($arr) && count($arr)) {
            $ret = array();
            foreach ($arr as $k) {
                $ret[] = $this->get($k, $master);
            }
            return $ret;
        }
        return NULL;
    }
    /**
     * Memcache setMulti 包装
     * The Memcache setMulti Wrapper
     * @param array $arr
     * @param int $timeout
     * @param int $flag
     * @return Boolean
     */
    public function setMulti ($arr = array(), $timeout = 0, $flag = MEMCACHE_COMPRESSED)
    {
        if (is_array($arr) && count($arr)) {
            foreach ($arr as $k => $v) {
                $this->set($k, $v, $flag, $timeout);
            }
            return TRUE;
        }
        return FALSE;
    }
    /**
     * 获取单例变量
     * Get the Instance
     * @return Afx_Db_Memcache
     */
    public static function Instance ()
    {
        if (NULL === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    /**
     * 析构函数
     */
    function __destruct ()
    {
    	
    }
    /**
     * 魔术方法
     * @param string $m
     * @param array $arg
     */
    public function __call ($m, $arg)
    {
    	
    }
}
?>