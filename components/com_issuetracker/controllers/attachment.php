<?php
/*
 *
 * @Version       $Id: attachment.php 1929 2015-02-08 16:25:12Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.5
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-02-08 16:25:12 +0000 (Sun, 08 Feb 2015) $
 *
 */

// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JLoader::import('joomla.application.component.controllerform');

/**
 * Attachment controller class.
 */
class IssueTrackerControllerAttachment extends JControllerForm
{

   /**
    * @return bool
    */
   public function read() {
      // Load the BE model
      require_once JPATH_ADMINISTRATOR.'/components/com_issuetracker/models/attachment.php';

      $model = $this->getModel('attachment');
      $id = $model->getId();

      // Does the attachment exist?
      if ( $id != 0 ) {
         $model->DownloadFile($id);
      } else {
         // Should not get here.
         JError::raiseError(404, JText::_('COM_ISSUETRACKER_NO_ATTACHMENT_EXISTS'));
         return false;
      }
      return true;
   }
}
