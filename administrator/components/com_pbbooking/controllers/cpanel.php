<?php

// No direct access
 
defined('_JEXEC') or die('Restricted access'); 


class PbbookingControllerCpanel extends JControllerLegacy
{
    /**
     * Method to display the view
     *
     * @access    public
     */
    function display($cachable = false, $urlparams = array())
    {

        $this->checkForErrors();

        $db = JFactory::getDbo();
        $config = $GLOBALS['com_pbbooking_data']['config'];

        $view = $this->getView('cpanel','html');

        //load some data for the view....
        $view->upcoming_events = $db->setQuery('select * from #__pbbooking_events where dtstart >= NOW() and verified = 1 and deleted = 0 order by dtstart ASC limit 10')->loadObjectList();

        $view->pending_events = $db->setQuery('select * from #__pbbooking_events where verified = 0 and deleted = 0 order by dtstart DESC limit 10')->loadObjectList();
        $view->last_syncs = $db->setQuery('select * from #__pbbooking_sync order by id DESC limit 10')->loadObjectList();
    
        //define dates for calendar util calc...
        if (date_create("now",new DateTimeZone(PBBOOKING_TIMEZONE))->format('w') == $config->calendar_start_day) {
            //we're at start of week....
            $view->dtstart = date_create("now",new DateTimeZone(PBBOOKING_TIMEZONE));
            $view->dtend = date_create("next week", new DateTimeZone(PBBOOKING_TIMEZONE));
        } else {
            //we're not at start of week..... let's go back to start of week....
            $view->dtstart = date_create("now",new DateTimeZone(PBBOOKING_TIMEZONE));
            if ($view->dtstart->format('w') > $config->calendar_start_day) {
                $view->dtstart->modify('- '.($view->dtstart->format('w') - $config->calendar_start_day).' days');
                $view->dtend = date_create($view->dtstart->format(DATE_ATOM),new DateTimeZone(PBBOOKING_TIMEZONE));
                $view->dtend->modify('+7 days');
            } else {
                $view->dtend = date_create($view->dtstart->format(DATE_ATOM),new DateTimeZone(PBBOOKING_TIMEZONE));
                $view->dtend->modify('+ '.($config->calendar_start_day - $view->dtstart->format('w')).' days');
                $view->dtstart = date_create($view->dtend->format(DATE_ATOM),new DateTimeZone(PBBOOKING_TIMEZONE));
                $view->dtstart->modify('-7 days');
            }
        }

      

        //calc cal utilizations
        $view->cals = array();
        $cals = $db->setQuery('select * from #__pbbooking_cals')->loadObjectList();
        foreach ($cals as $i=>$cal) {
            $view->cals[$i] = new Pbbooking\Model\Calendar($config);
            $view->cals[$i]->loadCalendarFromDbase(array($cal->id),$view->dtstart,$view->dtend); 
            $view->cals[$i]->name = $cal->name;
        }

        $view->announcements = ($config->disable_announcements == 0) ? $this->loadAccouncements() : false;


        $view->config = $config;
        
        $view->display();
    }


    private function loadAccouncements(){

        $reader = new JFeedFactory();

        try{
            $feed = $reader->getFeed("https://hotchillisoftware.com/support/support?view=announce&feed=rss");
            return $feed;
        } catch(Exception $e){
            return false;
        }


    }

    private function checkForErrors(){
        // If is admin we'll check for system emails and redirect
        $db = \JFactory::getDbo();
        $tables = $db->getTableList();
        $table_exist = false;
        $error = false;
        foreach ($tables as $table) {

            if (stripos($table,"pbbooking_emails") !== false) {
                $table_exist = true;
            }
        }

        if ($table_exist) {
            $system_emails = $db->setQuery("select * from #__pbbooking_emails")->loadObjectList();
            if (!$system_emails || count($system_emails) < 12) {
                $error = true;
            }
        } else {
            $error = true;
        }

        if ($error) {
            $this->setRedirect("index.php?option=com_pbbooking&task=tools.restoreemail");
            return;
        }
    }

}

