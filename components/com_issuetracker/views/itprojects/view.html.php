<?php
/*
 *
 * @Version       $Id: view.html.php 1929 2015-02-08 16:25:12Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.5
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-02-08 16:25:12 +0000 (Sun, 08 Feb 2015) $
 *
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

JLoader::import( 'joomla.application.component.view');

/**
 * HTML View class for the Issue Tracker Component
 *
 * @package    Joomla.Components
 * @subpackage Issue Tracker
 */
class IssueTrackerViewItprojects extends JViewLegacy
{
   protected $print;
   protected $params;
   protected $pageclass_sfx;
   protected $data;
   /**
    * @param null $tpl
    * @return mixed|void
    */
   function display($tpl = null)
   {
      $app = JFactory::getApplication();

      $params  = $app->getParams();
      $this->params = $params;

      //Escape strings for HTML output
      $this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

      $this->print      = JFactory::getApplication()->input->getBool('print');

      $data = $this->get('Data');
      $this->data = $data;

      if ( is_null($data) || $data->id == 0 ) {
         // No project was found.
         if ( isset($_SERVER['HTTP_REFERER']) ) {
            $previousurl = $_SERVER['HTTP_REFERER'];
         } else {
            $previousurl = JURI::base();
         }
         $msg = JText::_('COM_ISSUETRACKER_PROJECT_NOT_FOUND');
         $app->enqueueMessage($msg);
         $app->redirect($previousurl);
      }

      // Create a shortcut for $item.
      $item = $data;

      $jversion = new JVersion();
      if ( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         $item->tagLayout      = new JLayoutFile('joomla.content.tags');

         $item->tags = new JHelperTags;
         $item->tags->getItemTags('com_issuetracker.itproject' , $item->id);
      }
      parent::display($tpl);
   }
}
