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
 * @package Afx_Module
 * @version $Id Mongo.php
 * The Mongo Class wrapper mongo class
 * @author Afx team && firedtoad@gmail.com &&dietoad@gmail.com
 */
class Afx_Db_Mongo{
	
	protected static $slavenum=0;
	protected static $_readlink=array();
	/**
	 * @var Afx_Db_Mongo
	 */
	private static $_instance=NULL;
	
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
	 * @return array the $options
	 */
	public static function getOptions() {
		return Afx_Db_Mongo::$options;
	}

	/**
	 * @return Mongo the $writelink
	 */
	public function getWritelink() {
		return self::$_writelink->selectDB(self::$_writeDb)->selectCollection(self::$_writeCollection);
	}

	/**
	 * @param array $options
	 */
	public static function setOptions($options) {
		Afx_Db_Mongo::$_options = $options;
	}

	public function __construct(){
		$this->_init();
	}
	private function _init(){
	    if(is_array(self::$_options)&&count(self::$_options)>0){
	    	if(!isset(self::$_options['mongo'])||!is_array(self::$_options['mongo'])){
	    		throw  new Afx_Db_Exception('no mongo configuration found', '404');
	    	}
	        $conf=self::$_options['mongo'];
	        if(!isset($conf['master'])){
	        	throw  new Afx_Db_Exception('no mongo master configuration found', '404');
	        }
	       if(!isset($conf['slave'])||!is_array($conf['slave'])){
	        	throw  new Afx_Db_Exception('no mongo slave configuration found', '404');
	        }
	        $master=$conf['master'];
	        $slave=$conf['slave'];
            static $keys = array('host' => 1, 'port' => 1,'db'=>1,'collection'=>1);
            $nokeys=array_diff_key($keys, $master);
            if(is_array($nokeys)&&count($nokeys)>0){
            	foreach ($nokeys as $k) {
            		throw new Afx_Db_Exception("no db mongo master $k found!", '404');
            	}
            }
            if(!is_array($slave)){
            	throw new Afx_Db_Exception("slave must be an array", '404');
            }
            
            $mongo_dsn='mongodb://'.$master['host'].":".$master['port'];
            self::$_writelink=new Mongo($mongo_dsn);
            self::$_writeDb=$master['db'];
            self::$_writeCollection=$master['collection'];    
            foreach ($slave as $k=>$v) {
            	$nokeys=array_diff_key($keys, $v);
	            if(is_array($nokeys)&&count($nokeys)>0){
	            	foreach ($nokeys as $k) {
	            		throw new Afx_Db_Exception("no db mongo master $k found!", '404');
	            	}
	            }
	            $mongo_dsn='mongodb://'.$v['host'].":".$v['port'];
	            self::$_readlink[]=new Mongo($mongo_dsn);
	            self::$_readlinkConf[]=array('db'=>$v['db'],'collection'=>$v['collection']);
	            ++self::$slavenum;
            }
	    }else{
	    	throw  new Afx_Db_Exception('no configuration found', '404');
	    }
	    
	}
	
	public function find($condtion=array(),$hint=NULL,$master=False){
		if($master){
			if(!$hint)return iterator_to_array($this->getWritelink()->find($condtion));
			return iterator_to_array($this->getWritelink()->find($condtion)->hint($hint));
		}
		if(!hint)
		return iterator_to_array($this->getReadLink()->find($condtion));
		return iterator_to_array($this->getReadLink()->find($condtion)->hint($hint));
	}
	
	/**
	 * @return MongoCollection
	 */
	public function getReadLink(){
		$server_num=rand(0, self::$slavenum)%self::$slavenum;
		echo 'get read link servernum=',$server_num;
		if(isset(self::$_readlink[$server_num])){
			return self::$_readlink[$server_num]->selectDB(self::$_readlinkConf[$server_num]['db'])->selectCollection(self::$_readlinkConf[$server_num]['collection']);
		}
	}
	public function findOne($condtion=array(),$hint=NULL,$master=False){
		 if($master){
		 	if(!$hint)return iterator_to_array($this->getWriteLink()->find($condtion)->limit(1));
			return iterator_to_array($this->getWriteLink()->find($condtion)->hint($hint)->limit(1));
		}
		if(!$hint)
		return iterator_to_array($this->getReadLink()->find($condtion)->limit(1));
		return iterator_to_array($this->getReadLink()->find($condtion)->hint($hint)->limit(1));
	}
	public function insert($data=array()){
		    $this->getWritelink()->insert($data);
	}
	public function batchinsert($data=array()){
		$this->getWritelink()->insert($data);
	}
	
	public function list_DBs(){
		return self::$_writelink->list_DBs();
	}
	
	public function connect(){
		
	}
	public function close(){
		
	}
	public function validate(){
		
	}
   
	public function deleteIndex($indexName){
		return $this->getWritelink()->deleteIndex($indexName);
	}
	public function ensureIndex($key=array(),$options=array()){
		return $this->getWritelink()->ensureIndex($key,$options);
	}
	public function save($data=array()){
		$this->getWritelink()->save($data);
	}
	public function remove($data=array()){
		$this->getWritelink()->remove($data);
	}
	public function dropDB($dbname=NULL){
		self::$_writelink->dropDB($dbname);
	}
	
	/**
	 * @return Afx_Db_Mongo
	 */
	public static function Instance(){
		if(!self::$_instance){
			self::$_instance=new self();
		}
		return self::$_instance;
	}
}