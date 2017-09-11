<?php
/**
 * @package		Hot Chilli Software PBBooking
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.hotchillisoftware.com
 */
namespace Pbbooking;

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


class Pbbookinghelper
{


    /**
     * email_user - sends the validation email to the user with format defined in configuration.
     * 			- New in 2.2 this method now supports all customfield tags based on |*varname*|
     *			- This method has been heavily modified in the 2.4.5.11a4 release to move to pending events.
     *
     * @param array data the array of appt specific data
     */
    static function email_user($event)
    {
        $db = \JFactory::getDbo();
        $config = $GLOBALS['com_pbbooking_data']['config'];
        $customfields = json_decode($event->customfields_data,true);
        $calendar = $db->setQuery('select * from #__pbbooking_cals where id = '.$db->escape($event->cal_id))->loadObject();

        //if the useroffset is set then the dtstart needs to be converted to the user's time zone.
        if($config->user_offset == 1) {
            $pending_dtstart = \Pbbooking\Pbbookinghelper::pbbConvertTimezone($event->dtstart,$event->user_offset);
            $event->dtstart = $pending_dtstart;
        }

        $mailer_config =\JFactory::getConfig();

        $recipient = $event->email;
        $bcc = null;
        if ($config->bcc_admin == 1) {
            $bcc = (isset($config->notification_email)) ? array($config->notification_email) : array($mailer_config->get('mailfrom'));
            if (isset($calendar->email)) $bcc[] = $calendar->email;
        }
        if (\JURI::base(true) != '') {
            $url = str_replace(\JURI::base(true).'/','',\JURI::base()).\JRoute::_('index.php?option=com_pbbooking&task=pbbooking.validate&id='.$event->id.'&email='.$event->email);
        } else {
            $url = preg_replace('/(.*)\/$/','$1',\JURI::base()).\JRoute::_('index.php?option=com_pbbooking&task=pbbooking.validate&id='.$event->id.'&email='.$event->email);
        }

        $urlstring = '<a href="'.$url.'">'.\JText::_('COM_PBBOOKING_VALIDATE_ANCHOR_TEXT')."</a>";

        //send email to client to let them know what is going on
        $email = self::prepare_message('client_validation_email',array('service_id'=>$event->service_id,'dtstart'=>$event->dtstart->format(DATE_ATOM),'url'=>$urlstring,'calendar'=>$calendar, "id"=>$event->id),$customfields);

        self::send_email($email['subject'],$email["body"],$recipient,$bcc);
    }



    /**
     * email admin - sends email to the administrator notifying them of a new appt in the calendar.
     *
     * @param int the id of the event in the database
     * @param int the id of the pending event in the databse
     */

    static function email_admin($event_id,$pending_id)
    {
        //load up data
        $db = \JFactory::getDbo();
        $config = $GLOBALS['com_pbbooking_data']['config'];
        $event = new \Pbbooking\Model\Event($event_id);
        $customdata = json_decode($event->customfields_data,true);
        $service = $db->setQuery('select * from #__pbbooking_treatments where id = '.(int)$event->service_id)->loadObject();
        $calendar = $db->setQuery('select * from #__pbbooking_cals where id = '.(int)$event->cal_id)->loadObject();

        //build email
        $email = self::prepare_message('admin_notification',array('service_id'=>$service->id,'dtstart'=>$event->dtstart->format(DATE_ATOM),'calendar'=>$calendar),$customdata);


        $email["body"] .= '<br><b>'.\JText::_('COM_PBBOOKING_SUCCESS_DATE').'</b> '.\JHtml::_('date',$event->dtstart->format(DATE_ATOM),\JText::_('COM_PBBOOKING_SUCCESS_DATE_FORMAT').' '.\JText::_('COM_PBBOOKING_SUCCESS_TIME_FORMAT'));
        $email["body"] .= '<br><b>'.\JText::_('COM_PBBOOKING_BOOKINGTYPE').'</b> '.PBBookingHelper::print_multilang_name($service,'service');
        $email["body"] .= '<ul>';
        $email["body"] .= '<p>'.\JText::_('COM_PBBOOKING_EDIT_LINK_MSG').' ';
        $email["body"] .= '<a href="'.\JURI::root(false).'/administrator/index.php?option=com_pbbooking&task=manage.display&dateparam='.$event->dtstart->format('Y-m-d').'">';
        $email["body"] .= \JURI::root(false).'/administrator/index.php?option=com_pbbooking&task=manage.display&dateparam='.$event->dtstart->format('Y-m-d').'</a></p>';
        foreach ($customdata as $data) {
            $email["body"] .= '<li>'.$data['fieldname'].'  - '.$data['data'].'</li>';
        }
        $email["body"] .- '</ul>';

        //build recipient list
        $mailer_config =\JFactory::getConfig();
        $recipient = array();
        if ($calendar->email) {
            $recipient[] = $calendar->email;
        }
        $recipient[] = (isset($config->notification_email) && $config->notification_email != '') ? $config->notification_email : $mailer_config->get('mailfrom');

        self::send_email($email['subject'],$email["body"],$recipient);
    }


    /**
     * get_shift_times() - returns an assoc array of shift times
     *
     * @return array an assoc array of shift times array('shift name'=>array('start_time'=>array(start_hour,start_min),'end_time'=>array(end_hour,end_min)))
     * @since 2.2
     */

