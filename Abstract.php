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
   
   public function delete($id=NULL){
      $rid=$id?($id>=0?$id:NULL):$this->id?($this->id>=0?$this->id:NULL):NULL;
      if(!$rid){
          return FALSE;
      }
      $sql=sprintf('DELETE FROM '.$this->_tablename.' WHERE id=%d',$rid);
      return $this->getAdapter()->execute($sql);
   }
   
   /**
    * 
    * Enter description here ...
    * @param long $id
    * @return bool
    */
   public function from_db($id){
     $sql=sprintf('SELECT * FROM '.$this->_tablename.' WHERE id=%d',$id);
     $arr=$this->getAdapter()->execute($sql);
     if($arr&&is_array($arr)){
         $this->_obj=self::fromArray($arr);;
         $this->setProperties($arr);
         return TRUE;
     }
     return FALSE;
   }   
}
