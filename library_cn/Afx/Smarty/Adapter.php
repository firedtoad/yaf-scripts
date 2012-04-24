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
 * smarty 集成
 * @package Afx_Smarty
 * @version $Id Adapter.php
 * The Smarty View Adapter implements the Yaf_View_Interface
 * @author Afx team && firedtoad@gmail.com &&dietoad@gmail.com
 */
class Afx_Smarty_Adapter implements Yaf_View_Interface
{
    /**
     * The Smarty Object
     * @var Smarty
     */
    protected $_smarty = NULL;
    /**
     * Enter description here ...
     * @param string $tmplPath
     * @param array $config
     */
    function __construct ($tmplPath = null, $config = array())
    {
        require_once 'Smarty.class.php';
        $this->_smarty = new Smarty();
        if (null !== $tmplPath) {
            $this->setScriptPath($tmplPath);
        }
        if (isset($config['cache_dir'])) {
            $this->_smarty->setCacheDir($config['cache_dir']);
        }
        if (isset($config['template_dir'])) {
            $this->_smarty->setTemplateDir($config['template_dir']);
        }
        if (isset($config['compile_dir'])) {
            $this->_smarty->setCompileDir($config['compile_dir']);
        }
        if (isset($config['cache']) && $config['cache'] == TRUE) {
            $this->_smarty->caching = $config['cache'];
        }
        if (isset($config['debug']) && $config['debug'] == TRUE) {
            $this->_smarty->debugging = $config['debug'];
        }
    }
    /**
     *
     * @param string $key
     * @param mixed $value
     */
    public function assign ($key, $value = NULL)
    {
        if (is_array($key)) {
            $this->_smarty->assign($key);
            return;
        }
        $this->_smarty->assign($key, $value);
    }
    /**
     * render the view file
     * @param string $tpl
     * @param array $vars
     */
    public function render ($tpl, $vars = array())
    {
        return $this->_smarty->fetch($tpl);
    }
    /**
     * display the view file
     * @param string $tpl
     * @param array $vars
     */
    public function display ($tpl, $vars = array())
    {
        return $this->_smarty->display($tpl);
    }
    /**
     * set the view path Notice we don't use the yaf default view path
     * we do nothing here
     * @param string $template_dir
     */
    public function setScriptPath ($template_dir)
    {
        $this->__script_path = $template_dir;
         //        echo "erer$template_dir";
    //        $this->_smarty->setTemplateDir($template_dir);
    }
    /**
     * get the smarty view path
     * @return string
     */
    public function getScriptPath ()
    {
        $this->_smarty->getTemplateDir();
    }
    /**
     * debug for help
     */
    public function debug ()
    {
        $this->_smarty->display('debug.tpl');
    }
    function __destruct ()
    {}
}
?>