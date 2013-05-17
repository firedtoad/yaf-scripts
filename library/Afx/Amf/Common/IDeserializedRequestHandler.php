<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Afx_Common
 */

/**
 * interface for a class that can handle a deserialized request
 * @package Afx_Common
 * @author Ariel Sommeria-klein
 */
interface Afx_Amf_Common_IDeserializedRequestHandler {

    /**
     * handle the deserialized request, usually by making a series of calls to a service. This should not handle exceptions, as this is done separately
     * @param mixed $deserializedRequest For Amf, this is an AmfPacket
     * @param Afx_Common_ServiceRouter $serviceRouter the service router created and configured by the gateway
     * @return mixed the response object.  For Amf, this is an AmfPacket
     */
    public function handleDeserializedRequest($deserializedRequest, Afx_Amf_Common_ServiceRouter $serviceRouter);
}

?>
