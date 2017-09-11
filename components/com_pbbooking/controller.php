<?php
/**
 * @package    Hot Chilli Software PBBooking
 * @link http://www.hotchillisoftware.com
 * @license    GNU/GPL
 */
 
// No direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport('joomla.application.component.controller');
jimport('joomla.mail.helper');

 
class PbbookingController extends JControllerLegacy
{
	
	
    /**
     * Method to display the view
     *
     * @access    public
     */
    function display($cachable = false, $urlparams = array())
    {	
    	parent::display($cachable,$urlparams);
    }

	

}