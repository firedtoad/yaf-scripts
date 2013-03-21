<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Plugins_Discovery
 * 
 */

/**
 * Contains all collected information about a service method.
 *
 * @author Ariel Sommeria-klein
 * @package Amfphp_Plugins_Discovery
 */
class Afx_Amf_plugins_MethodDescriptor {


    /**
     * 
     * @var array of ParameterInfo
     */
    public $arguments;

    /**
     *
     * @var string method level comment
     */
    public $description;

    /**
     * return type
     * @var string 
     */
    public $access;

    /**
     * constructor
     * @param array $parameters
     * @param string $comment
     * @param string $access 
     */
    public function __construct($comment, array $parameters, $access) {
        $this->description = $comment;
        $this->arguments = $parameters;
        $this->access = $access;
    }

}

?>
