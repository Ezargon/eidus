<?php
/*
 *
 * @Version       $Id: email.php 1929 2015-02-08 16:25:12Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.4
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-02-08 16:25:12 +0000 (Sun, 08 Feb 2015) $
 *
 */

defined('_JEXEC') or die('Restricted access');

JLoader::import('joomla.application.component.modeladmin');

/**
 * Issuetracker Model
 *
 * @package       Joomla.Components
 * @subpackage    Issuetracker
 */
class IssueTrackerModelEmail extends JModelAdmin
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
   public function getTable($type = 'Email', $prefix = 'IssueTrackerTable', $config = array())
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
      $form = $this->loadForm('com_issuetracker.email', 'email', array('control' => 'jform', 'load_data' => $loadData));
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
      $data = JFactory::getApplication()->getUserState('com_issuetracker.edit.email.data', array());

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
            $db->setQuery('SELECT MAX(ordering) FROM #__it_emails');
            $max = $db->loadResult();
            $table->ordering = $max+1;
         }

      }
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

      // $app  = JFactory::getApplication();

      // $cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

      $row = $this->getTable('email','IssueTrackerTable');

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

   /**
    * @param array $data
    * @param string $key
    * @return mixed
    */
   protected function allowEdit($data = array(), $key = 'id')
   {
      // Check specific edit permission then general edit permission.
      return parent::allowEdit($data, $key);
   }
}
