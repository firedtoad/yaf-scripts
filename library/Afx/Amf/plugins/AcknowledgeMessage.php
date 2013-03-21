<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Plugins_FlexMessaging
 */

/**
 * Used to generate a Flex Acknowledge message.
 * part of the AmfphpFlexMessaging plugin
 *
 * @package Amfphp_Plugins_FlexMessaging
 * @author Ariel Sommeria-Klein
 */
class Afx_Amf_plugins_AcknowledgeMessage {
    /**
     * correlation id. guid
     * @see generateRandomId
     * @var string 
     */
    public $correlationId;
    
    /**
     * message id. guid
     * @see generateRandomId
     * @var string 
     */
    public $messageId;
    
     /**
     * client id. guid
     * @see generateRandomId
     * @var string 
     */   
    public $clientId;
    
    /**
     * destination
     * @var string 
     */
    public $destination;
    
    /**
     * body
     * @var stdClass 
     */
    public $body;
    
    /**
     * time to live
     * @var int 
     */
    public $timeToLive;
    
    /**
     * time stamp
     * @var int 
     */
    public $timestamp;
    
    /**
     * headers. DSId(string), DSMessagingVersion(int)
     * @var stdClass 
     */
    public $headers;

    const FLEX_TYPE_COMMAND_MESSAGE = 'flex.messaging.messages.CommandMessage';
    const FLEX_TYPE_REMOTING_MESSAGE = 'flex.messaging.messages.RemotingMessage';
    const FLEX_TYPE_ACKNOWLEDGE_MESSAGE = 'flex.messaging.messages.AcknowledgeMessage';
    const FLEX_TYPE_ERROR_MESSAGE = 'flex.messaging.messages.ErrorMessage';
    const FIELD_MESSAGE_ID = 'messageId';
    
    /**
     * constructor
     * @param string $correlationId
     */
    public function __construct($correlationId) {
        $explicitTypeField = Afx_Amf_Constants::FIELD_EXPLICIT_TYPE;
        $this->$explicitTypeField = self::FLEX_TYPE_ACKNOWLEDGE_MESSAGE;
        $this->correlationId = $correlationId;
        $this->messageId = $this->generateRandomId();
        $this->clientId = $this->generateRandomId();
        $this->destination = null;
        $this->body = null;
        $this->timeToLive = 0;
        $this->timestamp = (float) (time() . '00');
        $this->headers = new stdClass();
    }

    /**
     *  generate random id
     * @return string
     */
    public function generateRandomId() {
        // version 4 UUID
        return sprintf(
                        '%08X-%04X-%04X-%02X%02X-%012X', mt_rand(), mt_rand(0, 65535), bindec(substr_replace(
                                        sprintf('%016b', mt_rand(0, 65535)), '0100', 11, 4)
                        ), bindec(substr_replace(sprintf('%08b', mt_rand(0, 255)), '01', 5, 2)), mt_rand(0, 255), mt_rand()
        );
    }

    public function setBody($body)
    {
        $this->body = $body;
    }
}


class ProfilingHeader {
    var $EventType;
    
    /**
     * time to live
     * @var int
     */
    public $includeTime;
    
    /**
     * time to live
     * @var int
     */
    public $decodeTime;
    
    /**
     * time to live
     * @var int
     */
    public $callTime;
    
    /**
     * time to live
     * @var int
     */
    public $frameworkTime;
    function __construct() {
        $explicitTypeField = Afx_Amf_Constants::FIELD_EXPLICIT_TYPE;
        $this->$explicitTypeField = 'data2.htdocs.core.amf.util.TraceHeader';
        $this->EventType = 'profiling';
        $this->includeTime = 0;
        $this->decodeTime = 0;
        $this->callTime = 3;
        $this->totalTime = 12;
        $this->frameworkTime = 8;
    }
}
?>
