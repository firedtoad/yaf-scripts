<?php
/**
 * @version $Id: Adapter.php 94 2012-12-10 03:39:40Z zhujinghe $
 * @author zhangwenhao 
 *
 */
interface Afx_Db_Adapter
{

    public static function Instance ();

    public static function debug ($bool);

    public function setConfig ($config);

    public function getConfig ();

    public function selectDatabase ($dbname);

    public function startTrans ();

    public function rollBack ();

    public function commit ();

    public function getLastSql ();

    public function quote ($v, $type);
    
    /**
     * @param string $sql
     * @return Afx_Db_Result
     */
    public function execute ($sql);
}