    public static function get_shift_times()
    {
        $config = $GLOBALS['com_pbbooking_data']['config'];

        //get start_hour start_min end_hour end_min for groupings
        $groupings = array();

        if ($config->enable_shifts == 1) {
            if ($config->time_groupings) {
                foreach (json_decode($config->time_groupings,true) as $k=>$v) {
                    $start_time = str_split($v['shift_start'],2);
                    $end_time = str_split($v['shift_end'],2);
                    $groupings[$k] = array('start_time'=>array('start_hour'=>(int)ltrim($start_time[0],'0'),
                        'start_min'=>(int)ltrim($start_time[1],'0')),
                        'end_time'=>array('end_hour'=>(int)ltrim($end_time[0],'0'),
                            'end_min'=>(int)ltrim($end_time[1],'0')),
                        'display_label'=>(isset($v['display_label'])) ? $v['display_label'] : $k);
                }
            }
        } else {
            $trading_hours = json_decode($config->trading_hours,true);
            $earliest_start = date_create("now",new \DateTimeZone(PBBOOKING_TIMEZONE));
            $latest_finish = date_create("now",new \DateTimeZone(PBBOOKING_TIMEZONE));
            $earliest_start->setTime(23,59,59);
            $latest_finish->setTime(0,0,0);
            foreach ($trading_hours as $hours) {
                if ($hours['status'] == 'open') {
                    $open_time = str_split($hours['open_time'],2);
                    $close_time = str_split($hours['close_time'],2);
                    $test_date = date_create("now",new \DateTimeZone(PBBOOKING_TIMEZONE));

                    //test the opening time
                    $test_date->setTime((int)$open_time[0],(int)$open_time[1],0);
                    if ($test_date < $earliest_start)
                        $earliest_start->setTime((int)$open_time[0],(int)$open_time[1],0);

                    //test the closing time
                    $test_date->setTime((int)$close_time[0],(int)$close_time[1],0);
                    if ($test_date > $latest_finish)
                        $latest_finish->setTime((int)$close_time[0],(int)$close_time[1],0);
                }
            }

            //create the groupings array
            $groupings['allday'] = array('start_time'=>array('start_hour'=>$earliest_start->format('G'),'start_min'=>ltrim($earliest_start->format('i'),0)),
                'end_time'=>array('end_hour'=>$latest_finish->format('G'),'end_min'=>ltrim($latest_finish->format('i'),0)),
                'display_label'=>'ALLDAY');
        }
        return $groupings;
    }

    /**
     * get_shift_for_appointments
     *
     * @param int id - the id of the event
     * @return string the shift label
     * @deprecated - only used by ajax_edit at the moment
     */

    public static function get_shift_for_appointment($id)
    {
        $db = \JFactory::getDbo();
        $db->setQuery('select * from #__pbbooking_config');
        $config = $db->loadObject();

        date_default_timezone_get(PBBOOKING_TIMEZONE);

        $db->setQuery('select * from #__pbbooking_events where id = '.$db->escape($id));
        $event = $db->loadObject();

        $shift_times = self::get_shift_times();
        $dtstart = date_create($event->dtstart,new \DateTimeZone(PBBOOKING_TIMEZONE));

        foreach ($shift_times as $k=>$v) {
            $shift_start = date_create($dtstart->format(DATE_ATOM),new \DateTimeZone(PBBOOKING_TIMEZONE));
            $shift_end = date_create($dtstart->format(DATE_ATOM),new \DateTimeZone(PBBOOKING_TIMEZONE));

            $shift_start->setTime((int)$v['start_time']['start_hour'],(int)$v['start_time']['start_min']);
            $shift_end->setTime((int)$v['end_time']['end_hour'],(int)$v['end_time']['end_min']);

            if ($dtstart>= $shift_start && $dtstart <= $shift_end) {
                return $k;
            }
        }

    }

    /**
     *
     * send_admin_validation - sends a validation request to the admin to validate the appointment
     *
     * @param array $data the data array containing appointment details
     * @return boolean success or failure
     * @since 2.2.2
     */

    public static function send_admin_validation($event)
    {
        $db = \JFactory::getDbo();
        $config= $GLOBALS['com_pbbooking_data']['config'];
        $customfields = json_decode($event->customfields_data,true);
        $calendar = $db->setQuery('select * from #__pbbooking_cals where id = '.(int)$event->cal_id)->loadObject();

        $url = (isset($_SERVER['HTTPS'])) ? 'https://' : 'http://';
        $url .= $_SERVER['HTTP_HOST'].\JRoute::_('index.php?option=com_pbbooking&task=validate&id='.$event->id.'&token='.$event->validation_token.'&email='.$event->email);
        $urlstring = '<a href="'.$url.'">'.\JText::_('COM_PBBOOKING_VALIDATE_ANCHOR_TEXT')."</a>";

        //build the email body
        $email = self::prepare_message('email_body',array('service_id'=>$event->service_id,'dtstart'=>$event->dtstart->format(DATE_ATOM),'url'=>$urlstring,'calendar'=>$calendar),(array)$customfields);

        //build the recipent list
        $mailer_config =\JFactory::getConfig();
        $recipient = (isset($config->notification_email) && $config->notification_email !='') ? array($config->notification_email) : array($mailer_config->get('mailfrom'));
        if ($calendar->email) $recipient[] = $calendar->email;


        //send_email to admin for validation....
        self::send_email($email["subject"],$email["body"],$recipient);

        //send email to client to let them know what is going on
        $email = self::prepare_message('admin_validation_pending_email',array('service_id'=>$event->service_id,'dtstart'=>$event->dtstart->format(DATE_ATOM),'url'=>$urlstring,'calendar'=>$calendar),(array)$customfields);
        self::send_email($email['subject'],$email["body"],$event->email);
    }

