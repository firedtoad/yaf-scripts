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
 * @package Afx_Db
 * @version $Id Adapter.php
 * The Pdo Class Adapter Provider Communication With The RelationShip Database
 * @author Afx team && firedtoad@gmail.com &&dietoad@gmail.com
 */
class Afx_Db_Adapter
{
    /**
     * @var PDO
     */
    
    private static $link_read = NULL;
    
     /**
     * @var PDO
     */
    private static $link_write = NULL;
    
    /**
     * store the default dbname
     * @var string
     */
    
    private static $dbname=NULL;
    
    /**
     * 
     * @var string
     */
    private static $db_changed;
     /**
     * store the Database Configuration 
     * @var array
     */
    private  static $options = array();
    
    /**
     * store the databases mapping to the tables 
     * @var array
     */
    
    private static $mapping=array();
    
      /**
     * @var string
     */
    private static $read_dsn = NULL;
    
     /**
     * @var string
     */
    private static $write_dsn = NULL;
    
     /**
     * @var Afx_Db_Adapter
     */
    
    private static $instance =NULL;
    
     /**
      * the last execute sql
      *@var string 
      */
    private static $lastSql=NULL;
    
    /**
     * last operator server  
     * @var string 
     */
    private static $lastServer=NULL;
    
     /**
     * last error info 
     * @var string 
     */
    private static $lastError=NULL;
    
    /**
     * @return the $mapping
     */
    public static function getMapping ()
    {
        return self::$mapping;
    }

	/**
     * @param array $mapping
     */
    public static function setMapping ($mapping)
    {
        self::$mapping = $mapping;
    }
    
    /**
     * Initialize The Configuration
     * @param array $arr
     */
	public static function initOption($arr=array()){        
        self::$options=$arr;
        return TRUE;
    }
    
    /**
     * Get the Configuration
     * @return array
     */
    public static function getOptions(){
        return self::$options;
    }
    
    /**
     * @return Afx_Db_Adapter
     */
    public static function instance(){
        if(NULL===self::$instance){
            self::$instance=new self();
        }
        return self::$instance;
    }
    
    /**
     * Initialize The PDO connections
     * @throws Afx_Db_Exception
     * @throws Exception
     */
    private function _initConnection(){
        if (count(self::$options) == 0) {
            throw new Afx_Db_Exception('no  configuration found!', 404);
        }
        if(!isset(self::$options['db'])){
            throw new Afx_Db_Exception('no db  configuration found!', 404);
        }
        if(!isset(self::$options['db']['master'])||!is_array(self::$options['db']['master'])){
            throw new Afx_Db_Exception('no master db configuration found!', 404);
        }
        $master=self::$options['db']['master'];
        //if no slave use the master as the slave
        $slave=isset(self::$options['db']['slave'])&&is_array(self::$options['db']['slave'])?self::$options['db']['slave']:self::$options['db']['master'];
       static  $keys = array('host' => 1, 'user' => 1, 'password' => 1, 'port' => 1, 
        'dbname' => 1,'charset'=>1);
        $nokeys = array_diff_key($keys, $master);
        if (count($nokeys)) {
            foreach ($nokeys as $k => $v) {
                throw new Afx_Db_Exception("no $k found in db master configuration!", 
                404);
            }
        }
        $nokeys = array_diff_key($keys, $slave);
        if (count($nokeys)) {
            foreach ($nokeys as $k => $v) {
                throw new Afx_Db_Exception("no $k found in db slave configuration!",  404);
            }
        }
      self::$dbname=$master['dbname'];
      self::$read_dsn= 'mysql:host=' . $slave['host'] . ';port=' .$slave['port'] . ';dbname=' . $slave['dbname'].';charset='.$slave['charset'].';';
      self::$write_dsn= 'mysql:host=' . $master['host'] . ';port=' .$master['port'] . ';dbname=' . $master['dbname'].';charset='.$master['charset'].';';
        
        try{
          self::$link_read = new PDO(self::$read_dsn, $slave['user'],$slave['password'],array(PDO::ATTR_TIMEOUT=>1));
          self::$link_write = new PDO(self::$write_dsn, $master['user'],$master['password'],array(PDO::ATTR_TIMEOUT=>1));
        }catch(PDOException $e){
            throw new Exception($e) ;
        }
    }
    
