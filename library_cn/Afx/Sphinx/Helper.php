<?php
require_once dirname(__FILE__) . '/sphinxapi.php';
class Afx_Sphinx_Helper
{
    /**
     * @var SphinxClient
     */
    private  $_sphinx = NULL;
    private $_host='127.0.0.1';
    private $port=9312;
    function __call ($method, $param)
    {
        //Afx_Debug_Helper::print_r(func_get_args());
       if(method_exists($this->_sphinx, $method))
       {
           return  call_user_method_array($method, $this->_sphinx,$param);
          //return $this->_sphinx->$method($param);
       }
    }
    function __construct ($host,$port,$timeout)
    {
      $this->_sphinx=new SphinxClient();
      $this->_sphinx->SetConnectTimeout($timeout);
      $this->_sphinx->SetServer($host,$port);
    }
    function __destruct ()
    {}
    /**
     * @param string $host
     * @param int $port
     * @return Afx_Sphinx_Helper
     */
    public static function Instance($host,$port,$timeout=3){
       return new  self($host,$port,$timeout);
    }

}