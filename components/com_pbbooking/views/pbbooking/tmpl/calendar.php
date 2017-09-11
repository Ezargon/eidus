<?php

/**
* @license		GNU General Public License version 2 or later; see LICENSE.txt
* @link		http://www.hotchillisoftware.com
*/

// No direct access
 
defined('_JEXEC') or die('Restricted access'); 

jimport( 'joomla.application.module.helper' );

  
$doc = JFactory::getDocument();

JHtml::_("jquery.framework");
JHtml::_('behavior.framework');             //the only reason this is still needed is to allow the Joomla.JText functions.


// Load external dependencies
JHtml::script("com_pbbooking/external/front.js",true,true);


// Load PBBooking scripts and styles.
JHtml::_('script','com_pbbooking/app/com_pbbooking.general.js',false,true);
JHtml::_('script','com_pbbooking/app/com_pbbooking.individual.freeflow.js',false,true);
JHtml::stylesheet("com_pbbooking/pbbooking-site.css", array(),true);

JText::script("COM_PBBOOKING_SERVICE_POINTS_SUFFIX");
JText::script("COM_PBBOOKING_SERVICE_POINTS_OR");

?>


<h1><?php echo $doc->title;?></h1>

<div id="pbbooking-notifications"></div>

<?php
    $modules = JModuleHelper::getModules('pbbookingpagetop');
    foreach ($modules as $module) {
        echo JModuleHelper::renderModule($module);
    }
?>

<div class="pbbooking singlepage">

<?php 


include('individual_freeflow_view.php');

?>

</div>

<?php
    $modules = JModuleHelper::getModules('pbbookingpagebottom');
    foreach ($modules as $module) {
        echo JModuleHelper::renderModule($module);
    }
?>

<?php
if ($this->config->show_link) {
	echo '<p>Powered by <a href="http://hotchillisoftware.com">PBBooking - Online Booking for Joomla</a>.</p>';
}

?>