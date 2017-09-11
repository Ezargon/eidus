<?php
/*
 *
 * @Version       $Id: issuetrackertypes.php 1929 2015-02-08 16:25:12Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.5
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

if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}

/**
 * Class JFormFieldIssueTrackerTypes
 */
class JFormFieldIssueTrackerTypes extends JFormField
{
   protected $type      = 'IssueTrackerTypes';

   /**
    * @return mixed
    */
   protected function getInput()
   {
      $tree = array();
      $tree = IssueTrackerHelper::getTypes(1);

      array_unshift($tree, JHTML::_('select.option', '0', JText::_('JALL'), 'value', 'text'));
//      array_unshift($tree, JHTML::_('select.option', '', '- '.JText::_('COM_ISSUETRACKER_SELECT_TYPE').' -', 'value', 'text'));
      return JHTML::_('select.genericlist',  $tree,  $this->name, 'class="inputbox" multiple="multiple"', 'value', 'text', $this->value);
   }
}
