<?php
/*
 *
 * @Version       $Id: issuetracker.php 1929 2015-02-08 16:25:12Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.5.0
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-02-08 16:25:12 +0000 (Sun, 08 Feb 2015) $
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Required for Joomla 3.0
if(!defined('DS')){
   define('DS',DIRECTORY_SEPARATOR);
}

// Require the base controller
require_once (JPATH_COMPONENT.DS.'controller.php');

// Execute the task.
$controller = JControllerLegacy::getInstance('IssueTracker');

$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();

