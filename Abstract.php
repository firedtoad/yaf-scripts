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
 * @version $Id Abstract.php
 * The Module Class Impliment The Core ORM CRUD Operator
 * @author Afx team && firedtoad@gmail.com &&dietoad@gmail.com
 */

abstract  class Afx_Module_Abstract{
    
    
    protected  $_tablename='t_dummy';
    
    /**
     * 
     * @var Afx_Db_Adapter
     */
    
    protected  $_adapter=NULL;
    
    /**
     * store the Object fetch from database and compare for update
     * @var stdClass
     */
    
    private $_obj;
    
    /**
     * The Only Instance 
     * @var Afx_Module_Abstract::
     */
    
    protected static  $_instance=NULL;
    
    /**
     * Get the pure Object Fetch From The database
     * @return stdClass 
     */
    public function getObj ()
    {
        return $this->_obj;
    }

	/**
	 * set The Pure Object  for insert 
	 * @deprecated
     * @param stdClass $_obj
     */
    public function setObj ($_obj)
    {
        $this->_obj = $_obj;
    }
     /**
     * Get the Db Adapter
     * @return Afx_Db_Adapter
     */

	public function getAdapter ()
    {
            
        return $this->_adapter;

    }
    /**
     * Magic function __set
     * @param string $name
     * @param mixed $value
     */
    public function __set($name,$value){
       self::$_instance->$name=$value;
    }
    
    /**
     * 
     * Magic function __set
     * @param string $name
     * @retunr  mixed
     */
    public function __get($name){
//        return self::$_instance->$name;
          return $this->$name;
    }
    /**
     * Set the Db Adapter
     * @param Afx_Db_Adapter $_adapter
     */
    public function setAdapter ($_adapter)
    {
        $this->_adapter = $_adapter;
    }
    /**
     * constructor function 
     * Here We use Default Db Adapter
     */
	public  function __construct(){
	    $this->_adapter=Afx_Db_Adapter::instance();
    }
    
    /**
     * Convert An Array To A stdClass 
     * @param array $arr
     * @return stdClass
     */
    public static function fromArray($arr=array()){
         if(count($arr)){
             $obj=new stdClass();
            foreach($arr as $k=>$v){
                $obj->$k=$v;
            }   
            return $obj;
         }
         return NULL;
    }
    
    /**
     * Convert An stdClass To A Array 
     * @param stdClass $obj
     * @return array
     */
    public static function toArray($obj=stdClass){
         $arr=array();
         if(!is_object($obj)){
             return NULL;
         }
         foreach ($obj as $k=>$v){
             $arr[$k]=$v;
         }
         return count($arr)?$arr:NULL;
    }
    
    /**
     * apply an array keys as self properties
     * @param array $arr
     */
  private function setProperties($arr=array()){
      if(!is_array($arr)){
          return;
      }
       foreach($arr as $k=>$v){
           $this->$k=$v;
       }
  }
    
  /**
   * save an object to the database
   * the most complicated function 
   * @param void
   * @return TRUE if success else FALSE
   */
   public function save(){
      $sql='';
       if($this->_obj===NULL){
        /*
         * the _obj is null so the object is new construct
         * we insert the object to the database
         */
       $sql='INSERT INTO '.$this->_tablename.' (';
              $arr=self::toArray($this);
              if(!is_array($arr)){
                  return FALSE;
              }
              foreach ($arr as $k=>$v){
                  if(strncasecmp($k, '_', 1)==0||strncasecmp($k, 'id', 2)==0){
                      //filter the id and startwith '_' properties
                      //because most of the table will have the id auto_increment 
                      //and nearly no table column name start with '_' so we filter them                      
                      unset($arr[$k]);
                      continue;
                  }
              }
              
             if(!count($arr))return FALSE;
             //concat the keys
             $keystr= implode(',',array_keys($arr));
             //here we add ',' more one time so drop it
             $sql.=substr($keystr, 0,strlen($keystr));
             $sql.=') VALUES(';
             //concat the values
             foreach ($arr as $k=>$v){
                 //here we only deal with string and numeric  
                 //we need deal others future
                    if(is_string($v)){
                        $sql.=$this->getAdapter()->quote($v,PDO::PARAM_STR).",";
                    }else{
                        $sql.=$this->getAdapter()->quote($v).",";
                    }
             }
             //here we add ',' more one time so drop it again
             $sql=substr($sql, 0,strlen($sql)-1);
             $sql.=')';
             
           return $this->getAdapter()->execute($sql,$this->_tablename);
          }
          if($this->_obj&& $this->_obj instanceof stdClass){
               //the _obj exists  it is fetch from the object
               //so update it
              $sql ='UPDATE '.$this->_tablename ;
              $arr_old=self::toArray($this->_obj);
              $arr_this=self::toArray($this);
              if(is_array($arr_old)&&is_array($arr_this)){
               //calculate  the properties need te be update
              $arr_update=array_intersect_key($arr_this, $arr_old);
              $arr_real_update=array_diff($arr_update, $arr_old);
              if(count($arr_real_update)){
                   $sql.=' set '; 
                   foreach ($arr_real_update as $k=>$v){
                       //here we only deal with string and numeric  
                       //we need deal others future
                       if(is_string($v)){
                           $sql.=$k."=".$this->getAdapter()->quote($v,PDO::PARAM_STR).",";
                       }elseif (is_numeric($v)){
                           $sql.=$k."=".$this->getAdapter()->quote($v).",";
                           
                       }
                    }
                    //',' more add again drop it
                    $sql=substr($sql,0,strlen($sql)-1);
                    if(isset($arr_old['id'])){
                        $sql.=sprintf(' where id=%d',$arr_old['id']);
                       return $this->getAdapter()->execute($sql,$this->_tablename);
                    }else{
                        return FALSE;
                    }
               }
               return FALSE;
              }else{
              return FALSE;
              }
          }
   }
   
