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

<h3><?php echo 'Selección de hora para '.JHtml::_('date',$this->dateparam->format(DATE_ATOM),JText::_('COM_PBBOOKING_DAY_VIEW_DATE_FORMAT'));?></h3>

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


	<table id="pbbooking" class="table-striped">
	<!-- Draw header row showing calendars across the top....-->
		<tr>
			<th  colspan="5">
				<a href="<?php echo JRoute::_('index.php?option=com_pbbooking&task=dayview&dateparam='.$dtYesterday->format("Ymd"));?>" rel="nofollow">&lt;&lt;</a>
				<?php echo JHtml::_('date',$this->dateparam->format(DATE_ATOM),JText::_('COM_PBBOOKING_DAY_VIEW_DATE_FORMAT'));?>
				<a href="<?php echo JRoute::_('index.php?option=com_pbbooking&task=dayview&dateparam='.$dtTomorrow->format("Ymd"));?>" rel="nofollow">&gt;&gt;</a>
			</th>
		</tr>
	<!--	<tr>
			
			<?php foreach ($this->cals as $cal) :?>
				<th colspan="5">
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
		</tr> -->

		<!-- draw table data rows -->
                      
		<?php 
                $keys_hora = array();
                $keys_minuto = array();
                $keys_hora = array();
                $tabla[][] = array();
                //Mientras "9:00" <  "13:25" - aumenta cinco mintos.
                while ($this->day_dt_start <= $this->last_slot_for_day) 
                 :?>
			<?php $slot_end = date_create($this->day_dt_start->format(DATE_ATOM),new DateTimezone(PBBOOKING_TIMEZONE));?>
			<?php $slot_end->modify('+ '.$this->config->time_increment.' minutes');?>
                        <?php $hora = $this->day_dt_start->format('H');?>
                        <?php $minuto = $this->day_dt_start->format('i'); ?>
                       
                        <?php $buffer = ""; ?>
                            
			<tr>

                                    <?php foreach ($this->cals as $cal){
                                       
				
                                        
                                            $event = $cal->is_free_from_to($this->day_dt_start,$slot_end);
                                            $busy = ($event || $this->day_dt_start<$this->earliest || $cal->exceededMaxBookingsForDay($this->day_dt_start) || !$cal->possibleToBookAnyServiceAtTime($this->day_dt_start));
                                        
                                            $buffer.="<td class=\"pbbooking-";
                                            if($busy){
                                                 $buffer.="busy-cell\">";
                                            }else{
                                                 $buffer.="free-cell\">";
                                            }
                                        
                                            if(!$busy){
                                                if($this->user->authorise('pbbooking.create','com_pbbooking')){
                                                    $buffer.="<a href=\"".JRoute::_('index.php?option=com_pbbooking&task=create&dtstart='.$this->day_dt_start->format('YmdHi').'&cal_id='.$cal->cal_id)."\" rel=\"nofollow\">";
                                                    $buffer.=$hora.":".$minuto;
                                                    $buffer.="</a>";
                                             
                                                }else{
                                                    $buffer.=$hora.":".$minuto;

                                                }
                                            }else{
                                                if(($this->config->show_busy_front_end == 0)){
                                                    $buffer.=$hora.":".$minuto."<br>(".JText::_('COM_PBBOOKING_BUSY').")";
                                                }else{
                                                    if (is_object($event)){
							 $buffer.= htmlspecialchars($event->getSummary());
                                                    }else{
							$buffer.= $hora.":".$minuto."(".JText::_('COM_PBBOOKING_BUSY').")";
                                                }
                                            }
                                            }
                                            $buffer.="</td>";
                                            $tabla[$hora][$minuto]=$buffer;
                                            //echo $hora . ":" . $minuto . "=" . $buffer;
                                            
                                            if (!in_array($minuto, $keys_minuto))
                                            { 
                                               array_push( $keys_minuto, $minuto);
                                            }
                                             if (!in_array($hora, $keys_hora))
                                            { 
                                               array_push( $keys_hora, $hora);
                                            }
                                         
                                    }
                                        
                                     ?>
		<?php $this->day_dt_start->modify('+ '.$this->config->time_increment.' minutes');?>
		<?php endwhile;?>

		<!-- end draw table data rows-->

	<?php 
        
        $buffer="";
        
          
           // $buffer.= "<th>9</th><th>10</th><th>11</th><th>12</th><th>13</th>";
           asort($keys_minuto);
           ksort($keys_hora);
            foreach ($keys_minuto as $minuto){
                $buffer.= "<tr>";
                foreach ($keys_hora as $hora){
                    try{
                        if(isset($tabla[$hora][$minuto])){
                           $buffer.=  $tabla[$hora][$minuto];
                        }else{
                          $buffer.= "<td class=\"noday\">". "</td>";
                        
                      }
                      
                    }catch(Exception $e){
                         
                    }finally{
                       
                    }
                }
                
                $buffer.= "</tr>";
            }
            $buffer.= "</table>";
            
            echo $buffer;
        ?>

</div>

<?php
    $modules = JModuleHelper::getModules('pbbookingpagebottom');
    foreach ($modules as $module) {
        echo JModuleHelper::renderModule($module);
    }
?>