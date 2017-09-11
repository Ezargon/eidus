<?php /**
 * @title		Shape 5 Open Table
 * @version		1.0
 * @package		Joomla
 * @website		http://www.shape5.com
 * @copyright	Copyright (C) 2015 Shape 5 LLC. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
$url = JURI::root().'modules/mod_s5_opentable/';
$s5_ot_idcode = $params->get('s5_ot_idcode', '');
$s5_ot_pretext = $params->get('pretext', '');


require (JModuleHelper::getLayoutPath('mod_s5_opentable'));