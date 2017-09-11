<?php

/**
* @package		Hot Chilli Software PBBooking
* @license		GNU General Public License version 2 or later; see LICENSE.txt
* @link		http://www.hotchillisoftware.com
*/
 
// No direct access
 
defined('_JEXEC') or die('Restricted access'); 
	
$doc = JFactory::getDocument();

if ($this->config->user_offset == 1) {
	$dtstart_user = \Pbbooking\Pbbookinghelper::pbbConvertTimezone($this->event->dtstart,$this->event->user_offset); 
}
$this->calendar = $this->event->getCalendar();
?>

<h2><?php echo $doc->title;?></h2>


<div class="pbbooking multipage">

	<h2><?php echo JText::_('COM_PBBOOKING_SUCCESS_HEADING');?></h2>
	<p><?php echo JText::_('COM_PBBOOKING_VALIDATED_MESSAGE');?></p>

	<p><em><?php echo JText::_('COM_PBBOOKING_SUCCESS_SUB_HEADING');?></em></p>

	<table class="table table-striped">
		<tr>
			<th>
				<?php echo JText::_('COM_PBBOOKING_BOOKING_REFERENCE');?>
			</th>
			<td>
				<?php echo strtoupper(substr(md5($this->event->id),0,4).$this->event->id);?>
			</td>
		</tr>
	<!--	<tr>
			<th>
				<?php echo JText::_('COM_PBBOOKING_SUCCESS_DATE');?>
			</th>
			<td>
				<?php if ($this->config->user_offset == 1) :?>
					<strong><?php echo JText::_('COM_PBBOOKING_YOUR_DATE');?></strong> <?php echo JHtml::_('date',$dtstart_user->format(DATE_ATOM),JText::_('COM_PBBOOKING_SUCCESS_DATE_FORMAT'));?>
					<strong><?php echo JText::_('COM_PBBOOKING_OUR_DATE');?></strong> <?php echo JHtml::_('date',$this->event->dtstart->format(DATE_ATOM),JText::_('COM_PBBOOKING_SUCCESS_DATE_FORMAT'));?>
				<?php else:?>
					<?php echo JHtml::_('date',$this->event->dtstart->format(DATE_ATOM),JText::_('COM_PBBOOKING_SUCCESS_DATE_FORMAT'));?>
				<?php endif;?>
			</td>
		<tr> -->
			<th><?php echo JText::_('COM_PBBOOKING_SUCCESS_TIME');?></th>
			<td>
				<?php if ($this->config->user_offset == 1) :?>
					<!-- <strong><?php echo JText::_('COM_PBBOOKING_YOUR_TIME');?></strong> <?php echo JHtml::_('date',$dtstart_user->format(DATE_ATOM),JText::_('COM_PBBOOKING_SUCCESS_DATE_TIME_FORMAT'));?> -->
					<!-- <strong><?php echo JText::_('COM_PBBOOKING_OUR_TIME');?></strong> --><?php echo JHtml::_('date',$this->event->dtstart->format(DATE_ATOM),JText::_('COM_PBBOOKING_SUCCESS_DATE_TIME_FORMAT'));?>
				<?php else:?>
					<?php echo JHtml::_('date',$this->event->dtstart->format(DATE_ATOM),JText::_('COM_PBBOOKING_SUCCESS_TIME_FORMAT'));?>
				<?php endif;?>
			</td>
		</tr>
		<tr>
			<th><?php echo JText::_('COM_PBBOOKING_BOOKINGTYPE');?></th>
			<td><?php echo \Pbbooking\Pbbookinghelper::print_multilang_name($this->service,'service');?></td>
		</tr>
		<?php if (isset($this->calendar)) :?>
			<tr><th><?php echo JText::_('COM_PBBOOKING_SUCCESS_CALENDAR');?></th><td><?php echo \Pbbooking\Pbbookinghelper::print_multilang_name($this->calendar,'calendar');?></td></tr>
		<?php endif;?>
	</table>



<?php
	$cal = $this->event->getCalendar();
	$content = '';

	if ($cal->article_id) {
		$c_article =JTable::getInstance("content");
		$c_article->load($cal->article_id);
		$content .= '<h3>'. $c_article->get("title").'</h3>';
		$content .= $c_article->get("introtext"); // introtext and/or fulltext
		$content .= $c_article->get("fulltext");
	}

	if ($this->service->article_id) {
		$s_article = JTable::getInstance("content");
		$s_article->load($this->service->article_id);
		$content .= '<h3>'. $s_article->get("title").'</h3>';
		$content .= $s_article->get("introtext"); // introtext and/or fulltext
		$content .= $s_article->get("fulltext");

	}

	echo $content;
?>


</div>