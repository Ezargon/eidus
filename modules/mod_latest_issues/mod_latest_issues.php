<?php
/*
 *
 * @Version       $Id: mod_latest_issues.php 1929 2015-02-08 16:25:12Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    mod_latest_issues
 * @Release       1.6.3
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

if(!include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php')){
   echo JText::_('MOD_LATEST_ISSUES_MESSAGE');
   return;
};

$nbissues = intval($params->get('no_issues',5));
// $ordering = ($params->get('ordering') == 'alias') ? 'i.alias' : 'i.actual_resolution_date';
$order = $params->get('ordering');
switch ($order) {
   case 'actual_resolution_date':
      $ordering = 'i.actual_resolution_date';
      break;
   case 'identified_date':
      $ordering = 'i.identified_date';
      break;
   case 'priority':
      $ordering = 'i.priority';
      break;
   case 'issue_type':
      $ordering = 'i.issue_type';
      break;
   case 'alias':
      $ordering = 'i.alias';
      break;
   case 'project':
      $ordering = 'p.title';
      break;
   default:
      $ordering = 'i.actual_resolution_date';
   }

$projects = $params->get('projects',0);

$query  = 'SELECT i.id, i.actual_resolution_date, i.alias, i.issue_summary, p.title ';
$query .= ' ,p.id AS project_id ';
$query .= ' ,s.status_name AS status ';
$query .= 'FROM #__it_issues as i ';
$query .= 'LEFT JOIN #__it_projects as p on i.related_project_id = p.id ';
$query .= 'LEFT JOIN #__it_status as s on i.status = s.id ';
$query .= 'WHERE i.state = 1 AND i.public = 1 ';

// Get component version Expects string such as xx.xx.xx
$db     =  JFactory::getDBO();
$db->setQuery("SELECT version from `#__it_meta` WHERE type = 'component'");
$version = $db->loadResult();
$fpos = strpos($version, '.');
$vcnt = intval(substr($version, 0, $fpos)) * 10000;
$spos = strpos($version, '.', $fpos+1);
$vcnt += intval(substr($version, $fpos+1, $spos-$fpos)) * 100;
$vcnt += intval(substr($version, $spos+1));

// $app = JFactory::getApplication('site');
// $app->enqueueMessage($vcnt);

// Thus 1.3.3 should become 10000 + 300 + 3 = 10303
if ($vcnt >= 10303) {
   // Filter by access level.
   $user = JFactory::getUser();
   $groups  = implode(',', $user->getAuthorisedViewLevels());
   $query .= ' AND i.access IN ('.$groups.')';
   $query .= ' AND p.access IN ('.$groups.')';
}

if ( ! empty($projects) && $projects[0] != "" ) {
   // Check if we have 0 as the first element in our array, if so ignore the where clause inclusion.
   $pids = implode("','", $projects);                   // Put in a form suitable for our query.
   if ( substr($pids, 0, 1) == ',')  $pids = substr($pids,1);   // Check that first character is not a comma.
   if (strncmp($pids, '0',1 ) != 0) {
      $query .= "AND i.related_project_id IN ('".$pids."') ";
   }
}

/*
if($projects != '0'){
//   $projectids = explode(',',$projects);
   $query .= "AND i.related_project_id IN ('".implode("','",$projects)."') ";
}
*/

$status = $params->get('status', 0);
if( $status[0] != '0'){
   $query .= "AND i.status IN ('".implode("','",$status)."') ";
}

$query .= "ORDER BY ".$ordering." DESC ";
if(!empty($nbissues)) $query .= "LIMIT ".$nbissues;

if($params->get('popup')){
   JHTML::_('behavior.modal','a.modal');
}

$db->setQuery($query);
$issues = $db->loadObjectList();
$issues = IssueTrackerHelper::updateprojectname($issues);

require(JModuleHelper::getLayoutPath('mod_latest_issues'));