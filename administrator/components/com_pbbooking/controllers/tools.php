<?php
/**
 * @package    Hot Chilli Software PBBooking
 * @link http://www.hotchillisoftware.com
 * @license    GNU/GPL
 */
 
// No direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport('joomla.application.component.controller');
 

class PbbookingControllerTools extends JControllerLegacy
{
	
	/**
	* migrates a database to the latest schema.  Useful for database problems that occur when migrating from the free version.
	*/

	function migrate()
	{

		$db = JFactory::getDbo();

		/* load the existing data into some arrays....*/
		$block_days = $db->setQuery('select * from #__pbbooking_block_days order by id ASC')->loadObjectList();
		$cals = $db->setQuery('select * from #__pbbooking_cals order by id ASC')->loadObjectList();
		$config = $db->setQuery('select * from #__pbbooking_config')->loadObject();
		$customfields = $db->setQuery('select * from #__pbbooking_customfields order by id ASC')->loadObjectList();
		$customfields_data =  $db->setQuery('select * from #__pbbooking_customfields_data order by id ASC')->loadObjectList();
		$events = $db->setQuery('select * from #__pbbooking_events order by id ASC')->loadObjectList();
		$logs = $db->setQuery('select * from #__pbbooking_logs order by id ASC')->loadObjectList();
		//$surveys = $db->setQuery('select * from #__pbbooking_surveys order by id ASC')->loadObjectList();
		$treatments = $db->setQuery('select * from #__pbbooking_treatments order by id ASC')->loadObjectList();

		$cal_ids = array();

		/*load the sql file and change into statemetns*/
		$query_string = JFile::read(JPATH_ADMINISTRATOR.'/components/com_pbbooking/sql/install.sql');
		$queries =array();
		if (JOOMLA_VERSION != '2.5')
			$queries = JDatabaseDriver::splitSql($query_string);
		else
			$queries = JDatabase::splitSql($query_string);


		//remove the inserts.... don't want them we're loading back the data from the previous version
		foreach ($queries as $i=>$query) {
			if (preg_match('/(INSERT).*/',$query)) {
				//do nothing ....
			} else {
				//run the query.
				$db->setQuery($query)->query();
			}
		}

		//make any changes that might need to be made.
		if (!isset($config->booking_details_template) || $config->booking->details_template == '')
			$config->booking_details_template = "<p><table><tr><th>{{COM_PBBOOKING_SUCCESS_DATE}}</th><td>{{dstart}}</td></tr><tr><th>{{COM_PBBOOKING_SUCCESS_TIME}}</th><td>{{dtstart}}</td></tr><tr><th>{{COM_PBBOOKING_BOOKINGTYPE}}</th><td>{{service.name}}</td></tr></table></p>";

		//now create the data.
		foreach ($block_days as $block_day) $db->insertObject('#__pbbooking_block_days',$block_day);
		foreach ($cals as $cal) {
			$db->insertObject('#__pbbooking_cals',$cal);
			$cal_ids[] = $cal->id;	
		} 
		$db->insertObject('#__pbbooking_config',$config);
		foreach ($customfields as $customfield) $db->insertObject('#__pbbooking_customfields',$customfield);
		foreach ($customfields_data as $cfd) $db->insertObject('#__pbbooking_customfields_data',$cfd);
		foreach ($events as $event) {
			if (!isset($event->verified))
				$event->verified == 1;				//sets all old events to verified status.
			$db->insertObject('#__pbbooking_events',$event);
		}
		foreach ($logs as $log) $db->insertObject('#__pbbooking_logs',$log);
		//foreach ($surveys as $survey) $db->insertObject('#__pbbooking_surveys',$survey);
		foreach ($treatments as $treatment) {
			$treatment->calendar = implode(',',$cal_ids);
			$db->insertObject('#__pbbooking_treatments',$treatment);	
		}

		$this->setRedirect('index.php?option=com_pbbooking',JText::_('COM_PBBOOKING_DATABASE_MIGRATION_DONE'));

	}




	/**
	 * A untility function to unlink the PBBooking installation from a google account.  This might be needed due to
	 * expired refresh token etc.
	 */

	public function unlink()
	{
		$db = JFactory::getDbo();
		$app = JFactory::getApplication();

		$db->setQuery('update #__pbbooking_config set `authcode` = "", `token` = "" where id = 1')->query();

		$this->setRedirect('index.php?option=com_pbbooking',JText::_('COM_PBBOOKING_GOOGLE_CAL_UNLINKED'));
		
		return;
	}

	/**
	 * A utility function to purge the sync queue
	 */
	public function purgesync()
	{
		$db = JFactory::getDbo();

		$db->setQuery('delete from #__pbbooking_sync')->execute();

		$this->setRedirect('index.php?option=com_pbbooking',JText::_('COM_PBBOOKING_DASHBOARD_PURGE_SYNC_PURGED'));

	}


	public function restoreemail(){
		$sql  = file_get_contents(JPATH_ADMINISTRATOR."/components/com_pbbooking/assets/system-emails.sql");

		$db = JFactory::getDbo();
		$lines = $db->splitSql($sql);
		foreach ($lines as $line) {
			$db->setQuery($line)->execute();
		}

		// Don't need to worry about error JDatabase error handler will catch failed.
		$this->setRedirect("index.php?option=com_pbbooking", JText::_("COM_PBBOOKING_DASHBOARD_RESTORE_EMAILS_SUCCESS"),"success");
	}



}