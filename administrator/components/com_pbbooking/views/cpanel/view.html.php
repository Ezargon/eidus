<?php
/**
* @package		Hot Chilli Software PBBooking
* @license		GNU General Public License version 2 or later; see LICENSE.txt
* @link		http://www.hotchillisoftware.com
*/
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
 
jimport( 'joomla.application.component.view' );
 

class PbbookingViewCpanel extends JViewLegacy
{
   
    function display($tpl = null)
    {
        \JToolBarHelper::title( JText::_( 'COM_PBBOOKING' ).' '.JText::_('COM_PBBOOKING_DASHBOARD'), 'generic.png' );
 

        parent::display($tpl);
    }
}