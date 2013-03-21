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
 * AS3 XMLDocument type. 
 * @see Afx_Amf_Types_Xml
 *
 * @package Afx_Amf_Types
 * @author Ariel Sommeria-klein
 */
class Afx_Amf_Types_XmlDocument {

    /**
     * data
     * @var string 
     */
    public $data;

    /**
     * constructor
     * @param string $data
     */
    public function __construct($data) {
        $this->data = $data;
    }

}

?>
