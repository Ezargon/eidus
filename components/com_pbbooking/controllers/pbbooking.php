<?php
/**
 * @license	    GNU General Public License version 2 or later; see  LICENSE.txt
 * @author      Eric Fernance
 *
 * @copyright   Eric Fernance
 */

defined("_JEXEC") or die("Invalid Direct Access");

class PbbookingControllerPbbooking extends JControllerLegacy
{

	public function display($cachable = false,$urlparams = array()) {
		
		parent::display($cachable, $urlparams);	

	}

    
    /**
     * 
     * saves the appointment to the pending_events table and routes validation emails
     * 
     */
    	
	function save()
	{
		$db =JFactory::getDBO();
		$config = $GLOBALS["com_pbbooking_data"]["config"];
		$user = JFactory::getUser();
		$input = JFactory::getApplication()->input;
		JPluginHelper::importPlugin('pbbooking');


		//check if user can save an appointment and bail early if they can't.
		if (!$user->authorise('pbbooking.create','com_pbbooking')) {
			$this->setRedirect('index.php?option=com_pbbooking',JText::_('COM_PBBOOKING_LOGIN_MESSAGE_CREATE'));
			return;
		}

		//check is user passed recaptcha and if not redirect
		if ($GLOBALS['com_pbbooking_data']['config']->enable_recaptcha) {
			$code= $input->get('recaptcha_response_field',null,'string');     
			JPluginHelper::importPlugin('captcha');
		   	$dispatcher = JEventDispatcher::getInstance();
		   	$res = $dispatcher->trigger('onCheckAnswer',$code);
		   	if(!$res[0]){
		   		$this->setRedirect(JRoute::_('index.php?option=com_pbbooking&dateparam='.$_POST['date']),JText::_('COM_PBBOOKING_INCORRECT_RECAPTCHA'));
		   		return;
		   }
		}

		$event = new \Pbbooking\Model\Event();
		if ($event->createFromPost($_POST)) {
		} else {
			if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']))
				$this->setRedirect(JRoute::_('index.php?option=com_pbbooking&dateparam='.$_POST['date']),JText::_('COM_PBBOOKING_BOOKING_PROBLEM'));
			return;
		}

		if ($event->isValid()) {
		} else {
			$this->setRedirect(JRoute::_('index.php?option=com_pbbooking&dateparam='.$_POST['date']),JText::_('COM_PBBOOKING_PROBLEM_EVENT_INVALID'));
			return;
		}
		
		//make sure that the email address is valid!
		if (!JMailHelper::isEmailAddress($event->email)) {
			$this->setRedirect(JRoute::_('index.php?option=com_pbbooking&dateparam='.$_POST['date']),JText::_('COM_PBBOOKING_PROBLEM_EVENT_INVALID_EMAIL'));
			return;
		}



		//create pending event and email user
		$pending_id = $event->save();

		//set the validation to paypal if dicated by the service
		$service = $db->setQuery('select * from #__pbbooking_treatments where id = '.(int)$event->service_id)->loadObject();
		if (isset($service->require_payment) && $service->require_payment == 1)
			$config->validation = 'paypal';

		if ($pending_id) {			
			//switch statement to handle the different validation types.
			switch ($config->validation) {
				case 'client':
					\Pbbooking\Pbbookinghelper::email_user($event);
					
					//now redirect - load up the view
					$view = $this->getView('Pbbooking','html');
					$view->setLayout('success');
					
					//populate needed data into the view.
					$view->service = $service;
					$view->config = $config;
					$view->event = new \Pbbooking\Model\Event($event->id);				//a fresh copy is loaded because if multi time zone is used the event is polluted during emailing.
					break;
				case 'auto':
					$this->setRedirect(JRoute::_('index.php?option=com_pbbooking&task=validate&id='.$event->id.'&email='.$event->email));
					return;
					break;
				case 'admin':

					//create the token and update the record with the token
					$event->validation_token = md5($event->id.$event->email.$event->dtstart->format('Ymd'));
					
					//THIS IS A BAD WORK AROUND TO FIX ISSUE #196
					$event->updateValidationToken();
					//END FIX
					
					\Pbbooking\Pbbookinghelper::send_admin_validation($event);
					
					//now redirect - load up the view
					$view = $this->getView('PBBooking','html');
					$view->setLayout('success');
					
					//populate needed data into the view.
					$view->service = $db->setQuery('select * from #__pbbooking_treatments where id = '.$db->escape($event->service_id))->loadObject();
					$view->config = $config;
					$view->event = $event;
					break;
				case 'paypal':
					if (PBBOOKING_VERSION == "free") {
						die("Paypal Payments are not supported in the free version");
					}
					$this->setupPayment($event,$service);
					return;
					break;
			}

			JEventDispatcher::getInstance()->trigger('onPendingEventCreated', array($event->id));

			//display the view
			$view->display();
				    	
		} else {
			$this->setRedirect(JRoute::_('index.php?option=com_pbbooking&dateparam='.$event->dtstart->format('Ymd')),JText::_('COM_PBBOOKING_BOOKING_PROBLEM'));				
		}

	}
	