    /**
     * gets valid services where there are linked calendars.  Ths method is used to fix an error where services without calendars
     * were still being displayed in the front end.
     *
     * @return array an array of service objects
     * @since 2.3
     */

    public static function get_valid_services()
    {
        //first get list of all services
        $db = \JFactory::getDbo();
        $db->setQuery('select * from #__pbbooking_treatments order by ordering DESC');
        $services = $db->loadObjectList();

        //loop through the arr to see if we have valids
        $ret_services = array();
        foreach ($services as $service) {
            $add_service = false;
            foreach (explode(',',$service->calendar) as $cal_id) {
                if ( in_array($cal_id, array_keys($GLOBALS['com_pbbooking_data']['calendars']) ) )
                    $add_service = true;
            }
            if ($add_service)
                $ret_services[] = $service;
        }
        return $ret_services;
    }


    /**
     * gets a calendar name for a specified calendar id
     * @param int the id of the calenar
     * @return string the calendar name
     * @since 2.3
     */

    public static function get_calendar_name_for_id($cal_id)
    {
        $db = \JFactory::getDbo();
        $db->setQuery('select * from #__pbbooking_cals where id = '.$db->escape($cal_id));
        $calendar = $db->loadObject();

        return $calendar->name;
    }

    /**
     * sends an email to the user when an event is marked for auto validation.
     * @param int the id of the event to send the validation email to
     * @return bool
     * @since 2.3
     */

    public static function send_auto_validate_email($event_id)
    {

        $db = \JFactory::getDbo();
        $config = $GLOBALS['com_pbbooking_data']['config'];
        $event = new \Pbbooking\Model\Event($event_id);
        $service = $db->setQuery('select * from #__pbbooking_treatments where id = '.$db->escape($event->service_id))->loadObject();
        $calendar = $db->setQuery('select * from #__pbbooking_cals where id = '.(int)$event->cal_id)->loadObject();

        //send email to client to let them know what is going on
        $email = self::prepare_message('auto_validated_appt_email',array('service_id'=>$service->id,'dtstart'=>$event->dtstart->format(DATE_ATOM),"id"=>$event->id,'url'=>null,'calendar'=>$calendar),json_decode($event->customfields_data,true));
        self::send_email($email['subject'],$email["body"],$event->email);

    }

    /**
     * a function to handle sending of mail from pbbooking.  We send a few emails to let's centralise the code
     * @param string the message subject
     * @param string the message body
     * @param string the recipient
     * @param string the bcc
     * @return bool
     * @since 2.3
     */

    public static function send_email($subject,$body,$recipient,$bcc=null)
    {

        $mailer =\JFactory::getMailer();
        $config =\JFactory::getConfig();
        $mailer->setSender(array($config->get('mailfrom'),$config->get('fromname')));

        $mailer->addRecipient($recipient);
        // Fix for issue documented here: http://joomla.stackexchange.com/questions/16051/fix-the-invalid-address-error-after-upgrading-to-joomla-3-5-1
        if (isset($bcc) && is_array($bcc)) {
            foreach ($bcc as $email) {
                if (\JMailHelper::isEmailAddress($email)) {
                    $mailer->addBCC($email);
                }
            }
        } else {
            $mailer->addBCC($bcc);
        }

        $mailer->setSubject($subject);
        $mailer->isHTML(true);

        $mailer->setBody($body);
        $mailer->Send();

        return true;

    }


    /**
     * prepares or sms message for sending loads the template from new message centre, parses custom field tags
     * @param string the template to load and parse
     * @param assoc array containing appointment details
     * @param assoc array containing the custom fields and data
     *
     * @return array assoc array containing the email body and email subject
     * @since 2.3.1
     * @access public
     * @throws \Exception;
     */

