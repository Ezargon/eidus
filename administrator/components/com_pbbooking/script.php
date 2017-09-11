<?php
/**
* @package		Hot Chilli Software PBBooking
* @license		GNU General Public License version 2 or later; see LICENSE.txt
* @link		http://www.hotchillisoftware.com
*/
 
// No direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );


class com_pbbookingInstallerScript
{
    var $tables;
    var $data;
    var $displayTemplateWarning;
    var $pbbConfig;
    var $multilangs;


    public function __construct()
    {
        $this->tables = array('block_days','cals','config','customfields','customfields_data','events','lang_override',
                            'logs','surveys','sync','treatments');
        $this->data = array();

    }

	function preflight($type,$parent) {
		$jversion = new JVersion();

		if (version_compare($jversion->getShortVersion(),'3.1.5') == -1) {
			Jerror::raiseWarning(null,'This version of PBBooking is not compatible with the version of Joomla you are using.');
			return false;
		}

        if (PHP_VERSION_ID < 50207) {
            JError::raiseWarning(null,'This version of PBBooking is not compatible with the version of PHP you are using.  PHP >= 5.3 is required.');
	       return false;
        }

        //do a clean migration to 3.0.0
        $db = JFactory::getDbo();
        $config = JFactory::getConfig();

        $current = $db->setQuery('select * from #__extensions where element = "com_pbbooking" and type="component"')->loadObject();

        if ($current) {
            $curmanifest = json_decode($current->manifest_cache,true);
            $needsMigration = version_compare('3.0.0',$curmanifest['version']);
            $this->pbbConfig = $db->setQuery("select * from #__pbbooking_config")->loadObject();

            // Need to check to ensure this table exists.
            $tables = $db->getTableList();
            foreach ($tables as $table) {

                if (stripos($table,"pbbooking_lang_override") !== false) {
                    $this->multilangs = $db->setQuery("select * from #__pbbooking_lang_override")->loadObjectList();
                }
            }


        } else
            $needsMigration = 0;
        
        if ($current && $needsMigration == 1) 
        {
            $this->displayTemplateWarning = true;
            //do the existing tables need to be dropped?
            
            
            //back up the user data
            $tablelist = $db->getTableList();
            $tableprefix = str_ireplace('#__', $config->get('dbprefix'), '#__pbbooking_');

            foreach ($this->tables as $table)
            {   
                if (in_array($tableprefix.$table,$tablelist))
                {
                    $this->data[$table] = $db->setQuery('select * from #__pbbooking_'.$table)->loadObjectList();
                    $db->dropTable($tableprefix.$table);
                }
            }
        }


    }

	function install($parent) {

	}


	function update($parent)
	{
        $db = JFactory::getDbo();
        //restore the data
        if (isset($this->data['config']) && count($this->data['config'])>0)
        {
            foreach ($this->data as $table=>$data)
            {
                foreach ($data as $row)
                {
                    //only want to restore the columns that are in the new data structure
                    $columns = $db->getTableColumns('#__pbbooking_'.$table);

                    $newrow = array();
                    foreach ($columns as $column=>$value)
                        $newrow[$column] = (isset($row->$column)) ? $row->$column : null;

                    //**** CODE FOR EDGE CASES ****/

                    //edge case if (table is events and the verified is NOT set then we're comign from the free version)
                    //and need to set the event verified to 1.
                    if ($table == 'events' && !isset($newrow['verified']))
                        $newrow['verified'] =1;

                    if ($table == 'config')
                    {
                        $newrow['time_groupings'] = '{"morning":{"shift_start":"1000","shift_end":"1200","display_label":"morning"},"afternoon":{"shift_start":"1330","shift_end":"1700","display_label":"afternoon"},"evening":{"shift_start":"1700","shift_end":"1930","display_label":"evening"}}';
                        $newrow['booking_details_template'] = '<p><table><tr><th>{{COM_PBBOOKING_SUCCESS_DATE}}</th><td>{{dstart}}</td></tr><tr><th>{{COM_PBBOOKING_SUCCESS_TIME}}</th><td>{{dtstart}}</td></tr><tr><th>{{COM_PBBOOKING_BOOKINGTYPE}}</th><td>{{service.name}}</td></tr></table></p>';
                    }

                    //****** END CODE FOR EDGE CASES *******/

                    $db->insertObject('#__pbbooking_'.$table,new JObject($newrow),(isset($row->id)) ? 'id' : null);
                }
            }
        }
	}

