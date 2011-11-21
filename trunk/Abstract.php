<?php
class Afx_Module_Abstract{
    
    /**
     * 
     * Enter description here ...
     * @var Afx_Db_Adapter
     */
    
    protected  $_adapter=NULL;
    
    /**
     * 
     * Enter description here ...
     * @var stdClass
     */
    
    private $_obj;
    
    /**
     * 
     * Enter description here ...
     * @var Afx_Module_Abstract::
     */
    
    protected static  $_instance=NULL;
    
   

    /**
     * @return stdClass 
     */
    public function getObj ()
    {
        return $this->_obj;
    }

	/**
     * @param stdClass $_obj
     */
    public function setObj ($_obj)
    {
        $this->_obj = $_obj;
    }
     /**
     * 
     * Enter description here ...
     * @return Afx_Db_Adapter
     */

	public function getAdapter ()
    {
            
        return $this->_adapter;

    }
    
    public function __set($name,$value){
        self::$_instance->$name=$value;
    }
    
    public function __get($name){
        return self::$_instance->$name;
    }
    
    public function setAdapter ($_adapter)
    {
        $this->_adapter = $_adapter;
    }

	protected  function __construct(){
	    $this->_adapter=Afx_Db_Adapter::instance();
    }
    
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
   * @param void
   * @return TRUE if success else FALSE
   */
  
   public function save(){
      $sql='';
       if($this->_obj===NULL){
       $sql='INSERT INTO '.$this->_tablename.' (';
              $arr=self::toArray($this);
              if(!is_array($arr)){
                  return FALSE;
              }
              foreach ($arr as $k=>$v){
                  if(strncasecmp($k, '_', 1)==0||strncasecmp($k, 'id', 2)==0){
                      unset($arr[$k]);
                      continue;
                  }
              }
              
             if(!count($arr))return FALSE;
             $keystr= implode(',',array_keys($arr));
             $sql.=substr($keystr, 0,strlen($keystr));
             $sql.=') VALUES(';
             foreach ($arr as $k=>$v){
                    if(is_string($v)){
                        $sql.="'$v',";
                    }else{
                        $sql.="$v,";
                    }
             }
             $sql=substr($sql, 0,strlen($sql)-1);
             $sql.=')';
             
           return $this->getAdapter()->execute($sql);
          }
          if($this->_obj&& $this->_obj instanceof stdClass){
              $sql ='UPDATE '.$this->_tablename ;
              $arr_old=self::toArray($this->_obj);
              $arr_this=self::toArray($this);
              if(is_array($arr_old)&&is_array($arr_this)){
              $arr_update=array_intersect_key($arr_this, $arr_old);
              $arr_real_update=array_diff($arr_update, $arr_old);
              if(count($arr_real_update)){
                   $sql.=' set '; 
                   foreach ($arr_real_update as $k=>$v){
                       if(is_string($v)){
                           $sql.="$k='$v',";
                       }elseif (is_numeric($v)){
                           $sql.="$k=$v,";
                       }
                    }
                    $sql=substr($sql,0,strlen($sql)-1);
                    if(isset($arr_old['id'])){
                        $sql.=sprintf(' where id=%d',$arr_old['id']);
                       return $this->getAdapter()->execute($sql);
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
    * 
    * delete the object in the database
    * @param int $id
    * @return TRUE if success else FALSE
    */
   
   
   public function delete($id=NULL){
      $rid=$id?($id>=0?$id:NULL):$this->_obj->id?($this->_obj->id>=0?$this->_obj->id:NULL):NULL;
      echo '<pre>';
      var_dump($this);
      if(!$rid){
          return FALSE;
      }
      $sql=sprintf('DELETE  FROM '.$this->_tablename.' WHERE id=%d',$rid);
      
      return $this->getAdapter()->execute($sql);
   }
   
   /**
    * 
    * select an object from the database id is the specific id
    * @param long $id
    * @return Afx_Module_Abstract if success else NULL
    */
   
   public function from_db($id){
     $sql=sprintf('SELECT * FROM '.$this->_tablename.' WHERE id=%d',$id);
     $arr=$this->getAdapter()->execute($sql);
     if($arr&&is_array($arr[0])){
         $this->_obj=self::fromArray($arr[0]);;
         $this->setProperties($arr[0]);
         return $this;
     }
     return NULL;
   }   
   
   public function getList($offset=0,$limit=1000){
       $sql=sprintf('SELECT * FROM '.$this->_tablename." LIMIT $offset,$limit ");
       return $this->getAdapter()->execute($sql);
   }
   
}
