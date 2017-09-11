<?php

// No direct access
 
defined('_JEXEC') or die('Restricted access');

$pEvent = new \Pbbooking\Model\Event($this->item->id);

?>

<style>
	.printTable td, .printTable th{
		padding: 5px;
		border: 1px solid #ccc;
	}
	.printTable {
		border: 1px solid black;
		border-collapse: collapse;
	}
</style>

<form name="adminForm" id="adminForm" class="form form-horizontal">
    <div class="row-fluid">
        <div class="span12">
            <h1 style="text-align: center;"><?php echo JText::_('COM_PBBOOKING_EVENT_DISPLAY_EVENT');?></h1>


	        <table class="printTable" style="width: 90%;margin: 0px auto;">
		        <tr></tr>
		        <tr><th colspan="4"><?php echo JText::_("COM_PBBOOKING_CREATE_SUBHEADING");?></th></tr>
		        <tr>
			        <th><?php echo JText::_("COM_PBBOOKING_CLASS_SCHEDULE_START_TIME");?></th>
			        <td>
				        <?php echo JHtml::date($this->item->dtstart,JText::_("COM_PBBOOKING_DATE_FORMAT"),false);?>&nbsp;
				        <?php echo JHtml::date($this->item->dtstart,JText::_("COM_PBBOOKING_DATE_FORMAT"),false);?>
			        </td>
			        <th><?php echo JText::_("COM_PBBOOKING_CLASS_SCHEDULE_END_TIME");?></th>
			        <td>
				        <?php echo JHtml::date($this->item->dtend,JText::_("COM_PBBOOKING_DATE_FORMAT"),false);?>&nbsp;
				        <?php echo JHtml::date($this->item->dtend,JText::_("COM_PBBOOKING_DATE_FORMAT"),false);?>
			        </td>
		        </tr>
		        <tr>
			        <th><?php echo JText::_("COM_PBBOOKING_PRINT_CALENDAR");?></th>
			        <td><?php echo htmlentities($pEvent->getCalendar()->name);?></td>
			        <th><?php echo JText::_("COM_PBBOOKING_PRINT_SERVICE");?></th>
			        <td><?php echo htmlentities($pEvent->getService()->name);?></td>
		        </tr>
		        <tr><th colspan="4"><?php echo JText::_("COM_PBBOOKING_MESSAGE_BOOKING_DETAILS_TABLE");?></th></tr>
		        <?php foreach (json_decode($this->item->customfields_data,true) as $field):?>
			        <tr><th colspan="2"><?php echo htmlentities($field["fieldname"]);?></th><td colspan="2"><?php echo htmlentities($field["data"]);?></td></tr>
		        <?php endforeach;?>
	        </table>


        </div>

    </div>

    <input type="hidden" name="task"/>
    <input type="hidden" name="id" value="<?php echo $this->item->id;?>"/>
</form>
