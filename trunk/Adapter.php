<?php
class Afx_Db_Excetion extends Exception
{
}
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
     * @var array
     */
    private  static $options = array();
    
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
 
    public static function initOption($arr=array()){        
        self::$options=$arr;
    }
    
    public static function getOptions(){
        return self::$options;
    }
    
    public static function instance(){
        if(NULL===self::$instance){
            self::$instance=new self();
        }
        return self::$instance;
    }
    

    private function __construct ()
    {
//        var_dump(self::$options);
        if (count(self::$options) == 0) {
            throw new Afx_Db_Excetion('no  configuration found!', 404);
        }
        
        if(!isset(self::$options['db'])){
            throw new Afx_Db_Excetion('no db  configuration found!', 404);
        }
        
        if(!isset(self::$options['db']['master'])||!is_array(self::$options['db']['master'])){
            throw new Afx_Db_Excetion('no master db configuration found!', 404);
        }
        
        $master=self::$options['db']['master'];
        $slave=isset(self::$options['db']['slave'])&&is_array(self::$options['db']['slave'])?self::$options['db']['slave']:self::$options['db']['master'];
        $keys = array('host' => 1, 'user' => 1, 'password' => 1, 'port' => 1, 
        'dbname' => 1,'charset'=>1);
        $nokeys = array_diff_key($keys, $master);
        if (count($nokeys)) {
            foreach ($nokeys as $k => $v) {
                throw new Afx_Db_Excetion("no $k found in db master configuration!", 
                404);
            }
        }
        $nokeys = array_diff_key($keys, $slave);
        if (count($nokeys)) {
            foreach ($nokeys as $k => $v) {
                throw new Afx_Db_Excetion("no $k found in db slave configuration!",  404);
            }
        }
        
    echo  self::$read_dsn= 'mysql:host=' . $master['host'] . ';port=' .$master['port'] . ';dbname=' . $master['dbname'].';charset='.$master['charset'].';',"\n";
    echo  self::$write_dsn= 'mysql:host=' . $slave['host'] . ';port=' .$slave['port'] . ';dbname=' . $slave['dbname'].';charset='.$slave['charset'].';',"\n";
        
        try{
          self::$link_read = new PDO(self::$read_dsn, $master['user'],$master['password'],array(PDO::ATTR_TIMEOUT=>1));
          self::$link_write = new PDO(self::$write_dsn, $slave['user'],$slave['password'],array(PDO::ATTR_TIMEOUT=>1));
        var_dump(self::$link_read->errorInfo());
        var_dump(self::$link_write->errorInfo());
        }catch(PDOException $e){
            throw new Exception($e) ;
        }
    }
    public function execute ($sql)
    {
        echo $sql,"\n";
        if (strncasecmp($sql, 'select', 6) == 0) {
            try{
             $statment=self::$link_read->prepare($sql);
             
             if($statment instanceof  PDOStatement){
                $statment->execute();
             if($statment->errorCode()!='00000'){
                    throw new PDOException(implode('',$statment->errorInfo()), $statment->errorCode());
              }
                $obj= $statment->fetch(PDO::FETCH_ASSOC);
              if($statment->errorCode()!='00000'){
                    throw new PDOException(implode('',$statment->errorInfo()), $statment->errorCode());
              }
                return $obj;
            }
            }catch(PDOException $e){
                 throw new Exception($e) ;
            }
            
        } else {
            $ret = TRUE;
            try {
                self::$link_write->beginTransaction();
                self::$link_write->exec($sql);
                self::$link_write->commit();
                if(self::$link_write->errorCode()!='00000'){
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