    public static function prepare_message($template, $details, $customfields, $type="email")
    {
        $db       = \JFactory::getDbo();
        $config   = $GLOBALS['com_pbbooking_data']['config'];
        $service  = $db->setQuery('select * from #__pbbooking_treatments where id = '.$db->escape($details['service_id']))->loadObject();
        $calendar = $details['calendar'];

        if ($type == "email") {
            // Currently only emails allow multi language overrides.
            $message = self::get_multilang_message($template);
        }

        if ($type == "sms") {
            $message = $db->setQuery("select sms as body from #__pbbooking_smss where sms_tag = '".$db->escape($template)."'")->loadAssoc();
        }


        if (!$message) {
            // Unlikely but possible we've got no message.
            return;
        }

        //parse custom fields tags
        foreach ($customfields as $customfield) {
            $message["body"] = preg_replace('/\|\*'.$customfield['varname'].'\*\|/',$customfield['data'],$message["body"]);
        }

        //append service details if required
        $booking_details_template = $config->booking_details_template;

        $service_arr = (array)$service;
        $service_arr['name'] = \Pbbooking\Pbbookinghelper::print_multilang_name($service,'service');
        $service_arr['price'] = (string)PBBookingHelper::pbb_money_format($service_arr['price']);
        $calendar_arr = (array)$calendar;

        $twig = new \Twig_Environment(new \Twig_Loader_String());
        $booking_details = $twig->render($booking_details_template,
                      array('COM_PBBOOKING_SUCCESS_DATE'=>\JText::_('COM_PBBOOKING_SUCCESS_DATE'),
                        'COM_PBBOOKING_SUCCESS_TIME'=>\JText::_('COM_PBBOOKING_SUCCESS_TIME'),
                        'dstart'=>\JHtml::_('date',date_create($details['dtstart'],new \DateTimeZone(PBBOOKING_TIMEZONE))->format(DATE_ATOM),\JText::_('COM_PBBOOKING_SUCCESS_DATE_FORMAT')),
                        'dtstart'=>\JHtml::_('date',date_create($details['dtstart'],new \DateTimeZone(PBBOOKING_TIMEZONE))->format(DATE_ATOM),\JText::_('COM_PBBOOKING_SUCCESS_TIME_FORMAT')),
                        'COM_PBBOOKING_BOOKINGTYPE'=>\JText::_('COM_PBBOOKING_BOOKINGTYPE'),
                        'service'=>$service_arr,
                        'calendar'=>$calendar_arr,
                        'customfields'=>$customfields)
                );

        $message["body"] = str_ireplace('|*booking_details*|', $booking_details, $message["body"]);
        $message["body"] = str_ireplace("|*service*|", $service_arr["name"], $message["body"]);
        $message["body"] = str_ireplace("|f|", \JHtml::_('date',date_create($details['dtstart'],new \DateTimeZone(PBBOOKING_TIMEZONE))->format(DATE_ATOM),\JText::_('COM_PBBOOKING_SUCCESS_DATE_FORMAT')." ".\JText::_('COM_PBBOOKING_SUCCESS_TIME_FORMAT')), $message["body"]);

        // If the ID has been passed chances are we want the booking reference generated.
        if (isset($details["id"])) {
            $message["body"] = str_ireplace("|*booking_ref*|", strtoupper(substr(md5($details["id"]),0,4).$details["id"]), $message["body"]);
        }


        //append url string if we have it.....
        if (isset($details["url"])) {
            $message["body"] = preg_replace('/\|\*URL\*\|/',$details['url'],$message["body"]);
        }

        $message["body"] = preg_replace('/\|\*calendar_name\*\|/',PBBookingHelper::print_multilang_name($calendar,'calendar'),$message["body"]);


        //return completed string
        return $message;
    }

    /**
     * receives paypal payment details from notify and marks appt as validated and saves to the datase
     * @param int the id of the pending event
     * @param float the amount of the payment
     * @since 2.4
     * @access public
     */

    public static function confirm_payment($pending_id, $payment_gross)
    {

        $db = \JFactory::getDbo();
        $jconfig = \JFactory::getConfig();

        $event = new \Pbbooking\Model\Event($pending_id);
        $calendar = $db->setQuery('select * from #__pbbooking_cals where id = '.(int)$event->cal_id)->loadObject();
        $customfields = json_decode($event->customfields_data,true);
        $config = $GLOBALS['com_pbbooking_data']['config'];

        if ($event && $event->verified == 0) {			//only send the & validate the event once! previously this was sending multiple times see issue #233
            //got a pending event need to validate.....
            $event->validate();

            //if the useroffset is set then the dtstart needs to be converted to the user's time zone.
            if($config->user_offset == 1) {
                $pending_dtstart = \Pbbooking\Pbbookinghelper::pbbConvertTimezone($event->dtstart,$event->user_offset);
                $event->dtstart = $pending_dtstart;
            }

            //send email to client
            $email = self::prepare_message('client_payment_confirmed_email',array('dtstart'=>$event->dtstart->format(DATE_ATOM),'url'=>null,'service_id'=>$event->service_id,'calendar'=>$calendar),$customfields);
            self::send_email($email['subject'],$email["body"],$event->email);


            $email = self::prepare_message('admin_payment_confirmed_email',array('dtstart'=>$event->dtstart->format(DATE_ATOM),'url'=>null,'service_id'=>$event->service_id,'calendar'=>$calendar),$customfields);
            self::send_email($email['subject'],$email["body"],$event->email);

        }
    }

    /**
     * checks to see whether the day is a trading day and returns true or false. adapted from method by the same name in the free version but specifically for commercial.
     * updated in 2.4.5.8 to make single calendar implemnentatons more user friendly.
     * @param datetime the datetime to check
     * @param cals an array of calendar objects all nicely preloaded with events.
     * @return bool true or false
     * @since 2.4.1
     * @access public
     */
    public static function free_appointments_for_day($curr_day,$cal_arr=array())
    {
        $db = \JFactory::getDbo();
        $config = $GLOBALS['com_pbbooking_data']['config'];

        $trading_hours = json_decode($config->trading_hours,true);
        if ($trading_hours[$curr_day->format('w')]['status'] == 'closed')
            return false;


        foreach ($cal_arr as $cal) {
            if ($cal->exceededMaxBookingsForDay($curr_day)) {
                //do nothing it's exceeded bookings and will return false in due course
            } else {
                //the calendar hasn't exceeded it's possible # of bookings so check if there's actually time available
                if ($cal->timeAvailableForDay($curr_day)) {
                    return true;
                }
            }
        }

        return false;           //default case - either no free time of calendar max_bookings are exceeded.

    }

