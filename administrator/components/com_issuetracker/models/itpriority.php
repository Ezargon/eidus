<?php
/*
 *
 * @Version       $Id: itpriority.php 1929 2015-02-08 16:25:12Z geoffc $
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

JLoader::import('joomla.application.component.modeladmin');

/**
 * Issuetracker Model
 *
 * @package       Joomla.Components
 * @subpackage    Issuetracker
 */
class IssuetrackerModelItpriority extends JModelAdmin
{
   /**
    * @var     string   The prefix to use with controller messages.
    * @since   1.6
    */
   protected $text_prefix = 'COM_ISSUETRACKER';

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param string $type The type
     * @param string $prefix A prefix for the table class name. Optional.
     * @param array $config Configuration array for model. Optional.
     * @internal param \The $type table type to instantiate
     * @return  JTable   A database object
     * @since   1.6
     */
   public function getTable($type = 'Itpriority', $prefix = 'IssueTrackerTable', $config = array())
   {
      return JTable::getInstance($type, $prefix, $config);
   }

   /**
    * Method to get the record form.
    *
    * @param   array $data    An optional array of data for the form to interogate.
    * @param   boolean  $loadData   True if the form is to load its own data (default case), false if not.
    * @return  JForm A JForm object on success, false on failure
    * @since   1.6
    */
   public function getForm($data = array(), $loadData = true)
   {
      // Initialise variables.
      // $app  = JFactory::getApplication();

      // Get the form.
      $form = $this->loadForm('com_issuetracker.itpriority', 'itpriority', array('control' => 'jform', 'load_data' => $loadData));
      if (empty($form)) {
         return false;
      }

      return $form;
   }

   /**
    * Method to get the data that should be injected in the form.
    *
    * @return  mixed The data for the form.
    * @since   1.6
    */
   protected function loadFormData()
   {
      // Check the session for previously entered form data.
      $data = JFactory::getApplication()->getUserState('com_issuetracker.edit.itpriority.data', array());

      if (empty($data)) {
         $data = $this->getItem();
      }

      return $data;
   }

   /**
    * Method to get a single record.
    *
    * @param   integer  $pk The id of the primary key.
    *
    * @return  mixed Object on success, false on failure.
    * @since   1.6
    */
   public function getItem($pk = null)
   {
      if ($item = parent::getItem($pk)) {

         //Do any procesing on fields here if needed

      }

      return $item;
   }

   /**
    * Prepare and sanitise the table prior to saving.
    *
    * @since   1.6
    * @param JTable $table
    */
   protected function prepareTable($table)
   {
      JLoader::import('joomla.filter.output');

      if (empty($table->id)) {

         // Set ordering to the last item if not set
         if (@$table->ordering === '') {
            $db = JFactory::getDbo();
            $db->setQuery('SELECT MAX(ordering) FROM #__it_priority');
            $max = $db->loadResult();
            $table->ordering = $max+1;
         }

      }
   }

   /**
    * Method to test whether a record can be deleted.
    *
    * @param   object   $record  A record object.
    *
    * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
    * @since   1.6
    */
   protected function canDelete($record)
   {
      if (!empty($record->id)) {
         if ($record->state != -2) {
            return true;
         }
         $user = JFactory::getUser();
         return $user->authorise('core.delete', 'com_issuetracker.priority.'.(int) $record->id);
      }
      return false;
   }
   /**
    * Method to delete one or more records.
    *
    * @param   array  &$pks  An array of record primary keys.
    *
    * @return  boolean  True if successful, false if an error occurs.
    *
    * @since   11.1
    */
   public function delete(&$pks)
   {
      $pks = (array) $pks;
      $app  = JFactory::getApplication();

      // Check if there are any of the items to be deleted are in use.
      $query = 'SELECT COUNT( i.id ) AS numcat';
      $query .= ' FROM #__it_issues AS i' ;
      $query .= ' WHERE i.priority IN ( '.implode(',',$pks).' )' ;

      $this->_db->setQuery( $query );
      $cnt = $this->_db->loadResult();
      if ($cnt > 0) {
         $app->enqueueMessage(JText::plural('COM_ISSUETRACKER_N_DELETE_ITEMS_IN_USE_MSG', $cnt), 'error');
         return false;
      }

      $row = $this->getTable();

      // Iterate the items to delete each one.
      foreach ($pks as $pk)
      {
        if (!$row->delete( $pk )) {
           $this->setError( $row->getErrorMsg() );
           return false;
        }
      }
      return true;
   }

}