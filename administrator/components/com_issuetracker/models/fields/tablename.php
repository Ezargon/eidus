<?php
/*
 *
 * @Version       $Id: tablename.php 1929 2015-02-08 16:25:12Z geoffc $
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

if (! class_exists('IssueTrackerAuditHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'audit.php');
}

/**
 * Class JFormFieldJauditTablename
 */
class JFormFieldJauditTablename extends JFormField
{
   protected $type      = 'IssuetrackerTablename';

   /**
    * @return mixed
    */
   protected function getInput()
   {
      $tree = array();
      $tree = IssuetrackerAuditHelper::getTablename();

//      array_unshift($tree, JHTML::_('select.option', '0', JText::_('JALL'), 'value', 'text'));
      return JHTML::_('select.genericlist',  $tree,  $this->name, 'class="inputbox"', 'value', 'text', $this->value);
   }
}