	function validate() {
		
		$db=JFactory::getDBO();
		$input = JFactory::getApplication()->input;

		$pendingid = $input->get('id',0,'integer');
		$email = $input->get('email',null,'string');
		$token = $input->get('token',null,'string');
		JPluginHelper::importPlugin('pbbooking');

		$event = new \Pbbooking\Model\Event($pendingid);
		if ($event->id != $pendingid || $event->email != $email || ($GLOBALS["com_pbbooking_data"]["config"]->validation == 'admin' && $event->validation_token != $token)) {
			$this->setRedirect(JRoute::_('index.php?option=com_pbbooking'),JText::_('COM_PBBOOKING_VALIDATION_PROBLEM'));
			return;
		}

		if ($event->verified == 1) {
			$this->setRedirect(JRoute::_('index.php?option=com_pbbooking'),JText::_('COM_PBBOOKING_VALIDATION_PROBLEM_ALREADY_VERIFIED'));
			return;
		}


		if ($event->hasExpired()) {
			$view = $this->getView("Pbbooking","html");
			$view->setLayout("expired");
			$view->display();
			return;
		}


		//the event id and the email match and if using auto validation the token match so let's validate.
		$validated = $event->validate();

		if ($validated == false) {
			$this->setRedirect(JRoute::_('index.php?option=com_pbbooking'),JText::_('COM_PBBOOKING_VALIDATION_PROBLEM'));
			return;
		}

		//from here on it is assumed with have a succesfully validated event that has been wirtten to the relevant diaries.
		\Pbbooking\Pbbookinghelper::email_admin($event->id,$event->id);
		JEventDispatcher::getInstance()->trigger('onPendingEventValidated',array($event->id));

		//load up the view
		$view = $this->getView('PBBooking','html');
		$view->setLayout('validated');
		
		//populate the view with data
		$view->event = $event;
		$view->service = $db->setQuery("select * from #__pbbooking_treatments where id = ".$event->service_id)->loadObject();
		$view->calendar = $db->setQuery("select * from #__pbbooking_cals where id = ".$event->cal_id)->loadObject();
		$view->config = $GLOBALS["com_pbbooking_data"]["config"];

		//check if the appt is sent to auto validate as if it is we need to email the user
		if ($view->config->validation == 'auto') {
			\Pbbooking\Pbbookinghelper::send_auto_validate_email($event->id);
		}

		//check if the appt is set to admin validate as we need to let the user know it has been validated.
		if ($GLOBALS["com_pbbooking_data"]["config"]->validation == 'admin') {
			$email = \Pbbooking\Pbbookinghelper::prepare_message('admin_validation_confirmed',array('service_id'=>$event->service_id,'dtstart'=>$event->dtstart->format(DATE_ATOM),'url'=>null,'calendar'=>$view->calendar),json_decode($event->customfields_data,true));
			\Pbbooking\Pbbookinghelper::send_email($email['subject'],$email["body"],$event->email);
		}
		
		//display the view
		$view->display();
	}
	
