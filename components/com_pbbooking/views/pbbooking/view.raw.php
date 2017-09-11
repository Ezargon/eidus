<?php

/**
* @package		Hot Chilli Software PBBooking
* @license		GNU General Public License version 2 or later; see LICENSE.txt
* @link		http://www.hotchillisoftware.com
*/
 
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');
 
 
class PbbookingViewPbbooking extends JViewLegacy
{
    function display($tpl = null)
    {
    	$input = JFactory::getApplication()->input;
    	$task = $input->get("task");

    	switch($task) {
    		case "load_slots_for_day":
    			$this->filterCals();
    			$this->prepareData();
    			break;
    	}
			
		parent::display($tpl);
    }

    private function prepareData(){
		$pbbooking_config = $GLOBALS["com_pbbooking_data"]["config"];
		$input = JFactory::getApplication()->input;
		$config = JFactory::getConfig();
		$db = JFactory::getDbo();

    	date_default_timezone_set(JFactory::getConfig()->get('offset'));	

		$date = date_create($input->get('dateparam',null,'string'),new DateTimeZone(PBBOOKING_TIMEZONE));
		$end_of_day = clone $date;
		$end_of_day->setTime(23,59,59)->modify("+1 day");

		$grouping = $input->get('grouping',null,'string');
		$treatment_id = $input->get('treatment',null,'integer');
		$treatment = $db->setQuery('select * from #__pbbooking_treatments where id ='.$db->escape((int)$treatment_id))->loadObject();

		//get start_hour start_min end_hour end_min for groupings		
		$groupings = \Pbbooking\Pbbookinghelper::get_shift_times();
		
		//push vars into view
		$this->date_start = date_create($date->format(DATE_ATOM),new DateTimeZone(PBBOOKING_TIMEZONE));
		$this->date_end = date_create($date->format(DATE_ATOM),new DateTimeZone(PBBOOKING_TIMEZONE));
		$this->config = $pbbooking_config;
		$this->user_offset = $input->get('user_offset',0,'integer');

		if ($pbbooking_config->enable_shifts == 1) {
			$this->date_start->setTime($groupings[$grouping]['start_time']['start_hour'],$groupings[$grouping]['start_time']['start_min'],'0');
			$this->date_end->setTime($groupings[$grouping]['end_time']['end_hour'],$groupings[$grouping]['end_time']['end_min'],'0');
		} else {
            //need to loop through all calendars and find the earliest start time as presently it will just find the first
            if (count($this->cals) == 1) {
                $calhours = $this->cals[key($this->cals)]->getCalendarTradingHoursForDay($date);
                if (!$calhours) {
	                $this->date_start->setTime(0,0,0);
	                $this->date_end->setTime(23,59,59);
                } else {
			    	$this->date_start = date_create($calhours['dtstart']->format(DATE_ATOM),new DateTimeZone(PBBOOKING_TIMEZONE));
			    	$this->date_end = date_create($calhours['dtend']->format(DATE_ATOM),new DateTimeZone(PBBOOKING_TIMEZONE));
			    }
            } else {
                $this->date_start->setTime(0,0,0);
                $this->date_end->setTime(23,59,59);
            }
		}
		
		//make sure the time is far enough ahead and adjust accordingly.
		$earliest = date_create("now",new DateTimeZone(PBBOOKING_TIMEZONE))->modify('+ '.$pbbooking_config->prevent_bookings_within.' minutes');
		while ($this->date_start <= $earliest) {
			$this->date_start->modify('+ '.$pbbooking_config->time_increment.' minutes');
		}
		$this->time_increment = $pbbooking_config->time_increment;
		$this->treatment = $treatment;
    }

    /**
     * @since 3.2.0
     */
    private function filterCals(){
        $params = JFactory::getApplication()->getParams();
        $cal_id = JFactory::getApplication()->input->get('calendar',0,'integer');

        if ($cal_id && $cal_id > 0) {
            // Cals need to be filtered by the request param
            $this->cals = array($cal_id=>$GLOBALS["com_pbbooking_data"]["calendars"][$cal_id]);
        } else {
            // Cals need to be filtered by the menu param
            $this->cals = $GLOBALS['com_pbbooking_data']['calendars']; 
            $m_cals = $params->get("calendars");
            if ($m_cals) {
	            foreach ($this->cals as $i=>$cal) {
	                if (!in_array($i, $m_cals)) {
	                    unset($this->cals[$i]);
	                }
	            }            	
            }       
        }
    }
}