    /**
     * getSlave Server link
     * @return PDO 
     */
    public static function getSalve(){
        if(self::$link_read){
            return self::$link_read;
        }
        return NULL;
    }
    
     /**
     * getMaster Server link
     * @return PDO 
     */
    public static function getMaster(){
      if(self::$link_write){
          return self::$link_write;
      }
      return NULL;
    }
    
    /**
     * The construction we do initialize the database connections
     */
    private function __construct ()
    {
      $this->_initConnection();      
    }
    
    /**
     * ReInitialize Database Connections
     */
    public static function reInitConnection(){
        if(self::$instance){
            self::$instance->_initConnection();
            return TRUE;
        }
    }
    
    /**
     * 
     * quote a string with slashs
     * @param string $str
     * @param int $style PDOPram
     */
    public function quote($str,$style=PDO::PARAM_INT){
        if($str!=NULL)return self::$link_write->quote($str,$style);
    }
    
    /**
     * The main method execute the sql string
     * @param string $sql the sql string
     * @param string $table  on which table 
     * @param string $server  which server 
     * @param Boolean $usetrans whether use transaction default False
     * @throws PDOException 
     * @throws Exception
     */    
    public function execute ($sql,$table=NULL,$server='slave',$usetrans=FALSE)
    {
        self::$lastSql=$sql;
        self::$lastServer=$server;
         echo $sql,"\n";
        //we want to map the table in different database so
        //selete the default database every time
        if(self::$db_changed!=NULL){
        self::$link_read->exec("use ".self::$dbname);
        self::$link_write->exec("use ".self::$dbname);
        self::$db_changed=NULL;
        }
        //check if we need mapping 
        //Notice that it exists a bug when two database have the same table name 
        if($table!=NULL&&is_string($table)){
            if(is_array(self::$mapping)&&count(self::$mapping)){
                foreach (self::$mapping as $k =>$v){
                    if(isset($v[$table])){
                        self::$link_read->exec("use $k");
                        self::$link_write->exec("use $k");
                        self::$db_changed='changed';
                        break;
                    }
                }
            }
        }
        if (strncasecmp($sql, 'select', 6) == 0) {
            //read from the database
            //default operator the slave
            try{
             if($server=='slave'){
             $statment=self::$link_read->prepare($sql);
             }elseif($server=='master'){
             $statment=self::$link_write->prepare($sql);
             }
             if($statment instanceof  PDOStatement){
                $statment->execute();
             if($statment->errorCode()!='00000'){
                    self::$lastError=$statment->errorInfo();
                    throw new PDOException(implode('',$statment->errorInfo()), $statment->errorCode());
              }
                $obj= $statment->fetchALL(PDO::FETCH_ASSOC);
              if($statment->errorCode()!='00000'){
                    self::$lastError=$statment->errorInfo();
                    throw new PDOException(implode('',$statment->errorInfo()), $statment->errorCode());
              }
                return $obj;
            }
              }catch(PDOException $e){
                 throw new Exception($e) ;
              }
        } else {
            //delete or update 
            //operator the master
            $ret = TRUE;
            try {
                if($usetrans)self::$link_write->beginTransaction();
                self::$link_write->exec($sql);
                if($usetrans) self::$link_write->commit();
                if(self::$link_write->errorCode()!='00000'){
                     self::$lastError=self::$link_write->errorInfo();
                    throw new PDOException(implode('',self::$link_write->errorInfo()), self::$link_write->errorCode());
                }
            } catch (PDOException $e) {
                $ret = FALSE;
                throw new Exception($e) ;
            }
            return $ret;
        }
    }
}
