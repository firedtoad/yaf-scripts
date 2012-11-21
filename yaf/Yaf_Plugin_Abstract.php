<?php
/**
 * Yaf_Plugin_Abstract
 * @since 2.0
 */
class Yaf_Plugin_Abstract
{

    /**
     * public Yaf_Plugin::routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstarct $response)
     * @param Yaf_Request_Abstract $request
     * @param Yaf_Response_Abstarct $response
     * @return 
     * @since 2.0
     */
    public function routerStartup (Yaf_Request_Abstract $request, Yaf_Response_Abstarct $response)
    {
    }

    /**
     * public Yaf_Plugin::routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstarct $response)
     * @param Yaf_Request_Abstract $request
     * @param Yaf_Response_Abstarct $response
     * @return 
     * @since 2.0
     */
    public function routerShutdown (Yaf_Request_Abstract $request, Yaf_Response_Abstarct $response)
    {
    }

    /**
     * public Yaf_Plugin::dispatchLoopStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstarct $response)
     * @param Yaf_Request_Abstract $request
     * @param Yaf_Response_Abstarct $response
     * @return 
     * @since 2.0
     */
    public function dispatchLoopStartup (Yaf_Request_Abstract $request, Yaf_Response_Abstarct $response)
    {
    }

    /**
     * public Yaf_Plugin::preDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstarct $response)
     * @param Yaf_Request_Abstract $request
     * @param Yaf_Response_Abstarct $response
     * @return 
     * @since 2.0
     */
    public function preDispatch (Yaf_Request_Abstract $request, Yaf_Response_Abstarct $response)
    {
    }

    /**
     * public Yaf_Plugin::postDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstarct $response)
     * @param Yaf_Request_Abstract $request
     * @param Yaf_Response_Abstarct $response
     * @return 
     * @since 2.0
     */
    public function postDispatch (Yaf_Request_Abstract $request, Yaf_Response_Abstarct $response)
    {
    }

    /**
     * public Yaf_Plugin::dispatchLoopShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstarct $response)
     * @param Yaf_Request_Abstract $request
     * @param Yaf_Response_Abstarct $response
     * @return 
     * @since 2.0
     */
    public function dispatchLoopShutdown (Yaf_Request_Abstract $request, Yaf_Response_Abstarct $response)
    {
    }

    /**
     * public Yaf_Plugin::preResponse(Yaf_Request_Abstract $request, Yaf_Response_Abstarct $response)
     * @param Yaf_Request_Abstract $request
     * @param Yaf_Response_Abstarct $response
     * @return 
     * @since 2.0
     */
    public function preResponse (Yaf_Request_Abstract $request, Yaf_Response_Abstarct $response)
    {
    }
}