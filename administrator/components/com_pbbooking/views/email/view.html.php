<?php
/**
 * Created by     Eric Fernance
 * User:          Eric Fernance
 * Website:       http://hotchillisoftware.com
 * License:       GPL v2
 */


if (!defined('JPATH_BASE')) die();


class PbbookingViewEmail extends JViewLegacy {

  function display($tpl = null)
  {


    $input = JFactory::getApplication()->input;


    //load the items we need
    JToolBarHelper::title( JText::_( 'COM_PBBOOKING_EMAIL_TITLE' ), 'generic.png' );
    JToolbarHelper::custom('cpanel.display','dashboard','','COM_PBBOOKING_DASHBOARD',false);
    JToolbarHelper::save('email.save');
    JToolbarHelper::cancel('email.cancel', 'JTOOLBAR_CLOSE');

    $this->form = $this->get('Form');
    $this->item = $this->get('Item');
    $this->state = $this->get('State');
    $this->pagination    = $this->get('Pagination');


    // Display the template
    parent::display($tpl);
  }

}
