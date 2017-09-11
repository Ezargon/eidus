<?php
/*
 *
 * @Version       $Id: customfields.php 1929 2015-02-08 16:25:12Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.4
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-02-08 16:25:12 +0000 (Sun, 08 Feb 2015) $
 *
 */

// No direct access.
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.controlleradmin');

/**
 * Customfields list controller class.
 */
class IssuetrackerControllerCustomfields extends JControllerAdmin
{
   /**
    * Proxy for getModel.
    * @since   1.6
    * @param string $name
    * @param string $prefix
    * @return object
    */
   public function &getModel($name = 'customfield', $prefix = 'IssuetrackerModel')
   {
      $model = parent::getModel($name, $prefix, array('ignore_request' => true));
      return $model;
   }

   /**
    * Save the manual order inputs from the customfields list page.
    *
    * @return  void
    * @since   1.6
    */
   public function saveorder()
   {
      JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

      // Get the arrays from the Request
      $order     = JFactory::getApplication()->input->get('order', null, 'post', 'array');
      $originalOrder = explode(',', JFactory::getApplication()->input->getString('original_order_values'));

      // Make sure something has changed
      if (!($order === $originalOrder)) {
         parent::saveorder();
      } else {
         // Nothing to reorder
         $this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
         return;
      }
   }
}