    /**
     * send a reminder email for the specified event using
     * @param object the event object
     * @return book true or false
     * @since 2.4.2
     * @access public
     */

    public static function send_reminder_email_for_event($event)
    {
        $db = \JFactory::getDbo();
        $config = $GLOBALS['com_pbbooking_data']['config'];
        $calendar = $db->setQuery('select * from #__pbbooking_cals where id = '.(int)$event->cal_id)->loadObject();

        //if the useroffset is set then the dtstart needs to be converted to the user's time zone.
        if($config->user_offset == 1) {
            $pending_dtstart = \Pbbooking\Pbbookinghelper::pbbConvertTimezone(date_create($event->dtstart,new \DateTimeZone(PBBOOKING_TIMEZONE)),$event->user_offset);
            $event->dtstart = $pending_dtstart->format(DATE_ATOM);
        }


        $email = self::prepare_message('reminder_email',array('service_id'=>$event->service_id,'dtstart'=>$event->dtstart,'url'=>null,'calendar'=>$calendar),json_decode($event->customfields_data,true));
        self::send_email($email["subject"],$email["body"],$event->email,$bcc=null);

        return true;

    }

    /**
     * send a testimonal request email for the specified event
     * @param object the event object
     * @return bool true or false
     * @since 2.4.3
     * @access public
     */

    public static function send_testimonial_email_for_event($event)
    {
        $db = \JFactory::getDbo();
        $config = $GLOBALS['com_pbbooking_data']['config'];
        $calendar = $db->setQuery('select * from #__pbbooking_cals where id = '.(int)$event->cal_id)->loadObject();


        if (\JURI::base(true) != '')
            $url = str_replace(\JURI::base(true).'/','',\JURI::base()).\JRoute::_('index.php?option=com_pbbooking&task=survey&email='.$event->email.'&id='.$event->id);
        else
            $url = preg_replace('/(.*)\/$/','$1',\JURI::base()).\JRoute::_('index.php?option=com_pbbooking&task=survey&email='.$event->email.'&id='.$event->id);
        $urlstring = '<a href="'.$url.'">'.\JText::_('COM_PBBOOKING_SURVEY_ANCHOR_TEXT')."</a>";

        $email = self::prepare_message('survey_email',array('url'=>$urlstring,'service_id'=>$event->service_id,'dtstart'=>$event->dtstart,'calendar'=>$calendar),json_decode($event->customfields_data,true));
        self::send_email($email["subject"],$email["body"],$event->email,$bcc=null);

        return true;
    }


    /**
     * check for errors
     * @return array or null
     * @since 2.4.5.4
     */

    public static function check_for_errors()
    {
        $app = \JFactory::getApplication();
        $user = \JFactory::getUser();
        $input = \JFactory::getApplication()->input;


        //check the user time zone
        $user_tz = $user->getParam('timezone');
        if (isset($user_tz) && $user_tz != PBBOOKING_TIMEZONE) {
            $app->enqueueMessage(\JText::_('COM_PBBOOKING_TIMEZONE_MISMATCH'),'warning');
        }
    }


    /**
     * print mulitilingual name
     * @param object the object to return the international name for
     * @param string what type of object is it?
     * @return the internatalized name if needed or fall back if undefined or not international
     * @since 2.4.5.11
     */

    public static function print_multilang_name($object,$type)
    {
        $db = \JFactory::getDbo();
        $lang = \JFactory::getLanguage();


        $config = $GLOBALS['com_pbbooking_data']['config'];
        $tag = $lang->getTag();

        if ($config->enable_multilanguage == 1) {
            //return the multi language value
            if ($type != 'shift')
                $override = $db->setQuery('select * from #__pbbooking_lang_override where type = "'.$db->escape($type).'" and original_id = '.(int)$object->id.' and langtag="'.$db->escape($tag).'"')->loadObject();

            switch ($type) {
                case 'service':
                    return (isset($override->data)) ? $override->data : $object->name;
                    break;
                case 'calendar':
                    return (isset($override->data)) ? $override->data : $object->name;
                    break;
                case 'shift':
                    $label = \JText::_('COM_PBBOOKING_SHIFT_MULTI_'.strtoupper($object['key']));
                    return $label;
                    break;
                case 'customfield':
                    return (isset($override->data)) ? $override->data : $object->fieldname;
                    break;
            }
        } else {
            switch ($type) {
                case 'service':
                    return $object->name;
                    break;
                case 'shift':
                    return $object['value']['display_label'];
                    break;
                case 'customfield':
                    return $object->fieldname;
                    break;
            }
        }

        return $object->name;   //fall back....
    }

    /**
     * load the message objects - returns an array of two strings with the subject in one and the message in the other
     * @param string the message identified
     * @return array an array with the mesage body and subject
     * @access public
     * @since 2.4.5.11
     * @throws \Exception
     */

