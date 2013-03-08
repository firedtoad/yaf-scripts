<?php
/*
 * @version $Id: ByteArray.php 94 2012-12-10 03:39:40Z zhujinghe $
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
 * Amf byte arrays will be converted to and from this class
 *
 * @package Afx_Amf_Types
 * @author Ariel Sommeria-klein
 */

class Afx_Amf_Types_ByteArray
{
	public $data;

	public function Afx_Amf_Types_ByteArray($data)
	{
		$this->data = $data;
	}
}
?>
