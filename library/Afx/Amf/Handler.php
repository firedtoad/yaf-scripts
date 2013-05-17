<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Afx_Amf
 */

/**
 * This is the default handler for the gateway. It's job is to handle everything that is specific to Amf for the gateway.
 * 
 * @todo determine if indirection for serializer/deserializer necessary. Makes gateway code lighter, but is cumbersome 
 * @package Afx_Amf
 * @author Ariel Sommeria-Klein
 */
class Afx_Amf_Handler implements Afx_Amf_Common_IDeserializer, Afx_Amf_Common_IDeserializedRequestHandler, Afx_Amf_Common_IExceptionHandler, Afx_Amf_Common_ISerializer {
    /**
     * filter called for each amf request header, to give a plugin the chance to handle it.
     * Unless a plugin handles them, amf headers are ignored
     * Headers embedded in the serialized requests are regarded to be a Amf specific, so they get their filter in Amf Handler
     * @param Object $handler. null at call. Return if the plugin can handle
     * @param Afx_Amf_Header $header the request header
     * @todo consider an interface for $handler. Maybe overkill here
     */

    const FILTER_AMF_REQUEST_HEADER_HANDLER = 'FILTER_AMF_REQUEST_HEADER_HANDLER';

    /**
     * filter called for each amf request message, to give a plugin the chance to handle it.
     * This is for the Flex Messaging plugin to be able to intercept the message and say it wants to handle it
     * @param Object $handler. null at call. Return if the plugin can handle
     * @param Afx_Amf_Message $requestMessage the request message
     * @todo consider an interface for $handler. Maybe overkill here
     */
    const FILTER_AMF_REQUEST_MESSAGE_HANDLER = 'FILTER_AMF_REQUEST_MESSAGE_HANDLER';

    /**
     * filter called for exception handling an Amf packet/message, to give a plugin the chance to handle it.
     * This is for the Flex Messaging plugin to be able to intercept the exception and say it wants to handle it
     * @param Object $handler. null at call. Return if the plugin can handle
     * @todo consider an interface for $handler. Maybe overkill here
     */
    const FILTER_AMF_EXCEPTION_HANDLER = 'FILTER_AMF_EXCEPTION_HANDLER';

    /**
     * Amf specifies that an error message must be aimed at an end point. This stores the last message's response Uri to be able to give this end point
     * in case of an exception during the handling of the message. The default is '/1', because a response Uri is not always available
     * @var String
     */
    protected $lastRequestMessageResponseUri;

    /**
     * return error details
     * @see Afx_Config::CONFIG_RETURN_ERROR_DETAILS
     * @var boolean 
     */
    protected $returnErrorDetails = true;

    /**
     * use this to manipulate the packet directly from your services. This is an advanced option, and should be used with caution!
     * @var Afx_Amf_Packet
     */
    public static $requestPacket;

    /**
     * use this to manipulate the packet directly from your services. This is an advanced option, and should be used with caution!
     * @var Afx_Amf_Packet
     */
    public static $responsePacket;

    /**
     * constructor
     * @param array $sharedConfig
     */
    public function __construct($sharedConfig) {
        $this->lastRequestMessageResponseUri = '/1';
        if (isset($sharedConfig[Afx_Config::CONFIG_RETURN_ERROR_DETAILS])) {
            $this->returnErrorDetails = $sharedConfig[Afx_Config::CONFIG_RETURN_ERROR_DETAILS];
        }
    }

    /**
     * deserialize
     * @see Afx_Common_IDeserializer
     * @param array $getData
     * @param array $postData
     * @param string $rawPostData
     * @return string
     */
    public function deserialize(array $getData, array $postData, $rawPostData) {
        $deserializer = new Afx_Amf_Deserializer();
        $requestPacket = $deserializer->deserialize($getData, $postData, $rawPostData);
        return $requestPacket;
    }

    /**
     * creates a ServiceCallParameters object from an Afx_Amf_Message
     * supported separators in the targetUri are '/' and '.'
     * @param Afx_Amf_Message $Afx_Amf_Message
     * @return Afx_Common_ServiceCallParameters
     */
    protected function getServiceCallParameters(Afx_Amf_Message $Afx_Amf_Message) {
        $targetUri = str_replace('.', '/', $Afx_Amf_Message->targetUri);
        $split = explode('/', $targetUri);
        $ret = new Afx_Amf_Common_ServiceCallParameters();
        $ret->methodName = array_pop($split);
        $ret->serviceName = join($split, '/');
        $ret->methodParameters = $Afx_Amf_Message->data;
        return $ret;
    }

