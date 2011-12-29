<?php
/**
 * Yaf_Request_Abstract
 * @since 2.0
*/

class  Yaf_Request_Abstract{

/**
 * public Yaf_Request_Abstract::isGet(void)
 * @param 
 * @return 
 * @since 2.0
*/

public  function isGet(){}

/**
 * public Yaf_Request_Abstract::isPost(void)
 * @param 
 * @return 
 * @since 2.0
*/

public  function isPost(){}

/**
 * public Yaf_Request_Abstract::isPut(void)
 * @param 
 * @return 
 * @since 2.0
*/

public  function isPut(){}

/**
 * public Yaf_Request_Abstract::isHead(void)
 * @param 
 * @return 
 * @since 2.0
*/

public  function isHead(){}

/**
 * public Yaf_Request_Abstract::isOptions(void)
 * @param 
 * @return 
 * @since 2.0
*/

public  function isOptions(){}

/**
 * public Yaf_Request_Abstract::isCli(void)
 * @param 
 * @return 
 * @since 2.0
*/

public  function isCli(){}

/**
 * public Yaf_Request_Abstract::isXmlHttpRequest(void) 
 * @param 
 * @return 
 * @since 2.0
*/

public  function isXmlHttpRequest(){}

/**
 * public Yaf_Request_Abstract::getEnv(mixed $name, mixed $default = NULL)
 * @param mixed $name
 * @param mixed $default
 * @return 
 * @since 2.0
*/

public  function getEnv(mixed $name, mixed $default = NULL){}

/**
 * public Yaf_Request_Abstract::getServer(mixed $name, mixed $default = NULL)
 * @param mixed $name
 * @param mixed $default
 * @return 
 * @since 2.0
*/

public  function getServer(mixed $name, mixed $default = NULL){}

/**
 * public Yaf_Request_Abstract::getModuleName(void) 
 * @param 
 * @return 
 * @since 2.0
*/

public  function getModuleName(){}

/**
 * public Yaf_Request_Abstract::getControllerName(void) 
 * @param 
 * @return 
 * @since 2.0
*/

public  function getControllerName(){}

/**
 * public Yaf_Request_Abstract::getActionName(void) 
 * @param 
 * @return 
 * @since 2.0
*/

public  function getActionName(){}

/**
 * public Yaf_Request_Abstract::setModuleName(string $module) 
 * @param string $module
 * @return 
 * @since 2.0
*/

public  function setModuleName(string $module){}

/**
 * public Yaf_Request_Abstract::setControllerName(string $controller) 
 * @param string $controller
 * @return 
 * @since 2.0
*/

public  function setControllerName(string $controller){}

/**
 * public Yaf_Request_Abstract::setActionName(string $action) 
 * @param string $action
 * @return 
 * @since 2.0
*/

public  function setActionName(string $action){}

/**
 * public Yaf_Request_Abstract::setParam(mixed $value)
 * @param mixed $value
 * @return 
 * @since 2.0
*/

public  function setParam(mixed $value){}

/**
 * public Yaf_Request_Abstract::getParam(string $name, $mixed $default = NULL)
 * @param string $name
 * @param mixed $default
 * @return 
 * @since 2.0
*/

public  function getParam(string $name, mixed $default = NULL){}

/**
 * public Yaf_Request_Abstract::getException(void)
 * @param 
 * @return 
 * @since 2.0
*/

public  function getException(){}

/**
 * public Yaf_Request_Abstract::getParams(void)
 * @param 
 * @return 
 * @since 2.0
*/

public  function getParams(){}

/**
 * public Yaf_Request_Abstract::getLanguage(void)
 * @param 
 * @return 
 * @since 2.0
*/

public  function getLanguage(){}

/**
 * public Yaf_Request_Abstract::getMethod(void)
 * @param 
 * @return 
 * @since 2.0
*/

public  function getMethod(){}

/**
 * public Yaf_Request_Abstract::isDispatched(void)
 * @param 
 * @return 
 * @since 2.0
*/

public  function isDispatched(){}

/**
 * public Yaf_Request_Abstract::setDispatched(void)
 * @param 
 * @return 
 * @since 2.0
*/

public  function setDispatched(){}

/**
 * public Yaf_Request_Abstract::setBaseUri(string $name)
 * @param string $name
 * @return 
 * @since 2.0
*/

public  function setBaseUri(string $name){}

/**
 * public Yaf_Request_Abstract::getBaseUri(string $name)
 * @param string $name
 * @return 
 * @since 2.0
*/

public  function getBaseUri(string $name){}

/**
 * public Yaf_Request_Abstract::getRequestUri(string $name)
 * @param string $name
 * @return 
 * @since 2.0
*/

public  function getRequestUri(string $name){}

/**
 * public Yaf_Request_Abstract::setRequestUri(string $name)
 * @param string $name
 * @return 
 * @since 2.0
*/

public  function setRequestUri(string $name){}

/**
 * public Yaf_Request_Abstract::isRouted(void) 
 * @param 
 * @return 
 * @since 2.0
*/

public  function isRouted(){}

/**
 * public Yaf_Request_Abstract::setRouted(void)
 * @param 
 * @return 
 * @since 2.0
*/

public  function setRouted(){}

}