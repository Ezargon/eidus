<?php
/**
 * Created by     Eric Fernance
 * User:          Eric Fernance
 * Website:       http://hotchillisoftware.com
 * License:       GPL v2
 */


if (!defined('JPATH_BASE')) die();


class PbbookingViewSmss extends JViewLegacy {

  function display($tpl = null)
  {


    $input = JFactory::getApplication()->input;


    //load the items we need
    JToolBarHelper::title( JText::_( 'COM_PBBOOKING_SMSS' ), 'generic.png' );
    JToolbarHelper::custom('cpanel.display','dashboard','','COM_PBBOOKING_DASHBOARD',false);
    JToolbarHelper::editList("sms.edit");


    $this->items = $this->get('Items');
    $this->state = $this->get('State');
    $this->pagination    = $this->get('Pagination');

    $this->checkPluginPublished();


    // Display the template
    parent::display($tpl);
  }


  private function checkPluginPublished(){
    $db = JFactory::getDbo();

    $plugin = $db->setQuery("select * from #__extensions where type='plugin' and element='smsevents' and folder='pbbooking'")->loadObject();

    if (!$plugin) {
      // error message about pro only feature
      JFactory::getApplication()->enqueueMessage(JText::_("COM_PBBOOKING_GENERAL_REQUIRES_SUBSCRIBER"),"warning");
      return;
    }
    if (!$plugin->enabled) {
      // error message about needs publishing.
      JFactory::getApplication()->enqueueMessage(sprintf(JText::_("COM_PBBOOKING_FEATURE_NEEDS_PLUGIN_PUBLISHED"), "Hot Chilli Software: SMS Events Plugin"),"warning");
      return;
    }
  }

}
