<?php
/**
* @package		JooDatabase - http://joodb.feenders.de
* @copyright	Copyright (C) Computer - Daten - Netze : Feenders. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @author		Dirk Hoeschen (hoeschen@feenders.de)
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/*
 * Make sure the user is authorized to view this page
 */
if (!JFactory::getUser()->authorise('core.manage')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Set the table directory
JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/tables/');

// Require the helper library
require_once( JPATH_COMPONENT.'/helpers/joodb.php' );

// add some custem styles
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_joodb/assets/joodb.css');

$version = new JVersion();
if (intval($version->RELEASE)<3) {
    $document->addStyleSheet('components/com_joodb/assets/joodb-bootstrap.css');
}
// get the controller
require_once (JPATH_COMPONENT.'/controller.php');
if($controllerName = JRequest::getVar('controller')) {	
	require_once (JPATH_COMPONENT.'/controllers/'.$controllerName.'.php');
}

// creation of an object from class controller
// Create the controller
$classname	= 'JooDBController'.$controllerName;
$controller = new $classname(array('default_task' => 'display') );
$controller->registerTask('apply', 'save', 'unpublish','publish');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();

?>
