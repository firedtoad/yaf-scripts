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
 * @version $Id Adapter.php
 * The Pdo Class Adapter Provider Communication With The RelationShip Database
 * @author Afx TEAM && firedtoad@gmail.com && dietoad@gmail.com
 */
class Afx_Db_Adapter {
	/**
	 * 读取数据库链接数组
	 * @var PDO
	 */
	private static $link_read = array ();
	/**
	 * 写数据库链接数组
	 * @var PDO
	 */
	private static $link_write = array ();
	/**
	 * 保存从库数量以便随机选择数据库时使用
	 * store the slave_count by per database connection
	 * @var array
	 */
	private static $slave_count = array ();
	/**
	 * 默认数据库名,切库后切换回默认数据库
	 * store the default dbname
	 * @var string
	 */
	private static $dbname = NULL;
	/**
	 * 是否有切库操作
	 * @var string
	 */
	private static $db_changed;
	/**
	 * 数据库配置
	 * store the Database Configuration
	 * @var array
	 */
	private static $options = array ();
	/**
	 * 表库映射
	 * store the databases mapping to the tables
	 * @var array
	 */
	private static $mapping = array ();
	/**
	 * 从库数量
	 * Slave numbers
	 * @var int
	 */
	private static $slave_num = 0;
	/**
	 * 读数据库dsn
	 * @var string
	 */
	private static $read_dsn = array ();
	/**
	 * 写数据库dsn
	 * @var string
	 */
	private static $write_dsn = NULL;
	/**
	 * 单例保存本身
	 * @var Afx_Db_Adapter
	 */
	private static $instance = NULL;
	/**
	 * 保存执行的所有sql
	 * the last execute sqls
	 *@var array
	 */
	private static $lastSql = array ();
	/**
	 * 最后一次操作的数据库
	 * last operator server
	 * @var string
	 */
	private static $lastServer = NULL;
	/**
	 * 最后一次产生的错误
	 * last error info
	 * @var string
	 */
	private static $lastError = NULL;
	/**
	 * 调试标志
	 * var Boolean
	 */
	public static $debug = TRUE;
	
