<?php
/*
 *
 * @Version       $Id: itprojects.php 1929 2015-02-08 16:25:12Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.6
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-02-08 16:25:12 +0000 (Sun, 08 Feb 2015) $
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Issue Tracker Controller
 *
 * @package    Joomla.Components
 * @subpackage    com_issuetracker
 */
JLoader::import('joomla.application.component.controllerform');

/**
 * Class IssueTrackerControllerItprojects
 */
class IssueTrackerControllerItprojects extends JControllerForm
{

   /**
    *
    */
   function __construct() {
        $this->view_list = 'itprojectslist';
        parent::__construct();
    }

   /**
    * Method to save the submitted ordering values for records via AJAX.
    *
    * @return  void
    *
    * @since   3.0
    */
/*
   public function saveOrderAjax()
   {
      $pks = $this->input->post->get('cid', array(), 'array');
      $order = $this->input->post->get('order', array(), 'array');

      // Sanitize the input
      JArrayHelper::toInteger($pks);
      JArrayHelper::toInteger($order);

      // Get the model
      $model = $this->getModel();

      // Save the ordering
      $return = $model->saveorder($pks, $order);

      if ($return)
      {
         echo "1";
      }

      // Close the application
      JFactory::getApplication()->close();
   }
*/
   /**
    * Function that allows child controller access to model data
    * after the item has been deleted.
    *
    * @param   JModelLegacy  $model  The data model object.
    * @param   integer       $ids    The array of ids for items being deleted.
    *
    * @return  void
    *
    * @since   12.2
    */
   protected function postDeleteHook(JModelLegacy $model, $ids = null)
   {
   }
}