<?php
/*
 *
 * @Version       $Id: issuetracker_r_status.php 1929 2015-02-08 16:25:12Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.3.0
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
 * Class JFormFieldIssueTrackerRstatus
 */
class JFormFieldIssueTrackerRstatus extends JFormField
{
   protected $type      = 'IssueTrackerRstatus';

   /**
    * @return array
    */
   protected function getInput() {

      // get settings from com_issuetracker parameters
      $params  = JComponentHelper::getParams('com_issuetracker');

      $open_status         = $params->get('open_status', '4');
      $closed_status       = $params->get('closed_status', '1');

      // Build the filter options.
      $options = array();
      $options[]  = JHtml::_('select.option', '',  JText::_('COM_ISSUETRACKER_SELECT_STATUS'));
      $options[]  = JHtml::_('select.option', $open_status,   JText::_('COM_ISSUETRACKER_REOPEN'));
      $options[]  = JHtml::_('select.option', $closed_status, JText::_('COM_ISSUETRACKER_CLOSED'));

      return $options;
   }
}