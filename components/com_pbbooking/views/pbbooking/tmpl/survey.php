<?php

/**
* @package		Hot Chilli Software PBBooking
* @license		GNU General Public License version 2 or la<ter; see LICENSE.txt
* @link		http://www.hotchillisoftware.com
*/

// No direct access
 
defined('_JEXEC') or die('Restricted access'); 

$doc = JFactory::getDocument();
JHtml::_('script','com_pbbooking/external/front.js',true,true);
JHtml::_('script','com_pbbooking/app/com_pbbooking.general.js',true,true);
JHtml::_('stylesheet','com_pbbooking/pbbooking-site.css',array(),true);


	


?>

<h1><?php echo JText::_('COM_PBBOOKING_SURVEY_HEADING');?></h1>

<p><?php echo JTexT::_('COM_PBBOOKING_SURVEY_INTRODUCTION');?></p>

<form method="POST" action="<?php echo JRoute::_('index.php?option=com_pbbooking&task=survey');?>" class="form form-horizontal">
	<?php foreach ($this->form->getGroup('') as $field) :?>
		<?php echo $field->getControlGroup();?>
	<?php endforeach;?>
	<div class="control-group">
		<div class="controls">
			<input type="submit" value="<?php echo JText::_('COM_PBBOOKING_SURVEY_SUBMIT');?>"/>
		</div>
	</div>
	<input type="hidden" name="id" value="<?php echo $this->event->id;?>"/>
	<input type="hidden" name="email" value="<?php echo $this->event->email;?>"/>
	
</form>