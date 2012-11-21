<?php
interface Afx_Db_Adapter
{

    //    protected static $_instance;
    //    const _instance=1;
    public static function Instance ();

    public static function debug ($bool);

    public function setConfig ($config);

    public function selectDatabase ($dbname);

    public function startTrans ();

    public function rollBack ();

    public function commit ();

    public function getLastSql ();

    public function quote ($v, $type);

    /**
     * @param string $sql
     * @return Afx_Db_Mysqli_Result
     */
    public function execute ($sql);
}