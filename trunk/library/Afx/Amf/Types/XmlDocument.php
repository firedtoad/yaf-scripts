<?php
/*
 * @version $Id: XmlDocument.php 94 2012-12-10 03:39:40Z zhujinghe $
 */

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

class Afx_Amf_Types_XmlDocument
{
	public $data;

	public function Afx_Amf_Types_XmlDocument($data)
	{
		$this->data = $data;
	}
}
?>