    public static function get_multilang_message($ident)
    {

        $db = \JFactory::getDbo();
        $lang = \JFactory::getLanguage();
        $config = $GLOBALS['com_pbbooking_data']['config'];

        $multilang = array();

        if ($config->enable_multilanguage == 1) {
            // is multi lang find override.

            $body   = $db->setQuery("select * from #__pbbooking_lang_override where type = 'message' and langtag = '".$db->escape($lang->getTag())."' and messagename='".$db->escape($ident)."'")->loadObject();
            $subject = $db->setQuery("select * from #__pbbooking_lang_override where type = 'subject' and langtag = '".$db->escape($lang->getTag())."' and messagename='".$db->escape($ident)."'")->loadObject();


            $multilang["body"]    = ($body) ? $body->data : null;
            $multilang["subject"] = ($subject) ? $subject->data : null;

            // If both the body and subject were found return them now.
            if ($multilang["body"] && $multilang["subject"]) {
                return $multilang;
            }
        }

        // Multi lang was disabled, or one o fthe overrides were missing
        $email = $db->setQuery("select * from #__pbbooking_emails where email_tag = '".$db->escape($ident)."'")->loadObject();

        if (!$email) {
            throw new \Exception("No multi lang override or default template found");
        }

        return array(
            'body'=>(isset($multilang["body"]) && $multilang["body"]) ? $multilang["body"] : $email->emailbody,
            'subject'=>(isset($multilang["subject"]) && $multilang["subject"]) ? $multilang["subject"] : $email->emailsubject
        );
    }

    /**
     * sends alerts to the admin as required.  pulls settings from the language files
     * @param string message subject
     * @param string message body
     * @param array optional data
     */

    public static function send_admin_alert($subject,$body,$details)
    {
        $config = \JFactory::getConfig();
        $mailer = \JFactory::getMailer();

        $sender = array($config->get('mailfrom'),$config->get('fromname'));

        $mailer->setSender($sender);
        $mailer->addRecipient($config->get('mailfrom'));
        $mailer->setSubject(\JText::_($subject));

        $body = \JText::_($body);
        $body.="\n\n".\JText::_('COM_PBBOOKING_DATE_OF_ALERT').' '.$details['date']."\n".\JText::_('COM_PBBOOKING_REMOTE_IP_ADDRESS').' '.$details['remote_ip'];
        $mailer->setBody($body);

        $mailer->Send();

    }


    /**
     * my own implementation of number format used mainly to build up a service price.
     * @since 2.4.5.11a4
     * @param decimal the amount to format
     * @return string
     */

    public static function pbb_money_format($amount,$excludeSymbol=false)
    {
        $config = $GLOBALS['com_pbbooking_data']['config'];

        //if $excludeSymbol = true then changes are we're formatting for paypal so use the paypal specifi format
        if ($excludeSymbol == true)
            $currency = number_format((float)$amount,(int)\JText::_('COM_PBBOOKING_CURRENCY_NUM_DECIMALS'),\JText::_('COM_PBBOOKING_CURRENCY_SYMBOL_DECIMAL_PAYPAL'),\JText::_('COM_PBBOOKING_CURRENCY_SYMBOL_THOUSANDS'));
        else
            $currency = number_format((float)$amount,(int)\JText::_('COM_PBBOOKING_CURRENCY_NUM_DECIMALS'),\JText::_('COM_PBBOOKING_CURRENCY_SYMBOL_DECIMAL'),\JText::_('COM_PBBOOKING_CURRENCY_SYMBOL_THOUSANDS'));

        if ($excludeSymbol)
            return $currency;

        return ($config->currency_symbol_before==1) ? \JText::_('COM_PBBOOKING_CURRENCYSYMBOL').$currency : $currency.\JText::_('COM_PBBOOKING_CURRENCYSYMBOL');
    }

    /**
     * a little time converter function to save having to do this over and over
     * @since 2.4.5.11a7
     * @param DateTime the date time object to convert
     * @param int the user offset in minutes (signed) as returned from the Javascript post and stored with the event
     * @return DateTime the resulting datetime object after converting
     */

    public static function pbbConvertTimezone($dt,$userOffset)
    {
        $dtstart_user = date_create($dt->format(DATE_ATOM),new \DateTimeZone(PBBOOKING_TIMEZONE));	// this is the same date in the users time zone.;
        $dtz_server = new \DateTimeZone(PBBOOKING_TIMEZONE);
        $utc_offset = $dtz_server->getOffset($dtstart_user);

        //first get the time as if UTC...
        $dtstart_user->modify((($utc_offset>0) ? '- ' : '+').abs($utc_offset).' seconds');

        //now modify based on users offset from utc.
        $modifier = ($userOffset > 0) ? '-' : '+';
        $modifier .= abs($userOffset);
        $modifier .= ' minutes';
        $dtstart_user->modify($modifier);			//adjust for the offset.  This now is in the server time zone but the users time! DONT mess with the TZ

        return $dtstart_user;
    }


    /**
     * sets up the pbbooking global address space
     * @param    bool    $needsCalEvents    set to true to determine whether calevents are needed.
     */

