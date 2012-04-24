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
 * @version $Id Mongo.php
 * The Mongo Class wrapper mongo class
 * @author Afx team && firedtoad@gmail.com &&dietoad@gmail.com
 */
class Afx_Db_Mongo
{
    protected static $slavenum = 0;
    protected static $_readlink = array();
    /**
     * @var Afx_Db_Mongo
     */
    private static $_instance = NULL;
    protected static $_options;
    /**
     *
     * @var Mongo
     */
    protected static $_writelink;
    protected static $_writeDb;
    protected static $_writeCollection;
    protected static $_readlinkConf;
    /**
	 * @return the $_writeDb
	 */
	public static function getWriteDb() {
		return Afx_Db_Mongo::$_writeDb;
	}

	/**
	 * @param field_type $_writeDb
	 */
	public static function setWriteDb($_writeDb) {
		Afx_Db_Mongo::$_writeDb = $_writeDb;
	}

	/**
	 * @return the $_writeCollection
	 */
	public static function getWriteCollection() {
		return Afx_Db_Mongo::$_writeCollection;
	}

	/**
	 * @param field_type $_writeCollection
	 */
	public static function setWriteCollection($_writeCollection) {
		Afx_Db_Mongo::$_writeCollection = $_writeCollection;
	}

	/**
     * @return array the $options
     */
    public static function getOptions ()
    {
        return Afx_Db_Mongo::$options;
    }
    /**
     * @return Mongo the $writelink
     */
    public function getWritelink ()
    {
        return self::$_writelink->selectDB(self::$_writeDb)->selectCollection(
        self::$_writeCollection);
    }
    /**
     * @param array $options
     */
    public static function setOptions ($options)
    {
        Afx_Db_Mongo::$_options = $options;
    }
    public function __construct ()
    {
        $this->_init();
    }
    private function _init ()
    {
        if (is_array(self::$_options) && count(self::$_options) > 0) {
            if (! isset(self::$_options['mongo']) ||
             ! is_array(self::$_options['mongo'])) {
                throw new Afx_Db_Exception('no mongo configuration found', '404');
            }
            $conf = self::$_options['mongo'];
            if (! isset($conf['master'])) {
                throw new Afx_Db_Exception('no mongo master configuration found',
                '404');
            }
            if (! isset($conf['slave']) || ! is_array($conf['slave'])) {
                throw new Afx_Db_Exception('no mongo slave configuration found',
                '404');
            }
            $master = $conf['master'];
            $slave = $conf['slave'];
            static $keys = array('host' => 1, 'port' => 1, 'db' => 1,
            'collection' => 1);
            $nokeys = array_diff_key($keys, $master);
            if (is_array($nokeys) && count($nokeys) > 0) {
                foreach ($nokeys as $k) {
                    throw new Afx_Db_Exception("no db mongo master $k found!",
                    '404');
                }
            }
            if (! is_array($slave)) {
                throw new Afx_Db_Exception("slave must be an array", '404');
            }
            $mongo_dsn = 'mongodb://' . $master['host'] . ":" . $master['port'];
            self::$_writelink = new Mongo($mongo_dsn,array('persistent'=>1));
            self::$_writeDb = $master['db'];
            self::$_writeCollection = $master['collection'];
            foreach ($slave as $k => $v) {
                $nokeys = array_diff_key($keys, $v);
                if (is_array($nokeys) && count($nokeys) > 0) {
                    foreach ($nokeys as $k) {
                        throw new Afx_Db_Exception(
                        "no db mongo master $k found!", '404');
                    }
                }
                $mongo_dsn = 'mongodb://' . $v['host'] . ":" . $v['port'];
                self::$_readlink[] = new Mongo($mongo_dsn,array('persistent'=>1));
                self::$_readlinkConf[] = array('db' => $v['db'],
                'collection' => $v['collection']);
                ++ self::$slavenum;
            }
        } else {
            throw new Afx_Db_Exception('no configuration found', '404');
        }
    }
    public function find ($condtion = array(),$limit=10, $hint = NULL, $master = False)
    {
        if ($master) {
            if (! $hint)
                return iterator_to_array($this->getWritelink()->find($condtion));
            return iterator_to_array(
            $this->getWritelink()
                ->find($condtion)
                ->hint($hint));
        }
        if (! hint)
            return iterator_to_array($this->getReadLink()->find($condtion));
        return iterator_to_array(
        $this->getReadLink()
            ->find($condtion)
            ->hint($hint));
    }
    /**
     * @return MongoCollection
     */
    public function getReadLink ()
    {
        $server_num = rand(0, self::$slavenum) % self::$slavenum;
        echo 'get read link servernum=', $server_num;
        if (isset(self::$_readlink[$server_num])) {
            return self::$_readlink[$server_num]->selectDB(
            self::$_readlinkConf[$server_num]['db'])->selectCollection(
            self::$_readlinkConf[$server_num]['collection']);
        }
    }
    public function findOne ($condtion = array(), $hint = NULL, $master = False)
    {
        if ($master) {
            if (! $hint)
                return iterator_to_array(
                $this->getWriteLink()
                    ->find($condtion)
                    ->limit(1));
            return iterator_to_array(
            $this->getWriteLink()
                ->find($condtion)
                ->hint($hint)
                ->limit(1));
        }
        if (! $hint)
            return iterator_to_array(
            $this->getReadLink()
                ->find($condtion)
                ->limit(1));
        return iterator_to_array(
        $this->getReadLink()
            ->find($condtion)
            ->hint($hint)
            ->limit(1));
    }
    public function insert ($data = array())
    {
        $this->getWritelink()->insert($data);
    }
    public function batchinsert ($data = array())
    {
//    	Afx_Debug_Helper::print_r($data);
//    	Afx_Debug_Helper::print_r($this->getWritelink());
        $this->getWritelink()->batchinsert($data);
    }
    public function list_DBs ()
    {
        return self::$_writelink->list_DBs();
    }
    public function connect ()
    {}
    public function close ()
    {}
    public function validate ()
    {}
    public function deleteIndex ($indexName)
    {
        return $this->getWritelink()->deleteIndex($indexName);
    }
    public function ensureIndex ($key = array(), $options = array())
    {
        return $this->getWritelink()->ensureIndex($key, $options);
    }
    public function save ($data = array())
    {
        $this->getWritelink()->save($data);
    }
    public function remove ($data = array())
    {
        $this->getWritelink()->remove($data);
    }
    public function dropDB ($dbname = NULL)
    {
        self::$_writelink->dropDB($dbname);
    }
    
    /**
     * @return Afx_Db_Mongo
     */
    public static function Instance ()
    {
        if (! self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}