	function postflight($type,$parent)
	{
		$db = JFactory::getDbo();



		//update the standard ACL rules....
		$db->setQuery('update #__assets set rules = "{\"pbbooking.create\":{\"1\":1},\"pbbooking.deleteown\":{\"1\":1},\"pbbooking.browse\":{\"1\":1}}" where name="com_pbbooking"')->query();

        if (isset($this->displayTemplateWarning) && $this->displayTemplateWarning == true) {
            echo '<div class="alert alert-info">';
            echo JText::_('COM_PBBOOKING_TEMPLATE_COMPAT_WARNING');
            echo '</div>';
        }

        $config = $db->setQuery('select * from #__pbbooking_config')->loadObject();
        if ($config && !isset($config->google_cal_sync_secret))
        {
            $config->google_cal_sync_secret = substr(md5(rand()), 0, 12);
            $db->updateObject('#__pbbooking_config', $config, 'id');
        }

        // Migrate the existing messages if they exist
        if (isset($this->pbbConfig)){
            error_log("going to try and migrate messages....");
            if (isset($this->pbbConfig->email_body) || isset($this->pbbConfig->email_subject) !== "") {
                $db->updateObject("#__pbbooking_emails",new JObject(array(
                    "id"=>1,"emailbody"=>$this->pbbConfig->email_body, "emailsubject"=>$this->pbbConfig->email_subject
                )),"id");
            }
            if (isset($this->pbbConfig->auto_validated_appt_body) || isset($this->pbbConfig->auto_validated_appt_email_subject) !== "") {
                $db->updateObject("#__pbbooking_emails",new JObject(array(
                    "id"=>2,"emailbody"=>$this->pbbConfig->auto_validated_appt_body, "emailsubject"=>$this->pbbConfig->auto_validated_appt_email_subject
                )),"id");
            }

            if (isset($this->pbbConfig->admin_validation_pending_email_subject) || isset($this->pbbConfig->admin_validation_pending_email_body) !== "") {
                $db->updateObject("#__pbbooking_emails",new JObject(array(
                   "id"=>3,"emailbody"=>$this->pbbConfig->admin_validation_pending_email_body, "emailsubject"=>$this->pbbConfig->admin_validation_pending_email_subject
                )),"id");
            }

            if (isset($this->pbbConfig->admin_validation_confirmed_email_subject) || isset($this->pbbConfig->admin_validation_confirmed_email_body) !== "") {
                $db->updateObject("#__pbbooking_emails",new JObject(array(
                    "id"=>4,"emailbody"=>$this->pbbConfig->admin_validation_confirmed_email_body, "emailsubject"=>$this->pbbConfig->admin_validation_confirmed_email_subject
                )),"id");
            }
            if (isset($this->pbbConfig->admin_paypal_confirm) || isset($this->pbbConfig->admin_paypal_confirm_subject) !== "") {
                $db->updateObject("#__pbbooking_emails",new JObject(array(
                    "id"=>5,"emailbody"=>$this->pbbConfig->admin_paypal_confirm, "emailsubject"=>$this->pbbConfig->admin_paypal_confirm_subject
                )),"id");
            }

            if (isset($this->pbbConfig->client_paypal_confirm_subject) || isset($this->pbbConfig->client_paypal_confirm) !== "") {
                $db->updateObject("#__pbbooking_emails",new JObject(array(
                    "id"=>6,"emailbody"=>$this->pbbConfig->client_paypal_confirm, "emailsubject"=>$this->pbbConfig->client_paypal_confirm_subject
                )),"id");
            }
            if (isset($this->pbbConfig->reminder_email_body) || isset($this->pbbConfig->reminder_email_subject) !== "") {
                $db->updateObject("#__pbbooking_emails",new JObject(array(
                    "id"=>7,"emailbody"=>$this->pbbConfig->reminder_email_body, "emailsubject"=>$this->pbbConfig->reminder_email_subject
                )),"id");
            }
            if (isset($this->pbbConfig->testimonial_email_body) || isset($this->pbbConfig->testimonial_email_subject) !== "") {
                $db->updateObject("#__pbbooking_emails",new JObject(array(
                    "id"=>8,"emailbody"=>$this->pbbConfig->testimonial_email_body, "emailsubject"=>$this->pbbConfig->testimonial_email_subject
                )),"id");
            }

            // Need to load 9 admin notification from the site language file.
            JFactory::getLanguage()->load("com_pbbooking",JPATH_SITE, JFactory::getLanguage()->getTag(),true);
            $db->updateObject("#__pbbooking_emails",new JObject(array(
                "id"=>9,"emailbody"=>JText::_("COM_PBBOOKING_ADMIN_EMAIL_BODY"), "emailsubject"=>JText::_("COM_PBBOOKING_ADMIN_EMAIL_SUBJECT")
            )),"id");


            if (isset($this->multilangs) && count($this->multilangs) > 0) {
                // migrate the multi langs.
                $db->setQuery("update #__pbbooking_lang_override set messagename = 'client_validation_email' where original_id = 1 and type='message'")->execute();
                $db->setQuery("update #__pbbooking_lang_override set messagename = 'client_validation_email' where original_id = 1 and type='subject'")->execute();
                $db->setQuery("update #__pbbooking_lang_override set messagename = 'auto_validated_appt_email' where original_id = 2 and type='message'")->execute();
                $db->setQuery("update #__pbbooking_lang_override set messagename = 'auto_validated_appt_email' where original_id = 2 and type='subject'")->execute();
                $db->setQuery("update #__pbbooking_lang_override set messagename = 'admin_validation_pending_email' where original_id = 3 and type='message'")->execute();
                $db->setQuery("update #__pbbooking_lang_override set messagename = 'admin_validation_pending_email' where original_id = 3 and type='subject'")->execute();
                $db->setQuery("update #__pbbooking_lang_override set messagename = 'admin_validation_confirmed' where original_id = 4 and type='message'")->execute();
                $db->setQuery("update #__pbbooking_lang_override set messagename = 'admin_validation_confirmed' where original_id = 4 and type='subject'")->execute();
                $db->setQuery("update #__pbbooking_lang_override set messagename = 'admin_payment_confirmed_email' where original_id = 5 and type='message'")->execute();
                $db->setQuery("update #__pbbooking_lang_override set messagename = 'admin_payment_confirmed_email' where original_id = 5 and type='subject'")->execute();
                $db->setQuery("update #__pbbooking_lang_override set messagename = 'client_payment_confirmed_email' where original_id = 6 and type='message'")->execute();
                $db->setQuery("update #__pbbooking_lang_override set messagename = 'client_payment_confirmed_email' where original_id = 6 and type='subject'")->execute();

            }



        }

	}
}


?>