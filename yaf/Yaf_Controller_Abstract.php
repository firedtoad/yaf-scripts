<?php
/**
 * Yaf_Controller_Abstract
 * @since 2.0
 */
class Yaf_Controller_Abstract
{

    /**
     * public Yaf_Controller_Abstract::init()
     * @param 
     * @return 
     * @since 2.0
     */
    public function init ()
    {
    }

    /**
     * protected Yaf_Controller_Abstract::__construct(Yaf_Request_Abstract $request, Yaf_Response_abstrct $response, Yaf_View_Interface $view, array $invokeArgs = NULL)
     * @param Yaf_Request_Abstract $request
     * @param Yaf_Response_abstrct $response
     * @param Yaf_View_Interface $view
     * @param array $invokeArgs
     * @return 
     * @since 2.0
     */
    protected function __construct (Yaf_Request_Abstract $request, Yaf_Response_abstrct $response, Yaf_View_Interface $view, array $invokeArgs = NULL)
    {
    }

    /**
     * public Yaf_Controller_Abstract::getView(void)
     * @param 
     * @return Yaf_View_Interface
     * @since 2.0
     */
    public function getView ()
    {
    }

    /**
     * public Yaf_Controller_Abstract::getRequest(void)
     * @param 
     * @return  Yaf_Request_Http
     * @since 2.0
     */
    public function getRequest ()
    {
    }

    /**
     * public Yaf_Controller_Abstract::getResponse(void)
     * @param 
     * @return  Yaf_Response_Abstract
     * @since 2.0
     */
    public function getResponse ()
    {
    }

    /**
     * public Yaf_Controller_Abstract::initView(array $options = NULL)
     * @param array $options
     * @return 
     * @since 2.0
     */
    public function initView (array $options = NULL)
    {
    }

    /**
     * public Yaf_Controller_Abstract::getInvokeArg(string $name)
     * @param string $name
     * @return 
     * @since 2.0
     */
    public function getInvokeArg (string $name)
    {
    }

    /**
     * public Yaf_Controller_Abstract::getInvokeArgs(void)
     * @param 
     * @return 
     * @since 2.0
     */
    public function getInvokeArgs ()
    {
    }

    /**
     * public Yaf_Controller_Abstract::getModuleName(void)
     * @param 
     * @return 
     * @since 2.0
     */
    public function getModuleName ()
    {
    }

    /**
     * public Yaf_Controller_Abstract::setViewpath(string $view_directory)
     * @param string $view_directory
     * @return 
     * @since 2.0
     */
    public function setViewpath (string $view_directory)
    {
    }

    /**
     * public Yaf_Controller_Abstract::getViewpath(void)
     * @param 
     * @return 
     * @since 2.0
     */
    public function getViewpath ()
    {
    }

    /**
     * public Yaf_Controller_Abstract::forward($module, $controller, $action, $args = NULL)
     * @param $module
     * @param $controller
     * @param $action
     * @param $args =
     * @return 
     * @since 2.0
     */
    public function forward ($module, $controller, $action, $args = NULL)
    {
    }

    /**
     * public Yaf_Controller_Abstract::redirect(string $url)
     * @param string $url
     * @return 
     * @since 2.0
     */
    public function redirect (string $url)
    {
    }

    /**
     * protected Yaf_Controller_Abstract::render(string $action, array $var_array = NULL)
     * @param string $action
     * @param array $var_array
     * @return 
     * @since 2.0
     */
    protected function render (string $action, array $var_array = NULL)
    {
    }

    /**
     * protected Yaf_Controller_Abstract::display(string $action, array $var_array = NULL)
     * @param string $action
     * @param array $var_array
     * @return 
     * @since 2.0
     */
    protected function display (string $action, array $var_array = NULL)
    {
    }

    /**
     * private Yaf_Controller_Abstract::__clone()
     * @param 
     * @return 
     * @since 2.0
     */
    private function __clone ()
    {
    }
}