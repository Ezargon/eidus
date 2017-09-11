<?php
/*
 *
 * @Version       $Id: controller.php 1929 2015-02-08 16:25:12Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.4
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-02-08 16:25:12 +0000 (Sun, 08 Feb 2015) $
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JLoader::import('joomla.application.component.controller');

/**
 * Issue Tracker Model
 *
 * @package       Joomla.Components
 * @subpackage    Issue Tracker
 */

class IssueTrackerController extends JControllerLegacy
{
   /**
    * Method to display the view
    *
    * @access  public
    * @param bool $cachable
    * @param bool $urlparams
    * @return $this|\JControllerLegacy
    */
   public function display ( $cachable=false, $urlparams=false)
   {
      require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/issuetracker.php';
      //make sure mootools is loaded
      JHTML::_('behavior.framework');

      // Load the submenu.
      IssueTrackerHelper::addSubmenu(JFactory::getApplication()->input->get('view', 'cpanel'));

      $view    = JFactory::getApplication()->input->get('view', 'cpanel');
      JFactory::getApplication()->input->set('view', $view);

      parent::display();
      return $this;
   }
}