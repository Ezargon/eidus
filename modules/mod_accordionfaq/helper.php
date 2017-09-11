<?php
/**
 * @version		$Id: helper.php 10214 2008-04-19 08:59:04Z eddieajau $
 * @package		Joomla
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class modAccordionfaqHelper
{
	function editNumeric( $paramname, &$params, $default, &$value, &$errortext )
	{
		$paramvalue = $params->get( $paramname, $default );
		if (is_numeric($paramvalue))
		{
			$value = (int)$paramvalue;
			return true;
		}
		$errortext = "ERROR: Parameter ".$paramname."='".$paramvalue."' is invalid. ";
		$errortext .= "'".$paramvalue."' must be a number.";
		return false;
	}

	function editFaqClass( $classname, &$errortext )
	{
		$valid['round3'] = "1";
		$valid['round5'] = "1";
		$valid['round7'] = "1";
		$valid['round9'] = "1";
		$valid['headerbackground'] = "1";
		$valid['headerborder'] = "1";
		$valid['contentbackground'] = "1";
		$valid['contentborder'] = "1";
		$valid['border'] = "1";
		$valid['bcolor'] = "1";
		$valid['defaulticon'] = "1";
		$valid['onoff'] = "1";
		$valid['plus'] = "1";
		$valid['plus2'] = "1";
		$valid['plus3'] = "1";
		$valid['arrow'] = "1";
		$valid['greenarrow'] = "1";
		$valid['orangearrow'] = "1";
		$valid['orangearrow2'] = "1";
		$valid['help'] = "1";
		$valid['help2'] = "1";
		$valid['power'] = "1";
		$valid['check'] = "1";
		$valid['rtl'] = "1";
		$valid['alignright'] = "1";
		$valid['alignleft'] = "1";
		$valid['aligncenter'] = "1";
		if (! isset( $valid[strtolower($classname)]))
		{
			$validvalues = array_keys( $valid );
			$validlist = implode( $validvalues, ", ");
			$errortext = "WARNING: faqclass '".$classname."' is invalid. ";
			$errortext .= "Acceptable values for '".$classname."' are ".$validlist.".";
			return false;
		}
		return true;
	}

	function editParam( $paramname, &$params, $default, &$valid, &$value, &$errortext )
	{
		$paramvalue = $params->get( $paramname, $default );
		if (isset($valid[strtolower($paramvalue)]))
		{
			$value = $valid[strtolower($paramvalue)];
			return true;
		}
		$validvalues = array_keys( $valid );
		$validlist = implode( $validvalues, ", ");
		$errortext = "ERROR: Parameter ".$paramname."='".$paramvalue."' is invalid. ";
		$errortext .= "Acceptable values for '".$paramvalue."' are ".$validlist.".";
		return false;
	}

	function editTrueFalse( $paramname, &$params, $default, &$value, &$errortext )
	{
		$valid['true'] = "tru";
		$valid['false'] = "fals";
		$valid['yes'] = "tru";
		$valid['no'] = "fals";
		$valid['on'] = "tru";
		$valid['off'] = "fals";
		$valid['t'] = "tru";
		$valid['f'] = "fals";
		$valid['y'] = "tru";
		$valid['n'] = "fals";
		$valid['1'] = "tru";
		$valid['0'] = "fals";
		return $this->editParam( $paramname, $params, $default, $valid, $value, $errortext );
	}

	function formattedError( $errortext, $faqline )
	{
		$text = "<p style=\"color: red;background-color: yellow;visibility: visible\">\n";
		$text .= "<b>".$faqline."<br/>";
		$text .= $errortext."</b>";
		$text .= "</p>\n";
		return $text;
	}

}
?>