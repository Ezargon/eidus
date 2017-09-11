<?php
/*
 *
 * @Version       $Id: view.html.php 1929 2015-02-08 16:25:12Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.1
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-02-08 16:25:12 +0000 (Sun, 08 Feb 2015) $
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

JLoader::import( 'joomla.application.component.view');

/**
 * Class IssueTrackerViewDocumentation
 */
class IssueTrackerViewDocumentation extends JViewLegacy
{
   /**
    * @param null $tpl
    * @return mixed|void
    */
   function display($tpl = null)
   {
      $user  = JFactory::getUser();

      JHtml::stylesheet('com_issuetracker/administrator.css', array(), true, false, false);
      JToolBarHelper::title("Issue Tracker - " . JText::_('COM_ISSUETRACKER_TITLE_DOCUMENTATION'), 'documentation');

      if($user->authorise('core.admin', 'com_issuetracker')){
         JToolBarHelper::preferences('com_issuetracker', '600', '800');
      }

      JToolBarHelper::divider();
      JToolBarHelper::help( 'screen.issuetracker', true );

      $jversion = new JVersion();
      if( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         $this->sidebar = JHtmlSidebar::render();
//         JHtmlSidebar::setAction('index.php?option=com_issuetracker&view=attachments');
      } else {
         $this->setLayout("default25");
      }

      return parent::display($tpl);
   }
}