	function error() {
		//$this->setLayout('fail');
		$input = JFactory::getApplcation()->input;
		$input->set('layout','fail');
		parent::display();
	}
	
	





	/**
	* renders the day view based on the free version layout. based on the code from the feww version with modifications.
	* @access public
	* @since 2.4.1
	*/

	public function dayview()
	{
		$input = JFactory::getApplication()->input;
		$config = $GLOBALS["com_pbbooking_data"]["config"];



		if ($config->enable_shifts == 1) {
			$input->set("layout",'dayviewinshifts');
		} else {
			$input->set("layout",'dayview');
		}

		parent::display();
	}

	/**
	* responds to the create task
	* @param string the date and time from the command ?dtstart=201209041030
	* @param string the cal_id &cal=1
	* @since 2.4.1
	* @access public
	*/
	public function create()
	{
		$input = JFactory::getApplication()->input;
		$db = JFactory::getDbo();
		$view = $this->getView('pbbooking','html');
		$config = $GLOBALS["com_pbbooking_data"]["config"];

		//push the dateparam into the view now cause we need it so often...
		$view->dateparam = date_create($input->get('dtstart',null,'string'), new DateTimeZone(PBBOOKING_TIMEZONE));
		$nday = date_create($view->dateparam->format(DATE_ATOM),new DateTimeZone(PBBOOKING_TIMEZONE)); //required for google cal compatiblility with setQueryMax
		$nday->modify('+1 day');

		//check if I'm working with shifts and get the relevant shift
		if ($config->enable_shifts == 1) {
			$shifts = \Pbbooking\Pbbookinghelper::get_shift_times();
			foreach ($shifts as $label=>$shift) {
				$shift_start = date_create($view->dateparam->format(DATE_ATOM),new DateTimeZone(PBBOOKING_TIMEZONE));
				$shift_end = date_create($view->dateparam->format(DATE_ATOM),new DateTimeZone(PBBOOKING_TIMEZONE));
				$shift_start->setTime($shift['start_time']['start_hour'],$shift['start_time']['start_min'],0);
				$shift_end->setTime($shift['end_time']['end_hour'],$shift['end_time']['end_min'],0);
				if ( $view->dateparam >= $shift_start && $view->dateparam <= $shift_end )
					$view->closing_time = date_create($shift_end->format(DATE_ATOM),new DateTimeZone(PBBOOKING_TIMEZONE));
			}
		} else {
			$opening_hours = json_decode($config->trading_hours,true);		
			$closing_time_arr = str_split( $opening_hours[date_create($view->dateparam->format(DATE_ATOM),new DateTimezone(PBBOOKING_TIMEZONE))->format('w')]['close_time'],2 );
			$view->closing_time = date_create($input->get('dtstart',null,'string'),new DateTimeZone(PBBOOKING_TIMEZONE));
			$view->closing_time->setTime($closing_time_arr[0],$closing_time_arr[1],0);
		}
		
		$dateparam = $input->get('dtstart',date_create('now',new DateTimeZone(PBBOOKING_TIMEZONE))->format('YmdHi'),'string');
		$cal_id = $input->get('cal_id',0,'integer');
		$opening_hours = json_decode($config->trading_hours,true);
		$closing_time_arr = str_split( $opening_hours[date_create($dateparam,new DateTimezone(PBBOOKING_TIMEZONE))->format('w')]['close_time'],2 );
		
		$view->dateparam = date_create($dateparam,new DateTimeZone(PBBOOKING_TIMEZONE));
		$view->customfields = $db->setQuery('select * from #__pbbooking_customfields order by ordering desc')->loadObjectList();
		$view->treatments = $db->setQuery('select * from #__pbbooking_treatments order by ordering desc')->loadObjectList();
		$view->cal = $GLOBALS['com_pbbooking_data']['calendars'][$cal_id];
		$view->longest_time = $view->cal->get_longest_available_booking($view->dateparam);

		$view->config = $config;

		$view->setLayout('create');
		$view->display();
	}

