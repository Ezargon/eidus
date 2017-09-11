<?php
/*
 *
 * @Version       $Id: selectissuealias.php 1929 2015-02-08 16:25:12Z geoffc $
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
 * Class JFormField
 */
class JFormFieldSelectissuealias extends JFormField
{
   protected $type      = 'Selectissuealias';

   /**
    * @return mixed
    */
   protected function getInput() {

      $db = JFactory::getDBO();

      $groups = array();

      $groups[] = JHTML::_('select.option', 0, JText::_('COM_ISSUETRACKER_SELECT'));

      // Build the list of issue aliases
      $query = "SELECT alias AS value, CONCAT_WS(' - ', alias, issue_summary) AS text FROM `#__it_issues` ";
      $query .= " ORDER BY `alias`";
      $db->setQuery( $query );

      foreach( $db->loadObjectList() as $r){
         $groups[] = JHTML::_('select.option',  $r->value, $r->text );
      }

      return JHTML::_('select.genericlist',  $groups,  $this->name, 'class="inputbox"', 'value', 'text', $this->value);
   }
}