	/**
	 * 获取执行的sql
	 * @return the $lastSql
	 */
	public static function getLastSql() {
		return Afx_Db_Adapter::$lastSql;
	}
	/**
	 * 获取 表库映射
	 * @return the $mapping
	 */
	public static function getMapping() {
		return self::$mapping;
	}
	/**
	 * 设置表库映射
	 * @param array $mapping
	 */
	public static function setMapping($mapping) {
		self::$mapping = $mapping;
	}
	/**
	 * 初始化配置
	 * Initialize The Configuration
	 * @param array $arr
	 */
	public static function initOption($arr = array()) {
		self::$options = $arr;
		return TRUE;
	}
	/**
	 * 获取配置
	 * Get the Configuration
	 * @return array
	 */
	public static function getOptions() {
		return self::$options;
	}
	/**
	 * 单例返回自己
	 * @return Afx_Db_Adapter
	 */
	public static function Instance() {
		if (NULL === self::$instance) {
			self::$instance = new self ();
		}
		return self::$instance;
	}
	/**
	 * 初始化数据库链接
	 * Initialize The PDO connections
	 * @throws Afx_Db_Exception
	 * @throws Exception
	 */
	static $count = 0;
	private function _initConnection() {
		//echo "init ",self::$count++;
		if (count ( self::$options ) == 0) {
			throw new Afx_Db_Exception ( 'no  configuration found!', 404 );
		}
		if (! isset ( self::$options ['db'] )) {
			throw new Afx_Db_Exception ( 'no db  configuration found!', 404 );
		}
		if (! isset ( self::$options ['db'] ['master'] ) || ! is_array ( self::$options ['db'] ['master'] )) {
			throw new Afx_Db_Exception ( 'no master db configuration found!', 404 );
		}
		$master = self::$options ['db'] ['master'];
		//if no slave use the master as the slave
		$slave = isset ( self::$options ['db'] ['slave'] ) && is_array ( self::$options ['db'] ['slave'] ) ? self::$options ['db'] ['slave'] : self::$options ['db'] ['master'];
		static $keys = array ('host' => 1, 'user' => 1, 'password' => 1, 'port' => 1, 'dbname' => 1, 'charset' => 1 );
		$nokeys = array_diff_key ( $keys, $master );
		if (count ( $nokeys )) {
			foreach ( $nokeys as $k => $v ) {
				throw new Afx_Db_Exception ( "no $k found in db master configuration!", 404 );
			}
		}
		if (count ( $slave )) {
			foreach ( $slave as $k => $v ) {
				$nokeys = array_diff_key ( $keys, $v );
				if (count ( $nokeys )) {
					foreach ( $nokeys as $k1 => $v ) {
						throw new Afx_Db_Exception ( "no $k1 found in db slave $k configuration!", 404 );
					}
				}
			}
		}
		self::$dbname = $master ['dbname'];
		self::$write_dsn [self::$dbname] = 'mysql:host=' . $master ['host'] . ';port=' . $master ['port'] . ';dbname=' . $master ['dbname'] . ';charset=' . $master ['charset'] . ';';
		if (! isset ( self::$link_read [self::$dbname] ) || ! is_array ( self::$link_read [self::$dbname] )) {
			self::$link_read [self::$dbname] = array ();
		}
		if (! isset ( self::$read_dsn [self::$dbname] ) || ! is_array ( self::$read_dsn [self::$dbname] )) {
			self::$read_dsn [self::$dbname] = array ();
		}
		self::$slave_num = 0;
		try {
			foreach ( $slave as $k => $v ) {
				++ self::$slave_num;
				$dsn = 'mysql:host=' . $v ['host'] . ';port=' . $v ['port'] . ';dbname=' . $v ['dbname'] . ';charset=' . $v ['charset'] . ';';
				if ($dsn) {
					self::$read_dsn [self::$dbname] [] = $dsn;
				}
				self::$link_read [self::$dbname] [] = new PDO ( $dsn, $v ['user'], $v ['password'], array (PDO::ATTR_TIMEOUT => '1', PDO::ATTR_PERSISTENT => 0, PDO::MYSQL_ATTR_INIT_COMMAND => 'set names utf8' ) );
			}
			self::$slave_count [self::$dbname] = self::$slave_num;
			self::$link_write [self::$dbname] = new PDO ( self::$write_dsn [self::$dbname], $master ['user'], $master ['password'], array (PDO::ATTR_TIMEOUT => '1', PDO::ATTR_PERSISTENT => 0, PDO::MYSQL_ATTR_INIT_COMMAND => 'set names utf8' ) );
		} catch ( PDOException $e ) {
			//            throw $e;
			throw new Exception ( $e->getMessage (), '10061' );
		}
	}
	/**
	 * 随机获取从库链接
	 * getSlave Server link
	 * @return PDO
	 */
	public static function getSalve() {
		$server_num = rand ( 0, self::$slave_count [self::$dbname] ) % self::$slave_count [self::$dbname];
		if (isset ( self::$link_read [self::$dbname] [$server_num] )) {
			return self::$link_read [self::$dbname] [$server_num];
		}
		return NULL;
	}
	/**
	 * 获取主库链接
	 * getMaster Server link
	 * @return PDO
	 */
	public static function getMaster() {
		if (isset ( self::$link_write [self::$dbname] )) {
			return self::$link_write [self::$dbname];
		}
		return NULL;
	}
	/**
	 * 单例私有构造函数
	 * The construction we do initialize the database connections
	 */
	private function __construct() {
		$this->_initConnection ();
	}
	/**
	 * 重新初始化数据库
	 * ReInitialize Database Connections
	 */
	public static function reInitConnection() {
		if (self::$instance) {
			self::$instance->_initConnection ();
			return TRUE;
		}
	}
	/**
	 * 产生安全sql
	 * quote a string with slashs
	 * @param string $str
	 * @param int $style PDOPram
	 */
	public function quote($str, $style = PDO::PARAM_INT) {
		if ($str !== NULL && isset ( self::$link_write [self::$dbname] )) {
			if (self::$link_write [self::$dbname] instanceof PDO) {
				return self::$link_write [self::$dbname]->quote ( $str, $style );
			}
		}
	}
	/**
	 * 执行sql ,判断切库 保存sql 返回结果
	 * The main method execute the sql string
	 * @param string $sql the sql string
	 * @param string $table  on which table
	 * @param  Boolean $master whether operator the master
	 * @param Boolean $usetrans whether use transaction default False
	 * @throws PDOException
	 * @throws Exception
	 */
	public function execute($sql, $table = NULL, $master = FALSE, $usetrans = FALSE) {
		$server_num = rand ( 0, self::$slave_num ) % self::$slave_num;
		// Afx_Debug_Helper::print_r(self::$slave_count);
		if (strncasecmp ( $sql, 'EXPLAIN', 7 ) != 0) {
			self::$lastSql [] = $sql;
		}
		self::$lastServer = $master;
		if (self::$debug) {
			// echo $sql, "  serverNum=$server_num\n<br/>";
			Afx_Logger::log ( $sql );
		}
		//we want to map the table in different database so
		//selete the default database every time
		//        if (self::$db_changed != NULL) {
		//            self::$link_read[$server_num]->exec("use " . self::$dbname);
		//            self::$link_write->exec("use " . self::$dbname);
		//            self::$db_changed = NULL;
		//        }
		//check if we need mapping
		//Notice that it exists a bug when two database have the same table name
		if ($table != NULL && is_string ( $table )) {
			if (is_array ( self::$mapping ) && count ( self::$mapping )) {
				foreach ( self::$mapping as $k => $v ) {
					if (isset ( $v [$table] )) {
						$conf = Yaf_Registry::get ( 'conf' );
						if (isset ( $conf ['mappingdb'] ) && isset ( $conf ['mappingdb'] [$k] )) {
							
							if (! isset ( self::$link_read [$k] ) || ! isset ( self::$link_write [$k] )) {
								//echo "changed and init from ",self::$dbname,' to ',$k,"\n";
								$this->initOption ( $conf ['mappingdb'] [$k] );
								// Afx_Debug_Helper::print_r($conf['mappingdb'][$k]);
								$this->_initConnection ( $conf ['mappingdb'] [$k] );
							} else if (self::$dbname !== $k) {
								//echo "changed dbname from ",self::$dbname,' to ',$k,"\n";
								self::$dbname = $k;
							}
						}
						//echo   $sql=str_ireplace($table, $k.".".$table, $sql);
						//self::$link_read[$server_num]->exec("use $k");
						//self::$link_write->exec("use $k");
						// self::$db_changed = 'changed';
						break;
					}
				}
			}
		}
		// Afx_Debug_Helper::print_r(self::$read_dsn);
		if (strncasecmp ( $sql, 'select', 6 ) == 0) {
			//read from the database
			//default operator the slave
			try {
				if ($master == FALSE) {
					//                    self::$link_read[$server_num]->exec();
					$statment = self::$link_read [self::$dbname] [$server_num]->prepare ( $sql );
				} elseif ($master == TRUE) {
					$statment = self::$link_write [self::$dbname]->prepare ( $sql );
				}
				if ($statment instanceof PDOStatement) {
					$statment->execute ();
					if ($statment->errorCode () != '00000') {
						self::$lastError = $statment->errorInfo ();
						throw new PDOException ( implode ( '', $statment->errorInfo () ) . "  execute error", $statment->errorCode () );
					}
					$obj = $statment->fetchALL ( PDO::FETCH_ASSOC );
					if ($statment->errorCode () != '00000') {
						self::$lastError = $statment->errorInfo ();
						throw new PDOException ( implode ( '', $statment->errorInfo () ), "  fetch error", $statment->errorCode () );
					}
					return $obj;
				}
			} catch ( PDOException $e ) {
				echo $e->getMessage ();
				throw new Exception ( $e );
			}
		} else if (strncasecmp ( $sql, 'delete', 6 ) == 0 || strncasecmp ( $sql, 'update', 6 ) == 0 || strncasecmp ( $sql, 'insert', 6 ) == 0) {
			//delete or update  or insert
			//operator the master
			$ret = TRUE;
			try {
				if ($usetrans)
					self::$link_write [self::$dbname]->beginTransaction ();
				self::$link_write [self::$dbname]->exec ( $sql );
				if ($usetrans)
					self::$link_write [self::$dbname]->commit ();
				if (self::$link_write [self::$dbname]->errorCode () != '00000') {
					self::$lastError = self::$link_write [self::$dbname]->errorInfo ();
					throw new PDOException ( implode ( '', self::$link_write [self::$dbname]->errorInfo () ), self::$link_write [self::$dbname]->errorCode () );
				}
			} catch ( PDOException $e ) {
				$ret = FALSE;
				throw new Exception ( $e );
			}
			return $ret;
		} else {
			$stmt = self::$link_write [self::$dbname]->prepare ( $sql );
			$stmt->execute ();
			if ($stmt->errorCode () != '00000') {
				self::$lastError = $stmt->errorInfo ();
				throw new PDOException ( implode ( '', $stmt->errorInfo () ), $stmt->errorCode () );
			}
			return $stmt->fetchALL ();
		}
	}
}
