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
abstract class Afx_Module_Abstract
{
    protected $_tablename = 't_dummy';
    /**
     * 
     * @var Afx_Db_Adapter
     */
    protected static $_adapter = NULL;
    /**
     * store the Object fetch from database and compare for update
     * @var stdClass
     */
    private $_obj;
    /**
     * @var int $_limit the limit of per resultset
     */
    protected $_limit = 100;
    /**
     * store the condition used by the sql statment
     * @var array
     */
    protected $_where = array();
    /**
     * 
     * store the fields used by the sql statment
     * @var array()
     */
    protected $_field = array();
    /**
     * store the sql statment
     * @var string $_sql
     */
    protected $_sql = NULL;
    /**
     * start offset
     * @var int
     */
    protected $_offset = 0;
    /**
     * like condition
     * @var string
     */
    protected $_like = NULL;
    /**
     * store the or condition
     * @var array
     */
    protected $_wor = array();
    /**
     * store the join condition
     * @var array
     */
    protected $_join = array();
    /**
     * the on condition
     * @var array
     */
    protected $_on = array();
    /**
     * @var string order
     */
    protected $_order = NULL;
    /**
     * The Only Instance 
     * @var Afx_Module_Abstract::
     */
    protected static $_instance = NULL;
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
        return self::$_adapter;
    }
    /**
     * Magic function __set
     * @param string $name
     * @param mixed $value
     */
    public function __set ($name, $value)
    {
        $this->$name = $value;
    }
    /**
     * 
     * Magic function __set
     * @param string $name
     * @retunr  mixed
     */
    public function __get ($name)
    {
        //        return self::$_instance->$name;
        return $this->$name;
    }
    /**
     * Set the Db Adapter
     * @param Afx_Db_Adapter $_adapter
     */
    public static function setAdapter ($_adapter)
    {
        self::$_adapter = $_adapter;
    }
    /**
     * constructor function 
     * Here We use Default Db Adapter
     */
    public function __construct ()
    {}
    /**
     * Convert An Array To A stdClass 
     * @param array $arr
     * @return stdClass
     */
    public static function fromArray ($arr = array())
    {
        if (count($arr)) {
            $obj = new stdClass();
            foreach ($arr as $k => $v) {
                $obj->$k = $v;
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
    public static function toArray ($obj = stdClass)
    {
        $arr = array();
        if (! is_object($obj)) {
            return NULL;
        }
        foreach ($obj as $k => $v) {
            $arr[$k] = $v;
        }
        return count($arr) ? $arr : NULL;
    }
    /**
     * apply an array keys as self properties
     * @param array $arr
     */
    public function setProperties ($arr = array())
    {
        if (! is_array($arr)) {
            return;
        }
        foreach ($arr as $k => $v) {
            $this->$k = $v;
        }
    }
    /**
     * save an object to the database
     * the most complicated function 
     * @param void
     * @return TRUE if success else FALSE
     */
    public function save ()
    {
        $sql = '';
        if ($this->_obj === NULL) {
            /*
         * the _obj is null so the object is new construct
         * we insert the object to the database
         */
            $sql = 'INSERT INTO ' . $this->_tablename . ' (';
            $arr = self::toArray($this);
            if (! is_array($arr)) {
                return FALSE;
            }
            foreach ($arr as $k => $v) {
                if (strncasecmp($k, '_', 1) == 0 || strncasecmp($k, 'id', 2) == 0) {
                    //filter the id and startwith '_' properties
                    //because most of the table will have the id auto_increment 
                    //and nearly no table column name start with '_' so we filter them                      
                    unset(
                    $arr[$k]);
                    continue;
                }
            }
            if (! count($arr))
                return FALSE;
                 //concat the keys
            $keystr = implode(',', array_keys($arr));
            //here we add ',' more one time so drop it
            $sql .= substr($keystr, 0, strlen($keystr));
            $sql .= ') VALUES(';
            //concat the values
            foreach ($arr as $k => $v) {
                //here we only deal with string and numeric  
                //we need deal others future
                if (is_string($v)) {
                    $sql .= $this->getAdapter()->quote($v, PDO::PARAM_STR) . ",";
                } else {
                    $sql .= $this->getAdapter()->quote($v) . ",";
                }
            }
            //here we add ',' more one time so drop it again
            $sql = substr($sql, 0, strlen($sql) - 1);
            $sql .= ')';
            return $this->getAdapter()->execute($sql, $this->_tablename);
        }
        if ($this->_obj && $this->_obj instanceof stdClass) {
            //the _obj exists  it is fetch from the object
            //so update it
            $sql = 'UPDATE ' . $this->_tablename;
            $arr_old = self::toArray($this->_obj);
            $arr_this = self::toArray($this);
            if (is_array($arr_old) && is_array($arr_this)) {
                //calculate  the properties need te be update
                $arr_update = array_intersect_key($arr_this, 
                $arr_old);
                $arr_real_update = array_diff($arr_update, $arr_old);
                if (count($arr_real_update)) {
                    $sql .= ' set ';
                    foreach ($arr_real_update as $k => $v) {
                        //here we only deal with string and numeric  
                        //we need deal others future
                        if (is_string(
                        $v)) {
                            $sql .= $k . "=" .
                             $this->getAdapter()->quote($v, PDO::PARAM_STR) . ",";
                        } elseif (is_numeric($v)) {
                            $sql .= $k . "=" . $this->getAdapter()->quote($v) .
                             ",";
                        }
                    }
                    //',' more add again drop it
                    $sql = substr($sql, 0, strlen($sql) - 1);
                    if (isset($arr_old['id'])) {
                        $sql .= sprintf(' where id=%d', $arr_old['id']);
                        return $this->getAdapter()->execute($sql, 
                        $this->_tablename);
                    } else {
                        return FALSE;
                    }
                }
                return FALSE;
            } else {
                return FALSE;
            }
        }
    }
    /**
     * delete the object in the database
     * @param int $id
     * @return TRUE if success else FALSE
     */
    public function delete ($id = NULL)
    {
        $rid = $id ? ($id >= 0 ? $id : NULL) : $this->_obj->id ? ($this->_obj->id >=
         0 ? $this->_obj->id : NULL) : NULL;
        if (! $rid) {
            return FALSE;
        }
        $sql = sprintf('DELETE  FROM ' . $this->_tablename . ' WHERE id=%d', 
        $rid);
        return $this->getAdapter()->execute($sql, $this->_tablename);
    }
    /**
     * select an object from the database id is the specific id
     * @param long $id
     * @param  Boolean $master whether operator the master
     * @return Afx_Module_Abstract if success else NULL
     */
    public function getOne ($id, $master = FALSE)
    {
        if (! is_numeric($id)) {
            return NULL;
        }
        $sql = sprintf('SELECT * FROM ' . $this->_tablename . ' WHERE id=%d', 
        $id);
        $arr = $this->getAdapter()->execute($sql, $this->_tablename, $master);
        if ($arr && is_array($arr[0])) {
            $this->_obj = self::fromArray($arr[0]);
            ;
            $this->setProperties($arr[0]);
            return $this;
        }
        return NULL;
    }
    /**
     * Find One Object from database use specific key and value 
     * @param string $k  
     * @param  mixed $v  
     * @param  Boolean $master whether operator the master
     * @return Afx_Module_Abstract
     */
    public function findOne ($k, $v, $master = FALSE)
    {
        if (empty($k) || empty($v)) {
            return NULL;
        }
        if (is_string($v)) {
            $sql = sprintf(
            'SELECT * FROM ' . $this->_tablename . " WHERE %s=%s", $k, 
            $this->getAdapter()->quote($v, PDO::PARAM_STR));
        } elseif (is_numeric($v)) {
            $sql = sprintf(
            'SELECT * FROM ' . $this->_tablename . " WHERE %s=%d", $k, 
            $this->getAdapter()->quote($v));
        }
        $arr = $this->getAdapter()->execute($sql, $this->_tablename, $master);
        if ($arr && is_array($arr[0])) {
            $this->_obj = self::fromArray($arr[0]);
            $this->setProperties($arr[0]);
            return $this;
        }
        return NULL;
    }
    /**
     * select the result from database use conditions
     * @param int $limit
     * @param int $offset
     * @param Boolean $master
     */
    public function select ($limit = 100, $offset = 0, $master = FALSE)
    {
        $limit = $this->_limit > $limit ? $this->_limit : $limit;
        $sql = 'SELECT ';
        $hasWhere = 0;
        if (! $this->_sql) {
            if (! is_array($this->_field) || count($this->_field) == 0) {
                $sql .= "*";
            } elseif (count($this->_field)) {
                foreach ($this->_field as $k => $v) {
                    $sql .= $v . ",";
                }
            }
            $lastIndex = strrpos($sql, ',');
            if ($lastIndex > 0) {
                $sql = substr($sql, 0, $lastIndex);
            }
            $sql .= " FROM " . $this->_tablename;
            if (is_array($this->_join) && count($this->_join) > 0) {
                foreach ($this->_join as $k => $v) {
                    $sql .= " JOIN " . $v[0] . " ON " . $this->_tablename . "." .
                     $v[1] . "=" . $v[0] . "." . $v[2];
                }
            }
            if (is_array($this->_where) && count($this->_where) > 0) {
                $sql .= " WHERE ";
                $hasWhere = 1;
                foreach ($this->_where as $k => $v) {
                    if (is_string($v[1])) {
                        $sql .= "$k  " . $v[0] .
                         $this->getAdapter()->quote($v[1], PDO::PARAM_STR) .
                         " AND ";
                    } elseif (is_numeric($v[1])) {
                        $sql .= "$k " . $v[0] . $this->getAdapter()->quote(
                        $v[1]) . " AND ";
                    }
                }
                $lastIndex = strrpos($sql, 'AND');
                if ($lastIndex > 0) {
                    $sql = substr($sql, 0, $lastIndex);
                }
            }
            if (is_array($this->_wor) && count($this->_wor) > 0) {
                if (! $hasWhere) {
                    $sql .= ' WHERE ';
                } else {
                    $sql .= ' OR ';
                }
                foreach ($this->_wor as $k => &$v) {
                    if (is_string($v[1])) {
                        $sql .= "$k " . $v[0] .
                         $this->getAdapter()->quote($v[1], PDO::PARAM_STR) .
                         " OR ";
                    } else 
                        if (is_numeric($v)) {
                            $sql .= "$k " . $v[0] .
                             $this->getAdapter()->quote($v[1]) . " OR ";
                        }
                }
                $lastIndex = strrpos($sql, 'OR');
                if ($lastIndex > 0) {
                    $sql = substr($sql, 0, $lastIndex);
                }
            }
            if ($this->_order) {
                $sql .= $this->_order;
            }
            if ($this->_limit) {
                $sql .= " limit " . $this->_limit;
            }
        }
        $this->_where = array();
        $this->_join = array();
        $this->_on = array();
        $this->_like = NULL;
        $this->_field = NULL;
        $this->_wor = array();
        return $this->getAdapter()->execute($sql, $this->_tablename, $master);
    }
    /**
     * set limit
     * @param int $limit
     */
    public function limit ($limit = 100)
    {
        $this->_limit = $limit;
        return $this;
    }
    /**
     * set order
     * @param string $key
     * @param string $desc 
     */
    public function order ($key, $desc = 'DESC')
    {
        $this->_order = " ORDER BY $key $desc";
        return $this;
    }
    /**
     * set where condition
     * @param string $key
     * @param mixed $value
     * @param string $exp can be [>|<|!=|>=|<=|like|nlike]
     * @return Afx_Module_Abstract
     */
    public function where ($key = NULL, $value = NULL, $exp = '=')
    {
        static $allowExp = array('>' => 1, '<' => 1, '!=' => 1, '>=' => 1, 
        '<=' => 1, 'like' => 1, 'nlike' => 1);
        if (! $exp)
            $exp = '=';
        if (! isset($allowExp[$exp])) {
            return $this;
        }
        if ($key)
            $this->_where[$key] = array($exp, $value);
        return $this;
    }
    /**
     * set fetch field
     * @param array $fields
     */
    public function field ($fields = array())
    {
        if (count($fields)) {
            foreach ($fields as $k => $v) {
                $this->_field[$k] = $v;
            }
        }
        return $this;
    }
    /**
     * find A List from the Database
     * @param array $options
     * @param int $limit
     * @param int $offset
     * @param  Boolean $master whether operator the master
     * @return array
     */
    public function findList ($options = array(), $limit = 1000, $offset = 0, 
    $master = FALSE)
    {
        if (! count($options)) {
            return array();
        }
        $sql = 'SELECT * FROM ' . $this->_tablename . " WHERE ";
        foreach ($options as $k => $v) {
            if (is_string($v)) {
                $sql .= $k . "=" . $this->getAdapter()->quote($v, 
                PDO::PARAM_STR) . " AND ";
            } elseif (is_numeric($v)) {
                $sql .= $k . "=" . $this->getAdapter()->quote($v) . " AND ";
            }
        }
        $sql = substr($sql, 0, strlen($sql) - 4);
        if (is_numeric($offset) && is_numeric($limit)) {
            if ($offset == 0)
                $sql .= " limit $limit";
            else
                $sql .= " limit $offset,$limit";
        }
        return $this->getAdapter()->execute($sql, $this->_tablename, $master);
    }
    /**
     * get a list from the database 
     * @param int $offset
     * @param int $limit
     * @param  Boolean $master whether operator the masters
     * @return array 
     */
    public function getList ($offset = 0, $limit = 1000, $master = FALSE)
    {
        $sql = sprintf(
        'SELECT * FROM ' . $this->_tablename . " LIMIT $offset,$limit ");
        return $this->getAdapter()->execute($sql, $this->_tablename, $master);
    }
    /**
     * 
     * set offset
     * @param int $offset
     * @return Afx_Module_Abstract
     */
    public function offset ($offset)
    {
        $this->_offset = $offset;
        return $this;
    }
    /**
     * 
     * set like condition
     * @param string $key
     * @param mixed $value
     * @return Afx_Module_Abstract
    
    public function like ($key,$value)
    {
      if($key)
      $this->_like=$key ." LIKE %$value% ";
      return $this;
    }
     */
    /**
     * 
     * set join condition
     * @param string $table the table to join
     * @param string $lkey the left join key
     * @param string $rkey the right join key
     * @return Afx_Module_Abstract
     */
    public function join ($table, $lkey, $rkey)
    {
        if ($table)
            $this->_join[] = array($table, $lkey, $rkey);
        return $this;
    }
    /**
     * 
     * set or conditon 
     * @param string $key
     * @param mix $value
     * @param string $exp can be [>|<|!=|>=|<=|like|nlike]
     * @return Afx_Module_Abstract
     */
    public function wor ($key = NULL, $value = NULL, $exp = '=')
    {
        static $allowExp = array('>' => 1, '<' => 1, '!=' => 1, '>=' => 1, 
        '<=' => 1, 'like' => 1, 'nlike' => 1);
        if (! $exp)
            $exp = '=';
        if (! isset($allowExp[$exp])) {
            return $this;
        }
        if ($key)
            $this->_wor[$key] = array($exp, $value);
        return $this;
    }
    /**
     * for debug use
     */
    public function p ()
    {
        echo '<pre>';
        if (count(func_get_args())) {
            foreach (func_get_args() as $k => $v) {
                print_r($v);
            }
        }
        echo '</pre>';
    }
}
