<?php
/*
 *
 * @Version       $Id: view.html.php 1929 2015-02-08 16:25:12Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.0
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-02-08 16:25:12 +0000 (Sun, 08 Feb 2015) $
 *
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Load framework base classes
JLoader::import('joomla.application.component.view');

/**
 * Class IssueTrackerViewDbtasks
 */
class IssueTrackerViewDbtasks extends JViewLegacy
{
   /**
    * @param null $tpl
    * @return mixed|void
    */
   function  display($tpl = null)
   {
      // Get the task set in the model
      $model = $this->getModel();
      $task = $model->getState('task','browse');
      $msg = "";

      switch ($task) {
         case 'addsampledata':
            $model->addsampledata();
            $msg = JText::_( 'COM_ISSUETRACKER_SDATA_ADDED' );
            break;
         case 'remsampledata':
            $model->remsampledata();
            $msg = JText::_( 'COM_ISSUETRACKER_SDATA_REMOVED' );
            break;
         case 'syncusers':
            $model->syncusers();;
            $msg = JText::_( 'COM_ISSUETRACKER_USERS_SYNCHRONISED' );
            break;
      }

      // Shouldn't really do this here, but for the moment it will suffice.
      $app = JFactory::getApplication();
      $app->enqueueMessage($msg);
      $app->redirect('index.php?option=com_issuetracker');
      return;
   }
}