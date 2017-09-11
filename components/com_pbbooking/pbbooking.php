<?php
/**
 * @package    Hot Chilli Software PBBooking
 * @subpackage Components
 * @link http://www.hotchillisoftware.com
 * @license    GNU/GPL
*/
 
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$config = JFactory::getConfig();
$input = JFactory::getApplication()->input;


//set some defines cause we're goign to use things a lot!!
define('PBBOOKING_TIMEZONE',$config->get('offset')); 
if (!defined('DS')) {
    define('DS',DIRECTORY_SEPARATOR);
}



if (file_exists(JPATH_LIBRARIES."/pbbookinglib/Purplebeanie/Google/Syncer.php")) {
    define("PBBOOKING_VERSION","subscriber");
} else {
    define("PBBOOKING_VERSION","free");
}


//pull in my own framework files....
require_once(JPATH_LIBRARIES.DS.'pbbookinglib'.DS.'vendor'.DS.'autoload.php');
require_once(JPATH_LIBRARIES.DS.'purplebeanie'.DS.'autoload.php');

\Pbbooking\Pbbookinghelper::bootstrapPbbooking(true);


// begin - remap some of the old tasks here. -- this can be removed form v4 and is only needed for B/C of 3.2
$task = $input->getCmd("task");
if (in_array($task, array("load_slots_for_day","save","validate","doConfirmPayment","load_calendar_services","dayview","create","cron"))) {
	$input->set("task","pbbooking.".$task);
}
if ($task == "delete_appt") {
	$input->set("task","appointments.delete_appt");
}
if ($task == "sync") {
	$input->set("format",'');
	$input->set("task","cron.sync");
}

if ($task == "survey") {
	$input->set("task", null);
	$input->set("view","survey");
}

// END - remapping some of the old tasks.

$controller = JControllerLegacy::getInstance('Pbbooking');

$controller->execute($input->getCmd('task'));

$controller->redirect();
