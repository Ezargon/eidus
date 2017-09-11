<?php
/*
 *
 * @Version       $Id: issuetrackerpersonfe.php 1945 2015-03-03 11:09:50Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.7
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-03-03 11:09:50 +0000 (Tue, 03 Mar 2015) $
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
 * Class JFormFieldIssueTrackerPersonfe
 */
class JFormFieldIssueTrackerPersonfe extends JFormField
{
   protected $type      = 'IssueTrackerPerson';

   /**
    * @return mixed
    */
   protected function getInput()
   {
      $tree = array();
      $tree = IssueTrackerHelper::getPerson_name();

//      array_unshift($tree, JHTML::_('select.option', '0', JText::_('JALL'), 'value', 'text'));
      array_unshift($tree, JHTML::_('select.option', '', '- '.JText::_('COM_ISSUETRACKER_SELECT_PERSON').' -', 'value', 'text'));
      return JHTML::_('select.genericlist',  $tree,  $this->name, 'OnChange="getnotifyvalue();" class="inputbox"', 'value', 'text', $this->value);
   }
}