    public static function bootstrapPbbooking($needsCalEvents = true)
    {
        $db = \JFactory::getDbo();
        $input = \JFactory::getApplication()->input;
        $config = $db->setQuery('select * from #__pbbooking_config')->loadObject();

        $pbbookinginfo = array();
        $pbbookinginfo['config'] = $config;
        $pbbookinginfo['customfields'] = $db->setQuery("select * from #__pbbooking_customfields order by ordering ASC")->loadObjectList();
        $pbbookinginfo['services'] = $db->setQuery('select * from #__pbbooking_treatments order by ordering ASC')->loadObjectList();
        $GLOBALS['com_pbbooking_data'] = $pbbookinginfo;                //this line is needed as the bootstrapping code pulls a reference to the config

        //if there is a dateparam set then let's grab it
        $dateparam = $input->get('dateparam',null,'string');


        if ($dateparam)
            $pbbookinginfo['dtdateparam'] = new \DateTime($dateparam,new \DateTimeZone(PBBOOKING_TIMEZONE));
        else
            $pbbookinginfo['dtdateparam'] = new \DateTime("now",new \DateTimeZone(PBBOOKING_TIMEZONE));


        $dtbegin = clone $pbbookinginfo['dtdateparam'];
        $dtend = clone $pbbookinginfo['dtdateparam'];
        $dtbegin->modify('first day of')->setTime(0,0,0);
        $dtend->modify('last day of')->setTime(23,59,59);

        //now let's get the calendar objects and create an array of calendar objects for each ID.
        $calendars = array();
        $calIds = $db->setQuery('select * from #__pbbooking_cals order by ordering DESC')->loadObjectList();
        foreach ($calIds as $calId) {
            $calendars[$calId->id] = new \Pbbooking\Model\Calendar($config);
            $calendars[$calId->id]->loadCalendarFromDbase(array($calId->id),$dtbegin,$dtend,$needsCalEvents);
        }
        $pbbookinginfo['calendars'] = $calendars;
        $pbbookinginfo['blockdays'] = $db->setQuery("select * from #__pbbooking_block_days")->loadObjectList();


        $GLOBALS['com_pbbooking_data'] = $pbbookinginfo;



    }


    /**
     * sets up jQuery to be internationational.  Currently only handles front but will transition this to doing the back end
     *
     * @param    bool    $front    true if being access from the site or false if beign accessed from admin.
     */