	/**
	* runs any pending cron jobs such as reminders etc
	* @access public
	* @since 2.4.2
	*/

	public function cron()
	{
		$db = JFactory::getDbo();
		$view = $this->getView('pbbooking','html');
		$view->setLayout('cron');
		JPluginHelper::importPlugin('pbbooking');


		$view->config = $GLOBALS['com_pbbooking_data']['config'];
		if ($view->config->enable_cron) {

			//what cron tasks do i need to do?
			if ($view->config->enable_reminders == 1) {
				$reminder_details = json_decode($view->config->reminder_settings,true);
				$date_from = date_create("today",new DateTimeZone(PBBOOKING_TIMEZONE));
				$date_from->modify('+ '.$reminder_details['reminder_days_in_advance'].' days');
				$date_to = date_create($date_from->format(DATE_ATOM),new DateTimeZone(PBBOOKING_TIMEZONE));
				$date_to->setTime(23,59,59);

				//get all the events I should send for...
				$events = $db->setQuery('select * from #__pbbooking_events where verified = 1 and externalevent = 0 and dtstart >= "'.$date_from->format(DATE_ATOM).'" and dtstart <= "'.$date_to->format(DATE_ATOM).'"')->loadObjectList();

				//loop through all the events and send the reminder...
				foreach ($events as $event) {
					if ($event->reminder_sent == 0) {
						if (\Pbbooking\Pbbookinghelper::send_reminder_email_for_event($event))
							//update the event with the status....
							$db->updateObject('#__pbbooking_events',new JObject(array('id'=>$event->id,'reminder_sent'=>1)),'id');
						JEventDispatcher::getInstance()->trigger('onEventReminderSent', array($event->id));
					}
				}
			}

			if ($view->config->enable_testimonials) {
				$date_from = date_create("today",new DateTimeZone(PBBOOKING_TIMEZONE));
				$date_from->modify('- '.$view->config->testimonial_days_after.' days');
				$date_from->setTime(0,0,0);
				$date_to = date_create($date_from->format(DATE_ATOM),new DateTimeZone(PBBOOKING_TIMEZONE));
				$date_to->setTime(23,59,59);

				//get all the events....
				$events = $db->setQuery('select * from #__pbbooking_events where verified = 1 and externalevent = 0 and dtstart >= "'.$date_from->format(DATE_ATOM).'" and dtstart <= "'.$date_to->format(DATE_ATOM).'"')->loadObjectList();

				foreach ($events as $event) {
					if ($event->testimonial_request_sent == 0) {
						if (\Pbbooking\Pbbookinghelper::send_testimonial_email_for_event($event))
							$db->updateObject('#__pbbooking_events',new JObject(array('id'=>$event->id,'testimonial_request_sent'=>1)),'id');
					}
				}
			}

			$view->display();
		} else {
			$this->setRedirect(JRoute::_('index.php?option=com_pbbooking'),JText::_('COM_PBBBOOKING_CRON_NOT_ENABLED'));
		}
	}





	/**
	 * This will load the payment confirmation - selection page for the user to choose a payment provider.
	 *
	 * @param    Event       $event      the event object
	 * @param    JObhject    $service    the service object
	 */

	protected function setupPayment($event,$service)
	{
		/*$plugins = JPluginHelper::getPlugin("hcspay");
		if (count($plugins) == 1) {
			echo "only one plugin";
			return;
		}*/

		// Multiple plugins so bring up selection box.
		JPluginHelper::importPlugin("hcspay");

		// Load the view and display.
		$view = $this->getView("Pbbooking","html");
		$view->setLayout("paymentoptions");
		$view->providers = JEventDispatcher::getInstance()->trigger("getPaymentProviders");
		$view->event = $event;
		$view->service  = $service;
		$view->display();
		
		return;
	}


	/**
	 * This will start the payment process with the chosen plugin
	 *
	 * @since 3.2
	 */