    /**
     * process a request and generate a response.
     * throws an Exception if anything fails, so caller must encapsulate in try/catch
     *
     * @param Afx_Amf_Message $requestMessage
     * @param Afx_Common_ServiceRouter $serviceRouter
     * @return Afx_Amf_Message the response Message for the request
     */
    protected function handleRequestMessage(Afx_Amf_Message $requestMessage, Afx_Amf_Common_ServiceRouter $serviceRouter) {
        $filterManager = Afx_Amfphp_Core_FilterManager::getInstance();
        $fromFilters = $filterManager->callFilters(self::FILTER_AMF_REQUEST_MESSAGE_HANDLER, null, $requestMessage);
        if ($fromFilters) {
            $handler = $fromFilters;
            return $handler->handleRequestMessage($requestMessage, $serviceRouter);
        }

        //plugins didn't do any special handling. Assumes this is a simple Afx_Amf_ RPC call
        $serviceCallParameters = $this->getServiceCallParameters($requestMessage);
        $ret = $serviceRouter->executeServiceCall($serviceCallParameters->serviceName, $serviceCallParameters->methodName, $serviceCallParameters->methodParameters);
        $responseMessage = new Afx_Amf_Message();
        $responseMessage->data = $ret;
        $responseMessage->targetUri = $requestMessage->responseUri . Afx_Amf_Constants::CLIENT_SUCCESS_METHOD;
        //not specified
        $responseMessage->responseUri = 'null';
        return $responseMessage;
    }

    /**
     * handle deserialized request
     * @see Afx_Common_IDeserializedRequestHandler
     * @param mixed $deserializedRequest
     * @param Afx_Common_ServiceRouter $serviceRouter
     * @return mixed
     */
    public function handleDeserializedRequest($deserializedRequest, Afx_Amf_Common_ServiceRouter $serviceRouter) {
        self::$requestPacket = $deserializedRequest;
        self::$responsePacket = new Afx_Amf_Packet();
        $numHeaders = count(self::$requestPacket->headers);
        for ($i = 0; $i < $numHeaders; $i++) {
            $requestHeader = self::$requestPacket->headers[$i];
            //handle a header. This is a job for plugins, unless comes a header that is so fundamental that it needs to be handled by the core
            $fromFilters = Afx_Amfphp_Core_FilterManager::getInstance()->callFilters(self::FILTER_AMF_REQUEST_HEADER_HANDLER, null, $requestHeader);
            if ($fromFilters) {
                $handler = $fromFilters;
                $handler->handleRequestHeader($requestHeader);
            }
        }

        $numMessages = count(self::$requestPacket->messages);

        //set amf version to the one detected in request
        self::$responsePacket->amfVersion = self::$requestPacket->amfVersion;

        //handle each message
        for ($i = 0; $i < $numMessages; $i++) {
            $requestMessage = self::$requestPacket->messages[$i];
            $this->lastRequestMessageResponseUri = $requestMessage->responseUri;
            $responseMessage = $this->handleRequestMessage($requestMessage, $serviceRouter);
            self::$responsePacket->messages[] = $responseMessage;
        }

        return self::$responsePacket;
    }

    /**
     * handle exception
     * @see Afx_Common_IExceptionHandler
     * @param Exception $exception
     * @return Afx_Amf_Packet
     */
    public function handleException(Exception $exception) {
        $errorPacket = new Afx_Amf_Packet();
        $filterManager = Afx_Amfphp_Core_FilterManager::getInstance();
        $fromFilters = $filterManager->callFilters(self::FILTER_AMF_EXCEPTION_HANDLER, null);
        if ($fromFilters) {
            $handler = $fromFilters;
            return $handler->generateErrorResponse($exception);
        }

        //no special handling by plugins. generate a simple error response with information about the exception
        $errorResponseMessage = null;
        $errorResponseMessage = new Afx_Amf_Message();
        $errorResponseMessage->targetUri = $this->lastRequestMessageResponseUri . Afx_Amf_Constants::CLIENT_FAILURE_METHOD;
        //not specified
        $errorResponseMessage->responseUri = 'null';
        $data = new stdClass();
        $data->faultCode = $exception->getCode();
        $data->faultString = $exception->getMessage();
        if ($this->returnErrorDetails) {
            $data->faultDetail = $exception->getTraceAsString();
            $data->rootCause = $exception;
        } else {
            $data->faultDetail = '';
        }
        $errorResponseMessage->data = $data;

        $errorPacket->messages[] = $errorResponseMessage;
        return $errorPacket;
    }

    /**
     * serialize
     * @see Afx_Common_ISerializer
     * @param mixed $data
     * @return mixed
     */
    public function serialize($data) {

        $serializer = new Afx_Amf_Serializer();
        return $serializer->serialize($data);
    }

}

?>
