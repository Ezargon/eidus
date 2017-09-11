<?php
/*
 *
 * @Version       $Id: router.php 2050 2015-08-09 09:48:51Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.7
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-08-09 10:48:51 +0100 (Sun, 09 Aug 2015) $
 *
 */
defined('_JEXEC') or die('Restricted Access');

/**
 * IssueTrackerBuildRoute
 *
 * @param   array $query The sql query
 * @return  array
 *
 */
function IssueTrackerBuildRoute($query)
{
   $segments = array();

    if (isset($query['task'])) {
        $segments[] = implode('/', explode('.', $query['task']));
        unset($query['task']);
    }
/*
    if (isset($query['view'])) {
        $segments[] = $query['view'];
        unset($query['view']);
    }
*/
    if (isset($query['id'])) {
        $segments[] = $query['id'];
        unset($query['id']);
    }

   return $segments;
}

/**
 * IssueTrackerParseRoute
 *
 * @param $segments
 * @return array
 *
 * Formats:
 *
 * index.php?/issuetracker/task/id/Itemid
 *
 * index.php?/issuetracker/id/Itemid
 */
function IssueTrackerParseRoute($segments)
{
   $vars = array();

   // view is always the first element of the array
   $count = count($segments);

   /*
    echo "count: " . $count;
    echo "<br />";
    echo "segments 0: " . $segments[0];
    echo "<br />";
    echo "segments 1: " . $segments[1];
    echo "<br />";
   */

   if ($count) {
      $count--;
      $segment = array_shift($segments);
      if (is_numeric($segment)) {
         $vars['id'] = $segment;
      } else {
         $vars['task'] = $segment;
      }
   }

   if ($count) {
      $count--;
      $segment = array_shift($segments) ;
      if (is_numeric($segment)) {
         $vars['id'] = $segment;
      }
   }

   return $vars;
}