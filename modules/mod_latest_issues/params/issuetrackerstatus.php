<?php
/*
 *
 * @Version       $Id: issuetrackerstatus.php 1929 2015-02-08 16:25:12Z geoffc $
 * @Package       Joomla Issue Tracker Latest Issues Module
 * @Subpackage    mod_latest_issues
 * @Release       1.3.0
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-02-08 16:25:12 +0000 (Sun, 08 Feb 2015) $
 *
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Class JFormFieldIssueTrackerStatus
 */
class JFormFieldIssueTrackerStatus extends JFormField
{
   protected $type      = 'IssueTrackerStatus';

   /**
    * @return mixed
    */
   protected function getInput() {

      $db = JFactory::getDBO();
      $db->setQuery( 'SELECT `id` AS value, `status_name` AS text FROM `#__it_status` WHERE state=1 ORDER BY status_name');
      $options = array();

      foreach( $db->loadObjectList() as $r){
         $options[] = JHTML::_('select.option',  $r->value, $r->text );
      }

      array_unshift($options, JHTML::_('select.option', '0', JText::_('JALL'), 'value', 'text'));
      return JHTML::_('select.genericlist',  $options,  $this->name, 'class="inputbox" multiple="multiple"', 'value', 'text', $this->value);
   }
}