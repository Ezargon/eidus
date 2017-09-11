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
    	// Route to the layout based on the task.
    	$task = JFactory::getApplication()->input->getCmd("task");
    	switch ($task) {
    		case "create":
    		case "save":
    		case "validate":
    		case "doConfirmPayment":
            case "create":
            case "cron":
    			// The layout is already being defined in the controller
    			break;
            case "dayview":
                $this->prepareData();
                $this->filterCals();
                break;
    		default: {
    			$this->prepareData();
                $this->filterCals();
		    	//choose the view depending on which one the config is set to use and whether the user is authorised
		    	if ($this->user->authorise('pbbooking.browse','com_pbbooking')) {
					$this->setLayout(($this->config->multi_page_checkout == 0) ? 'calendar' : 'multipagecheckout');
		    	}
				else {
					$this->setLayout('notauthorised');
				}    			
    		}
    	}
		parent::display($tpl);
    }


    /**
     * Prepares the data for the view
     * @since 3.2.0
     * @todo This needs to be recoded in the v4 release to prevent duplication.
     * @todo The date calculations here are pretty old and could do with a re-write
     */

    private function prepareData() {
    	$this->config = $GLOBALS["com_pbbooking_data"]["config"];
    	$this->customfields = $GLOBALS['com_pbbooking_data']['customfields'];
		$this->now = date_create("now",new DateTimeZone(PBBOOKING_TIMEZONE));
		$this->treatments = \Pbbooking\Pbbookinghelper::get_valid_services();
		$this->user = JFactory::getUser();
		$this->shift_times = \Pbbooking\Pbbookinghelper::get_shift_times();
        $this->shifts = \Pbbooking\Pbbookinghelper::get_shift_times();

        //calc start and end day -- YUCK - this is a cut and paste out of the controller it needs a re-write
        $input = JFactory::getApplication()->input;
        $this->dateparam = date_create($input->get('dateparam','now','string'),new DateTimeZone(PBBOOKING_TIMEZONE));
        $opening_hours = json_decode($this->config->trading_hours,true);
        $this->opening_hours = $opening_hours[(int)$this->dateparam->format('w')];
        $start_time_arr = (isset($opening_hours[(int)$this->dateparam->format('w')]["open_time"])) ? str_split($opening_hours[(int)$this->dateparam->format('w')]['open_time'],2) : array();
        $end_time_arr = (isset($opening_hours[(int)$this->dateparam->format('w')]["close_time"])) ? str_split($opening_hours[(int)$this->dateparam->format('w')]['close_time'],2) : array();
        $this->day_dt_start = date_create($input->get('dateparam','now','string'),new DateTimezone(PBBOOKING_TIMEZONE));
        $this->day_dt_end = date_create($input->get('dateparam','now','string'),new DateTimezone(PBBOOKING_TIMEZONE));
        if (count($start_time_arr) ==2){
            $this->day_dt_start->setTime(isset($start_time_arr[0])?$start_time_arr[0]:0,$start_time_arr[1],0);
        }
        if (count($end_time_arr) == 2) {
            $this->day_dt_end->setTime($end_time_arr[0],$end_time_arr[1],0);
        }
        $this->earliest = date_create("now",new DateTimeZone(PBBOOKING_TIMEZONE));
        $this->earliest->modify('+ '.$this->config->prevent_bookings_within.' minutes');
        $this->last_slot_for_day = clone $this->day_dt_end->modify("- ".$this->config->time_increment." minutes");

		$this->master_trading_hours = json_decode($this->config->trading_hours,true);
		$this->latest = date_create("now",new DateTimeZone(PBBOOKING_TIMEZONE));
		if (isset($this->config->allow_booking_max_days_in_advance)) {
			$this->latest->modify('+ '.$this->config->allow_booking_max_days_in_advance.' days');
		}
	    	
		$this->dateparam = $GLOBALS['com_pbbooking_data']['dtdateparam'];

		$config =JFactory::getConfig();
    	$this->dateparam->setTimezone(new DateTimezone($config->get('offset')));
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