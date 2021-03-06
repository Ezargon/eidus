<?php
/*
 *
 * @Version       $Id: customfieldgroups.php 1929 2015-02-08 16:25:12Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.0
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

/**
 * Class JFormFieldColumns
 */
class JFormFieldCustomfieldgroups extends JFormField
{
   protected $type      = 'Customfieldgroups';

   /**
    * @return mixed
    */
   protected function getInput() {

      $db = JFactory::getDBO();

      // $id = JFactory::getApplication()->input->get('id');
      $groups = array();

      $groups[] = JHTML::_('select.option', 0, JText::_('COM_ISSUETRACKER_SELECT_CUSTOMGROUP'));

      //build the list of group names
      $query = "SELECT id AS value, name AS text FROM #__it_custom_field_group WHERE state = 1 ORDER BY `name`";
      $db->setQuery( $query );

      foreach( $db->loadObjectList() as $r){
         $groups[] = JHTML::_('select.option',  $r->value, $r->text );
      }

      return JHTML::_('select.genericlist',  $groups,  $this->name, 'class="inputbox"', 'value', 'text', $this->value);
   }
}