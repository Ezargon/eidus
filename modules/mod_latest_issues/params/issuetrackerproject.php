<?php
/*
 *
 * @Version       $Id: issuetrackerproject.php 1929 2015-02-08 16:25:12Z geoffc $
 * @Package       Joomla Issue Tracker Latest Issues module.
 * @Subpackage    mod_latest_issues
 * @Release       1.4.1
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-02-08 16:25:12 +0000 (Sun, 08 Feb 2015) $
 *
 */
defined('_JEXEC') or die('Restricted access');

// Required for Joomla 3.0
if(!defined('DS')){
   define('DS',DIRECTORY_SEPARATOR);
}
/*
if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}
*/
/**
 * Class JFormFieldIssueTrackerProject
 */
class JFormFieldIssueTrackerProject extends JFormField
{
   protected $type      = 'IssueTrackerProject';

   /**
    * @return mixed
    */
   protected function getInput() {

      // Get user to permit filtering by access level.
//      $user = JFactory::getUser();
//      $groups  = implode(',', $user->getAuthorisedViewLevels());

      $db = JFactory::getDBO();

       //build the list of projects
      $query = 'SELECT a.title AS text, a.id AS value, a.parent_id as parentid'
      . ' FROM #__it_projects AS a'
      . ' WHERE a.state = 1'
//      . ' AND a.access IN ('.$groups.')';
      . ' ORDER BY a.lft';
      $db->setQuery( $query );
      $data = $db->loadObjectList();

      $catId   = -1;
//      $required   = ((string) $this->element['required'] == 'true') ? TRUE : FALSE;

      $tree = array();
      $text = '';
      $tree = self::ProjectTreeOption($data, $tree, 0, $text, $catId);

      array_unshift($tree, JHTML::_('select.option', '0', JText::_('JALL'), 'value', 'text'));
//      array_unshift($tree, JHTML::_('select.option', '', '- '.JText::_('COM_ISSUETRACKER_SELECT_PROJECT').' -', 'value', 'text'));
      return JHTML::_('select.genericlist',  $tree,  $this->name, 'class="inputbox" multiple="multiple"', 'value', 'text', $this->value);
   }

   /**
    * @param $data
    * @param $tree
    * @param int $id
    * @param string $text
    * @param $currentId
    * @return mixed
    */
   public static function ProjectTreeOption($data, $tree, $id=0, $text='', $currentId)
   {
      if ( $id == 0 ) {
         $db = JFactory::getDBO();
         $query = "SELECT id FROM `#__it_projects` WHERE title= 'Root' ";
         $db->setQuery( $query );
         $rid = $db->loadResult();
      } else {
         $rid = $id;
      }

      foreach ($data as $key) {
         $show_text =  $text . $key->text;

         if ($key->parentid == $rid && $currentId != $rid && $currentId != $key->value) {
            $tree[$key->value]         = new JObject();
            $tree[$key->value]->text   = $show_text;
            $tree[$key->value]->value  = $key->value;
            $tree = self::ProjectTreeOption($data, $tree, $key->value, $show_text . " - ", $currentId );
         }
      }
      return($tree);
   }
}