<?php
/*
 *
 * @Version       $Id: dbtables.php 1929 2015-02-08 16:25:12Z geoffc $
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
 * Class JFormFieldDbtables
 */
class JFormFieldDbtables extends JFormField
{
   protected $type      = 'Dbtables';

   /**
    * @return mixed
    */
   protected function getInput() {

      $db = JFactory::getDBO();

      $id = JFactory::getApplication()->input->get('id');
      $options = array();
      if ( $id != 0 ) {
         // Temporary cludge until we set up view correctly!
         // print ("$this->name  $this->value;<p>");
         return $this->value;
      } else  {
         $prefix = $db->getPrefix();
         $query = 'SELECT table_name AS value, table_name as text FROM INFORMATION_SCHEMA.TABLES '
         . " WHERE table_name like '".$prefix."it_%'";
         $query .= " AND table_name NOT like '%view%' ";
         $query .= " AND table_name NOT LIKE '%chistory%' ";
         $query .= " AND table_name NOT LIKE '%trigger%' ";
         $db->setQuery( $query );

         foreach( $db->loadObjectList() as $r){
            $options[] = JHTML::_('select.option',  $r->value, $r->text );
         }
      }
//      array_unshift($options, JHTML::_('select.option', 'All', JText::_('JALL'), 'value', 'text'));
      array_unshift($options, JHTML::_('select.option', '', '- '.JText::_('COM_ISSUETRACKER_SELECT_TABLE').' -', 'value', 'text'));

      return JHTML::_('select.genericlist',  $options,  $this->name, 'class="inputbox"', 'value', 'text', $this->value);
   }
}
