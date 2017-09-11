<?php
/*
 *
 * @Version       $Id: columns.php 1929 2015-02-08 16:25:12Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.5.0
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-02-08 16:25:12 +0000 (Sun, 08 Feb 2015) $
 *
 */
defined('_JEXEC') or die('Restricted access');

if(!defined('DS')){
   define('DS',DIRECTORY_SEPARATOR);
}

/*
if (! class_exists('JauditHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'audit.php');
}
*/
/**
 * Class JFormFieldColumns
 */
class JFormFieldColumns extends JFormField
{
   protected $type      = 'Columns';

   /**
    * @return mixed
    */
   protected function getInput() {

      $db = JFactory::getDBO();

      $id = JFactory::getApplication()->input->get('id');
      $options = array();

      if ( $id != 0 ) {
         $db->setQuery('SELECT table_name from `#__it_triggers` WHERE id = '.$id);
         $tname = $db->loadResult();
      } else {
         $tname = NULL;
      }

      if ( !empty($tname) ) {
         //build the list of column name
         $query = "SELECT COLUMN_NAME AS value, COLUMN_NAME AS text FROM INFORMATION_SCHEMA.COLUMNS "
         . " WHERE table_name like '".$tname."'";
         $db->setQuery( $query );

         foreach( $db->loadObjectList() as $r){
            $options[] = JHTML::_('select.option',  $r->value, $r->text );
         }
      }
      array_unshift($options, JHTML::_('select.option', 'All', JText::_('JALL'), 'value', 'text'));
//      array_unshift($options, JHTML::_('select.option', '', '- '.JText::_('COM_ISSUETRACKER_SELECT_COLUMNS').' -', 'value', 'text'));

      return JHTML::_('select.genericlist',  $options,  $this->name, 'class="inputbox" multiple="multiple"', 'value', 'text', $this->value);
   }
}