    public static function jqueryUiInternationalise($front = true)
    {

        $config = \JFactory::getConfig();
        $lang = \JLanguage::getInstance($config->get('language'));
        $db = \JFactory::getDbo();
        $pbbConfig = $db->setQuery('select * from #__pbbooking_config')->loadObject();

        $dtLocalisation = array();

        //dateFormat
        $dtLocalisation['dateFormat'] = 'yy-mm-dd';

        //dayNames, dayNamesMin, dayNamesShort
        $dtLocalisation['dayNames'] = array();
        $dtLocalisation['dayNamesMin'] = array();
        $dtLocalisation['dayNamesShort'] = array();

        $bow = date_create("this sunday",new \DateTimeZone($config->get('offset')));
        for($i=0;$i<7;$i++){
            $dtLocalisation['dayNames'][] = \JHtml::_('date',$bow->format(DATE_ATOM),'l');
            $dtLocalisation['dayNamesMin'][] = \JHtml::_('date',$bow->format(DATE_ATOM),'D');
            $dtLocalisation['dayNamesShort'][] = \JHtml::_('date',$bow->format(DATE_ATOM),'D');
            $bow->modify('+1 day');
        }

        //nextText,prevText,currentText,closeText
        foreach (array('nextText','prevText','currentText','closeText') as $str) {
            $dtLocalisation[$str] = \JText::_('COM_PBBOOKING_DATEPICKER_'.strtoupper($str));
        }

        //isRTL
        $dtLocalisation['isRTL'] = ($lang->isRTL()) ? true : 0;

        //monthNames, monthNamesShort
        $dtLocalisation['monthNames'] = array();
        $dtLocalisation['monthNamesShort'] = array();
        $boy = date_create("first day of january",new \DateTimeZone($config->get('offset')));
        for ($i=0;$i<12;$i++) {
            $dtLocalisation['monthNames'][] = \JHtml::_('date',$boy->format(DATE_ATOM),'F');
            $dtLocalisation['monthNamesShort'][] = \JHtml::_('date',$boy->format(DATE_ATOM),'M');
            $boy->modify('+1 month');
        }

        /* not let's do some localisation for the dtpicker */

        $dtPickerLocalisation = array();

        //first set the strings
        foreach (array('timeOnlyTitle','timeText','hourText','minuteText','millisecText','timezoneText','currentText','closeText') as $str) {
            $dtPickerLocalisation[$str] = \JText::_('COM_PBBOOKING_TIMEPICKER_'.strtoupper($str));
        }

        //now the time format
        $dtPickerLocalisation['timeFormat'] = 'HH:mm:ss';

        //now the AM & PM settings
        $bod = date_create("now",new \DateTimeZone($config->get('offset')));
        $bod->setTime(0,0,0);
        $eod = date_create("now",new \DateTimeZone($config->get('offset')));
        $eod->setTime(23,59,59);

        $dtPickerLocalisation['amNames'] = array(\JHtml::_('date',$bod->format(DATE_ATOM),'A'),\JHtml::_('date',$bod->format(DATE_ATOM),'a'));
        $dtPickerLocalisation['pmNames'] = array(\JHtml::_('date',$bod->format(DATE_ATOM),'A'),\JHtml::_('date',$bod->format(DATE_ATOM),'a'));

        $dtPickerLocalisation["showMillisec"] = false;
        $dtPickerLocalisation["showMicrosec"] = false;
        $dtPickerLocalisation["showTimezone"] = false;

        $dtLocalisation["showMillisec"] = false;
        $dtLocalisation["showMicrosec"] = false;
        $dtLocalisation["showTimezone"] = false;


        //now the RTL
        $dtPickerLocalisation['isRTL'] = $dtLocalisation['isRTL'];

        //let's set the defaults...
        echo '<script>if (jQuery.timepicker != undefined) jQuery.timepicker.setDefaults('.json_encode($dtPickerLocalisation).');</script>';
        echo '<script>if (jQuery.datepicker != undefined) jQuery.datepicker.setDefaults('.json_encode($dtLocalisation).');</script>';

        //lets push the localisation in the doc in case we want to use it elswhere
        $doc = \JFactory::getDocument();
        $doc->addScriptDeclaration('var dtLocalisation = '.json_encode($dtLocalisation).';');
        echo '<script>var dtPickerLocalisation = '.json_encode($dtPickerLocalisation).';</script>';

        /* now let's do some full calendar localisation */

        //process some full calendar additions and push these into the dom as well
        $fullCalendarLocale = array();
        $fullCalendarLocale['weekends'] = true;

        //get the min hour max hour
        $shift_times = \Pbbooking\Pbbookinghelper::get_shift_times();
        $start_hour = 0;
        $end_hour = 0;

        $i=0;
        foreach ($shift_times as $shift_time) {
            if($i==0)
                $start_hour = $shift_time['start_time']['start_hour'];
            $end_hour = $shift_time['end_time']['end_hour'];
            $i++;
        }

        if ($end_hour < 23) $end_hour++;

        $fullCalendarLocale['minTime'] = $start_hour;
        $fullCalendarLocale['maxTime'] = $end_hour;
        $fullCalendarLocale['allDaySlot'] = false;;
        $fullCalendarLocale['firstDay'] = $pbbConfig->calendar_start_day;
        $fullCalendarLocale['dayNamesShort'] =  $dtLocalisation['dayNamesShort'];
        $fullCalendarLocale['dayNamesLong'] = $dtLocalisation['dayNames'];
        $fullCalendarLocale['monthNamesShort'] = $dtLocalisation['monthNamesShort'];
        $fullCalendarLocale['monthNames'] = $dtLocalisation['monthNames'];
        $fullCalendarLocale['slotDuration'] = '00:'.$pbbConfig->time_increment.':00';
        $fullCalendarLocale['slotEventOverlap'] = false;
        $fullCalendarLocale['header'] = array('left'=>'prev,next today','center'=>'title','right'=>'month,agendaWeek,agendaDay');
        $fullCalendarLocale['theme'] = true;
        $fullCalendarLocale['defaultView'] = 'agendaWeek';
        $fullCalendarLocale['columnFormat'] = \JText::_('COM_PBBOOKING_MANAGE_DIARIES_NEW_CAL_COLUMN_FORMAT');
        $fullCalendarLocale['editable'] = true;
        $fullCalendarLocale['buttonText'] = array('today'=>\JText::_("COM_PBBOOKING_MANAGE_DIARIES_NEW_CAL_TODAY_BUTTON"),
                                                    'month'=>\JText::_("COM_PBBOOKING_MANAGE_DIARIES_NEW_CAL_MONTH_BUTTON_"),
                                                    'week'=>\JText::_("COM_PBBOOKING_MANAGE_DIARIES_NEW_CAL_WEEK_BUTTON"),
                                                    'day'=>\JText::_("COM_PBBOOKING_MANAGE_DIARIES_NEW_CAL_DAY_BUTTON"));
        $fullCalendarLocale['axisFormat'] = \JText::_('COM_PBBOOKING_MANAGE_AXISFORMAT');
        $fullCalendarLocale['titleFormat'] = array(
                'month'=>\JText::_('COM_PBBOOKING_MANAGE_TITLE_FORMAT_MONTH'),
                'week'=>\JText::_('COM_PBBOOKING_MANAGE_TITLE_FORMAT_WEEK'),
                'day'=>\JText::_('COM_PBBOOKING_MANAGE_TITLE_FORMAT_DAY')
            );
        $fullCalendarLocale["timeFormat"] = \JText::_("COM_PBBOOKING_FULLCALENDAR_TIMEFORMAT");

        //just need to hook up the event sources...
        $cals = $db->setQuery('select * from #__pbbooking_cals')->loadObjectList();
        $fullCalendarLocale['eventSources'] = array();
        foreach ($cals as $cal) {
            $fullCalendarLocale['eventSources'][] = array(
                    'url'=> ($front) ? '?option=com_ajax&module=pbbmanage&method=getEvents&calid='.$cal->id.'&format=raw' : \JURI::root(false).'administrator/index.php?option=com_pbbooking&task=manage.get_calendar_events&calid='.$cal->id.'&format=raw',
                    'color'=> (isset($cal->color)) ? $cal->color : "#339933",
                    'textColor'=>'black'
                );
        }



        //push the fullCalendar config into the dom
        $doc->addScriptDeclaration('var fullCalendarLocalisation = '.json_encode($fullCalendarLocale).';');
    }

}