   /**
    * delete the object in the database
    * @param int $id
    * @return TRUE if success else FALSE
    */
   
   
   public function delete($id=NULL){
      $rid=$id?($id>=0?$id:NULL):$this->_obj->id?($this->_obj->id>=0?$this->_obj->id:NULL):NULL;
      if(!$rid){
          return FALSE;
      }
      $sql=sprintf('DELETE  FROM '.$this->_tablename.' WHERE id=%d',$rid);
      return $this->getAdapter()->execute($sql,$this->_tablename);
   }
   
   /**
    * select an object from the database id is the specific id
    * @param long $id
    * @param  string $server master|slave default slave
    * @return Afx_Module_Abstract if success else NULL
    */
   
   public function getOne($id,$server='slave'){
     if(!is_numeric($id)){
         return NULL;
     }
     $sql=sprintf('SELECT * FROM '.$this->_tablename.' WHERE id=%d',$id);
     $arr=$this->getAdapter()->execute($sql,$this->_tablename,$server);
     if($arr&&is_array($arr[0])){
         $this->_obj=self::fromArray($arr[0]);;
         $this->setProperties($arr[0]);
         return $this;
     }
     return NULL;
   }   
   /**
    * Find One Object from database use specific key and value 
    * @param string $k  
    * @param  mixed $v  
    * @param  string $server master|slave default slave
    * @return Afx_Module_Abstract
    */
   
   
   public function findOne($k,$v,$server='slave'){
       if(empty($k)||empty($v)){
           return NULL;
       }
       if(is_string($v)){    
       $sql=sprintf('SELECT * FROM '.$this->_tablename." WHERE %s=%s",$k,$this->getAdapter()->quote($v,PDO::PARAM_STR));
       }elseif (is_numeric($v)){
       $sql=sprintf('SELECT * FROM '.$this->_tablename." WHERE %s=%d",$k,$this->getAdapter()->quote($v));   
       }
      $arr=$this->getAdapter()->execute($sql,$this->_tablename,$server);
      if($arr&&is_array($arr[0])){
           $this->_obj=self::fromArray($arr[0]);
           $this->setProperties($arr[0]);
           return $this;
      }
      return NULL;
   }
   /**
    * find An List from the Database
    * @param array $options
    * @param int $limit
    * @param int $offset
    * @param  string $server master|slave default slave
    * @return array
    */
   
   public function findList($options=array(),$limit=1000,$offset=0,$server='slave'){
       if(!count($options)){
           return array();
       }
       $sql='SELECT * FROM '.$this->_tablename." WHERE ";
       foreach ($options as  $k=>$v){
           if(is_string($v)){    
           $sql.=$k."=".$this->getAdapter()->quote($v,PDO::PARAM_STR).",";
           }elseif(is_numeric($v)){
           $sql.=$k."=".$this->getAdapter()->quote($v).",";
           }
       }
       $sql=substr($sql, 0,strlen($sql)-1);
       if(is_numeric($offset)&&is_numeric($limit)){
           if($offset==0)
           $sql .=" limit $limit";
           else
           $sql .=" limit $offset,$limit";
       }
       return $this->getAdapter()->execute($sql,$this->_tablename,$server);
       
   }
   
   /**
    * get a list from the database 
    * @param int $offset
    * @param int $limit
    * @param  string $server master|slave default slave
    * @return array
    */
   public function getList($offset=0,$limit=1000,$server='slave'){
       $sql=sprintf('SELECT * FROM '.$this->_tablename." LIMIT $offset,$limit ");
       return $this->getAdapter()->execute($sql,$this->_tablename,$server);
   }
   
}
