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
 * @package Afx
 * @version $Id Module.php
 * The Module Class Impliment The Core ORM CRUD Operator
 * @author Afx team && firedtoad@gmail.com &&dietoad@gmail.com
 */
class Afx_Module extends Afx_Module_Abstract
{
    /**
     * create the module object using the given table name
     * @param string $tablename
     * @return Afx_Module
     */
    public function __construct ($tablename = 'tablename')
    {
      $this->_tablename=$tablename;
    }
}