<?php
/**
 * @package    Hot Chilli Software PBBooking
 * @link http://www.hotchillisoftware.com
 * @license    GNU/GPL
 */
 
// No direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport('joomla.application.component.controller');



class PbbookingControllerService extends JControllerForm
{
	
    function display($cachable = false, $urlparams = false) 
    {
        
        $input = JFactory::getApplication()->input;
        $input->set('view', $input->getCmd('view', 'Service'));

        
        parent::display($cachable);
    }


}