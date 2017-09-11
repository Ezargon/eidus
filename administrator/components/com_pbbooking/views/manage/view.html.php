<?php

/**
* @package		Hot Chilli Software PBBooking
* @license		GNU General Public License version 2 or later; see LICENSE.txt
* @link		http://www.hotchillisoftware.com
*/
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
 
jimport( 'joomla.application.component.view' );
 

class PbbookingViewmanage extends JViewLegacy
{

    function display($tpl = null)
    {
        $input = JFactory::getApplication()->input;
		$task = $input->get('task');
		$controller = $input->get('controller');
       
		//setup the title
		JToolbarHelper::title(JText::_('COM_PBBOOKING_SUB_MENU_MANAGE_DIARIES'));
        JToolbarHelper::custom('cpanel.display','dashboard','','COM_PBBOOKING_DASHBOARD',false);

        $this->cals = $GLOBALS['com_pbbooking_data']['calendars'];
        $this->dateparam = $input->get('dateparam',null,'string');

        $this->setLayout('newmanage');
	 	parent::display($tpl);

    }
}