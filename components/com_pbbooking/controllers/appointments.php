<?php

/**
* @package		Hot Chilli Software PBBooking
* @license		GNU General Public License version 2 or la<ter; see LICENSE.txt
* @link		http://www.hotchillisoftware.com
*/

// No direct access
 
defined('_JEXEC') or die('Restricted access'); 



class PbbookingControllerappointments extends JControllerLegacy

{

	public function __construct()
	{
		parent::__construct();
		//
		//check to see whether self service is allowed on this installation
		$db = JFactory::getDbo();
		$config = $GLOBALS['com_pbbooking_data']['config'];

		if ($config->enable_selfservice == 0) 
			$this->setRedirect(JRoute::_('index.php?option=com_pbbooking&view=pbbooking'),JText::_('COM_PBBOOKING_SELF_SERVICE_NOT_ENABLED'));

	}

	public function display($cachable = false, $urlparams = array())
	{
		parent::display($cachable,$urlparams);
	}

	/**
	* deletes an appointment
	* @access public
	* @since 2.4.4
	*/

	public function delete_appt()
	{
		$input = JFactory::getApplication()->input;
		$db = JFactory::getDbo();
		$user = JFactory::getUser();

		//set the error flag.
		$error = false;

		//can the user actually delete their appointments?
		if (!$user->authorise('pbbooking.deleteown','com_pbbooking')) {
			$this->setRedirect(JRoute::_('index.php?option=com_pbbooking&view=appointments'),JText::_('COM_PBBOOKING_APPOINTMENT_DELETE_NOT_AUTHORISED'));
			return;
		}

		//get the appointment id from the URL
		$a_id = $input->get('id',null,'integer');
		if ($a_id) {
			$appt = new \Pbbooking\Model\Event($a_id);
			if ($appt && $appt->email == $user->email) {
				//delete the appointment
				\Pbbooking\Model\Calendar::delete_event($appt->id);
				$this->setRedirect(JRoute::_('index.php?option=com_pbbooking&view=appointments'),JText::_('COM_PBBOOKING_SELF_SERVICE_APPT_DELETED'));				
			} else 
				$this->setRedirect(JRoute::_('index.php?option=com_pbbooking&view=appointments'),JText::_('COM_PBBOOKING_SELF_SERVICE_NO_APPT'));

		} else 
			$this->setRedirect(JRoute::_('index.php?option=com_pbbooking&view=appointments'),JText::_('COM_PBBOOKING_SELF_SERVICE_NO_APPT'));


	}
}




?>