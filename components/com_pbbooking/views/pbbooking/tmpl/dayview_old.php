<?php 
	
	$doc = JFactory::getDocument();
	JHtml::_('stylesheet','com_pbbooking/pbbooking-site.css',array(),true);
	JHtml::_("jquery.framework");
	//calculate the dates for the forward / back toggles
	$dtYesterday = date_create($this->day_dt_start->format(DATE_ATOM),new DateTimeZone(PBBOOKING_TIMEZONE));
	$dtTomorrow = date_create($this->day_dt_start->format(DATE_ATOM),new DateTimeZone(PBBOOKING_TIMEZONE));
	$dtYesterday->modify('-1 day');
	$dtTomorrow->modify('+1 day');

	JHtml::_('behavior.modal');

?>

<h2><?php echo $doc->title;?></h2>

<h3><?php echo JText::_('COM_PBBOOKING_DAY_VIEW_HEADING').' '.JHtml::_('date',$this->dateparam->format(DATE_ATOM),JText::_('COM_PBBOOKING_DAY_VIEW_DATE_FORMAT'));?></h3>

<?php if (!$this->user->authorise('pbbooking.create','com_pbbooking')) :?>
	<div class="pbbooking-notifications-active">
		<p><?php echo JText::_('COM_PBBOOKING_LOGIN_MESSAGE_CREATE');?></p>
	</div>
<?php endif;?>

<?php
    $modules = JModuleHelper::getModules('pbbookingpagetop');
    foreach ($modules as $module) {
        echo JModuleHelper::renderModule($module);
    }
?>


<div class="pbbooking multipage">


	<table id="pbbooking">
	<!-- Draw header row showing calendars across the top....-->
		<tr>
			<th colspan="<?php echo count($this->cals)+1;?>">
				<a href="<?php echo JRoute::_('index.php?option=com_pbbooking&task=dayview&dateparam='.$dtYesterday->format("Ymd"));?>" rel="nofollow">&lt;&lt;</a>
				<?php echo JHtml::_('date',$this->dateparam->format(DATE_ATOM),JText::_('COM_PBBOOKING_DAY_VIEW_DATE_FORMAT'));?>
				<a href="<?php echo JRoute::_('index.php?option=com_pbbooking&task=dayview&dateparam='.$dtTomorrow->format("Ymd"));?>" rel="nofollow">&gt;&gt;</a>
			</th>
		</tr>
		<tr>
			<th></th> <!-- first column left blank to display time slots -->
			<?php foreach ($this->cals as $cal) :?>
				<th>
					<?php if ($cal->article_id) :?>
						<a href="<?php echo JRoute::_("index.php?option=com_content&tmpl=component&view=article&id=".$cal->article_id);?>" class="modal">
							<?php echo \Pbbooking\Pbbookinghelper::print_multilang_name($cal,'calendar');?>
						</a>
					<?php else:?>
						<?php echo \Pbbooking\Pbbookinghelper::print_multilang_name($cal,'calendar');?>
					<?php endif;?>
					<?php if ($cal->image_url):?>
						<img src="<?php echo $cal->image_url;?>"/>
					<?php endif;?>
				</th>
			<?php endforeach;?>
		</tr>


		<!-- draw table data rows -->

		<?php while ($this->day_dt_start <= $this->last_slot_for_day) :?>
			<?php $slot_end = date_create($this->day_dt_start->format(DATE_ATOM),new DateTimezone(PBBOOKING_TIMEZONE));?>
			<?php $slot_end->modify('+ '.$this->config->time_increment.' minutes');?>
			<tr>
				<th><?php echo $this->day_dt_start->format(JText::_('COM_PBBOOKING_SUCCESS_TIME_FORMAT'));?></th>
				
                                    <?php $cont=0;?>
                                    <?php foreach ($this->cals as $cal) :?>
                                        
					<?php $event = $cal->is_free_from_to($this->day_dt_start,$slot_end);?>
					<?php $busy = ($event || $this->day_dt_start<$this->earliest || $cal->exceededMaxBookingsForDay($this->day_dt_start) || !$cal->possibleToBookAnyServiceAtTime($this->day_dt_start));?>
					
                                        <td class="pbbooking-<?php echo ($busy) ? 'busy' : 'free';?>-cell">
						<?php if (!$busy):?>
							<?php if ($this->user->authorise('pbbooking.create','com_pbbooking')) :?>
								<a href="<?php echo JRoute::_('index.php?option=com_pbbooking&task=create&dtstart='.$this->day_dt_start->format('YmdHi').'&cal_id='.$cal->cal_id);?>" rel="nofollow">
							<?php endif;?>
								<?php echo (!$cal->is_free_from_to($this->day_dt_start,$slot_end)) ? JText::_('COM_PBBOOKING_FREE') : JText::_('COM_PBBOOKING_BUSY');?>
							<?php if ($this->user->authorise('pbbooking.create','com_pbbooking')) :?>
								</a>
							<?php endif;?>
						<?php else :?>
							<?php if ($this->config->show_busy_front_end == 0):?>
								<?php echo JText::_('COM_PBBOOKING_BUSY');?>
							<?php else:?>
								<?php
									if (is_object($event)) :
										echo htmlspecialchars($event->getSummary());
									else:
										echo JText::_('COM_PBBOOKING_BUSY');
								endif;?>
							<?php endif;?>
						<?php endif;?>
					</td>
                                        <?php $cont++;?>
				<?php endforeach;?>
			</tr>
			<?php $this->day_dt_start->modify('+ '.$this->config->time_increment.' minutes');?>
		<?php endwhile;?>

		<!-- end draw table data rows-->

	</table>

</div>

<?php
    $modules = JModuleHelper::getModules('pbbookingpagebottom');
    foreach ($modules as $module) {
        echo JModuleHelper::renderModule($module);
    }
?>