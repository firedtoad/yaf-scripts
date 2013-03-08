<?php
/**
 * @version $Id: Factory.php 94 2012-12-10 03:39:40Z zhujinghe $
 * @author zhangwenhao 
 *
 */
class Afx_Db_Factory
{
    const DB_MYSQL='mysql';
    const DB_MYSQLI='mysqli';
    const DB_PDO='pdo';
    /**
     * @param $type
     * @return Afx_Db_Adapter
     */
    public static function DbDriver($type=self::DB_MYSQLI,$create=FALSE)
    {
        $driver=NULL;
        switch ($type) {
            case self::DB_MYSQL:
            $driver=Afx_Db_Mysql_Adapter::Instance($create);
            break;
            case self::DB_MYSQLI:
            $driver=Afx_Db_Mysqli_Adapter::Instance($create);
            break;
            case self::DB_PDO:
            $driver=Afx_Db_Pdo_Adapter::Instance($create);
            break;
            default:break;
        }
        return $driver;
    }
}