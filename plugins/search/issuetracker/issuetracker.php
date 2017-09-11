<?php
/*
 *
 * @Version       $Id: issuetracker.php 1929 2015-02-08 16:25:12Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.5.0
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-02-08 16:25:12 +0000 (Sun, 08 Feb 2015) $
 *
 */

defined('_JEXEC') or die( 'Restricted access' );

JLoader::import('joomla.plugin.plugin');

/**
 * Search Plugin for Issue Tracker
 *
 * @package    Joomla
 * @subpackage Issue Tracker
 */
class plgSearchIssuetracker extends JPlugin {

   /**
   * Get an array of search areas
   *
   * @return array
   */
   function &onContentSearchAreas() {
      static $areas = array('issuetracker' => 'Issues');
      return $areas;
   }

   /**
   * Issue Tracker search. Gets an array of objects, each
   * of which contains the instance variables title, text,
   * href, section, created, and browsernav
   *
   * @param string $text Search string
   * @param string $phrase Matching option, exact|any|all
   * @param string $ordering What to order by, newest|oldest|popular|alpha|category
   * @param array $areas Areas in which to search, null if search all
   * @return array Objects representing foobars
   */
   function onContentSearch($text, $phrase='', $ordering='', $areas=null) {

      // check we can handle the requested search
      if (is_array($areas) && !in_array('issuetracker', $areas)) {
         // not one of our areas... leave it alone!
         return array();
      }

      // Return if empty search string.
      $text = trim($text);
      if ($text == '') {
         return array();
      }

      $sIssues       = $this->params->get('search_issues',    1);
      $limit         = $this->params->def('search_limit',      50);
      $state = array();
      if ($sIssues) {
         $state[]=1;
      }

      // get the things we will need
      $db = JFactory::getDBO();

      // build SQL conditions WHERE clause
      $conditions = '';

      switch ($phrase) {

         case 'exact':
            // build an exact match LIKE condition
            $text = $db->Quote('%'.$db->escape($text, true).'%', false);
            $conditions = $db->quoteName('issue_summary') . " LIKE $text";
            $conditions .= " OR " . $db->quoteName('issue_description') . " LIKE $text";
            $conditions .= " OR " . $db->quoteName('resolution_summary') . " LIKE $text";
            $conditions .= " OR i." . $db->quoteName('alias') . " LIKE $text";
            break;

         case 'all':
         case 'any':
         default:
            // prepare the words individually
            $wordConditions = array();
            foreach (preg_split("~\s+~", $text) as $word) {
               $word = $db->Quote('%'.$db->escape($word, true).'%', false);
               $wordConditions[] = $db->quoteName('issue_summary') . " LIKE $word"
                        . " OR " . $db->quoteName('issue_description') . " LIKE $word"
                        . " OR " . $db->quoteName('resolution_summary') . " LIKE $word"
                        . " OR i." . $db->quoteName('alias') . " LIKE $word";
            }
            // determine the glue and put it all together!
            $glue = ($phrase == 'all') ? ') AND (' : ') OR (';
            $conditions = '('.implode($glue, $wordConditions).')';
         break;
      }

      // determine ordering
      switch ($ordering) {

         case 'alpha':
            $order = $db->quoteName('title') . ' ASC';
            break;

         case "oldest":
            $order = $db->quoteName('created') . ' ASC';
            break;

         case 'popular':

         case "newest":
         default:
            $order = $db->quoteName('created') . ' DESC';
            break;
      }

      //replace nameofplugin
      // $searchNameofplugin = JText::_( 'IssueTracker' );

      $rows = array();
      if (!empty($state)) {
         $user = JFactory::getUser();
         $groups  = implode(',', $user->getAuthorisedViewLevels());

         // complete the query   This will only display the issue description and resolution_summary.
         //  Note we have not searched on the Progress text!
        $query = "SELECT CONCAT_WS(': ', i.alias, i.issue_summary) AS title, "
               . "CASE WHEN CHAR_LENGTH(i.resolution_summary) THEN CONCAT_WS(': ', i.issue_description, i.resolution_summary) ELSE i.issue_description END AS text, "
               . "i.created_on AS created,"
               . "p.title AS section, "
               . "i.id as slug,   "
               . $db->Quote('2') . " AS browsernav "
               . " FROM #__it_issues AS i "
               . " INNER JOIN #__it_projects AS p ON p.id = i.related_project_id"
               . " WHERE (".$conditions.") "
               . " AND i.state = 1 AND i.public = 1 "
               . " AND i.access IN (".$groups.")"
               . " AND p.access IN (".$groups.")"
               . " ORDER BY $order";

         $db->setQuery($query, 0, $limit);
         $rows = $db->loadObjectList();

         if (isset($rows))
         {
            foreach($rows as $key => $row) {
               $rows[$key]->href = JRoute::_('index.php?option=com_issuetracker&view=itissues&id='.$row->slug );
            }
         }
      }
      return $rows;
   }
}