	public function doPluginPayment() {

		$input      = JFactory::getApplication()->input;
		$provider   = $input->get("provider");
		$event_id   = $input->get("event_id",null,"integer");
		$service_id = $input->get("service_id",null,"integer");
		
		if (!$provider) {
			throw new Exception("A provider needs to be specified", 1);
		}
		if (!$event_id || !$service_id) {
			throw new Exception("A service and event needs to be specified", 1);
		}
		$db = JFactory::getDbo();
		$db->setQuery("update #__pbbooking_events set payment_provider = '".$db->escape($provider)."' where id = ".(int)$event_id)->execute();

		$event     = new \Pbbooking\Model\Event($event_id);
		$service   = $event->getService();
		$returnUrl = (isset($_SERVER['HTTPS'])) ? 'https://' . $_SERVER['HTTP_HOST'].JRoute::_('index.php?option=com_pbbooking&task=pbbooking.doConfirmPayment&id='.$event->id) : 'http://' . $_SERVER['HTTP_HOST'].JRoute::_('index.php?option=com_pbbooking&task=pbbooking.doConfirmPayment&id='.$event->id);
		$cancelUrl = (isset($_SERVER['HTTPS'])) ? 'https://' . $_SERVER['HTTP_HOST'].JRoute::_('index.php?option=com_pbbooking&task=pbbooking.cancel') : 'http://' . $_SERVER['HTTP_HOST'].JRoute::_('index.php?option=com_pbbooking&task=pbbooking.cancel');

		JPluginHelper::importPlugin("hcspay",$provider);
		$result = JEventDispatcher::getInstance()->trigger("doSetupPayment",array($service->price,$service->pointscost,$returnUrl,$cancelUrl,"plg_pbbooking_pay_points"));

		if (is_string($result[0])) {
			echo $result[0];
		} else {
			// Means everthing went well.

		}

		return;
	}

	/**
	 * The return url lands here -- this will be the same for ALL plugins.
	 */

	public function doConfirmPayment()
	{

		$input = JFactory::getApplication()->input;
		$view = $this->getView('Pbbooking','html');

		$id       = $input->get('id',null,'integer');
		$event    = new \Pbbooking\Model\Event($id);
		$service  = $event->getService();

		if (!$id || !isset($event->id) || !isset($event->payment_provider)) { 
			die('The event is invalid.  This requires a valid event or an event that has been paid by a payment provider.');
		}

		if (!JPluginHelper::isEnabled("hcspay")) {
			die("There are no plguins enabled");
		}

		if (!JPluginHelper::importPlugin("hcspay",$event->payment_provider)) {
			die("The selected payment plugin could not be found.  Perhaps it has been unpublished");
		}

		$returnUrl = (isset($_SERVER['HTTPS'])) ? 'https://' . $_SERVER['HTTP_HOST'].JRoute::_('index.php?option=com_pbbooking&task=pbbooking.doConfirmPayment&id='.$event->id) : 'http://' . $_SERVER['HTTP_HOST'].JRoute::_('index.php?option=com_pbbooking&task=pbbooking.doConfirmPayment&id='.$event->id);
		$cancelUrl = (isset($_SERVER['HTTPS'])) ? 'https://' . $_SERVER['HTTP_HOST'].JRoute::_('index.php?option=com_pbbooking&task=pbbooking.cancel') : 'http://' . $_SERVER['HTTP_HOST'].JRoute::_('index.php?option=com_pbbooking&task=pbbooking.cancel');

		$result = JEventDispatcher::getInstance()->trigger("doConfirmPayment",array($service->price,$service->pointscost,$returnUrl,$cancelUrl,"plg_pbbooking_pay_points"));

		if ($result[0] == false) {
			$view->setLayout("fail");
		} else {
			// Means everthing went well.
            \Pbbooking\Pbbookinghelper::confirm_payment($event->id,sprintf('%0.2f',$service->price));
            $view->setLayout('paymentpending');

		}

		$view->display();
		return;

	}

	
}