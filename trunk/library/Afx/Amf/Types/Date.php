<?php
/*
 * @version $Id: Date.php 94 2012-12-10 03:39:40Z zhujinghe $
 */

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Afx_Amf_Types
 */

/**
 * Amf dates will be converted to and from this class. The PHP DateTime class is for PHP >= 5.2.0, and setTimestamp for PHP >= 5.3.0, so it can't be used in amfPHP
 * Of course feel free to use it yourself if your host supports it.
 * 
 * @package Afx_Amf_Types
 * @author Danny Kopping
 */
class Afx_Amf_Types_Date
{
        /**
         * number of ms since 1st Jan 1970
         * @var integer
         */
    	public $timeStamp;

	public function Afx_Amf_Types_Date($timeStamp)
	{
		$this->timeStamp = $timeStamp;

	}
}

?>