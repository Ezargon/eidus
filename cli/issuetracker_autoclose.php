<?php
/*
 *
 * @Version       $Id: issuetracker_autoclose.php 2018 2015-05-11 15:18:40Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.8
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-05-11 16:18:40 +0100 (Mon, 11 May 2015) $
 *
 */

// Make sure we're being called from the command line, not a web interface
if (array_key_exists('REQUEST_METHOD', $_SERVER)) die();

/**
 * This is a CRON script which should be called from the command-line, not the
 * web. For example something like:
 * /usr/bin/php /path/to/site/cli/scriptname.php
 */

// Set flag that this is a parent file.
define('_JEXEC', 1);
define('DEBUG', 0);
define('DS', DIRECTORY_SEPARATOR);

error_reporting(E_ALL | E_NOTICE);
ini_set('display_errors', 1);

// Load system defines
if (file_exists(dirname(dirname(__FILE__)) . '/defines.php')) {
   require_once dirname(dirname(__FILE__)) . '/defines.php';
}

if (!defined('_JDEFINES')) {
   define('JPATH_BASE', dirname(dirname(__FILE__)));
   require_once JPATH_BASE . '/includes/defines.php';
}

// Load the rest of the necessary files
if (file_exists(JPATH_LIBRARIES . '/import.legacy.php')) {
   require_once JPATH_LIBRARIES . '/import.legacy.php';
} else {
   require_once JPATH_LIBRARIES . '/import.php';
   // Force library to be in JError legacy mode
   JError::$legacy = true;
}
require_once JPATH_LIBRARIES . '/cms.php';

// Import necessary classes not handled by the autoloaders
jimport('joomla.application.menu');
jimport('joomla.environment.uri');
jimport('joomla.event.dispatcher');
jimport('joomla.utilities.utility');
jimport('joomla.utilities.arrayhelper');

// Load the Configuration
require_once JPATH_CONFIGURATION . '/configuration.php';

if (! class_exists('IssuetrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}

if (! class_exists('IssuetrackerHelperLog')) {
   require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'log.php');
}

if (! class_exists('IssuetrackerHelperCron')) {
   require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'cron.php');
}

// Load Library language
$lang = JFactory::getLanguage();

// Try the com_issuetracker cli file in the current language (without allowing the loading of the file in the default language)
$lang->load('com_issuetracker', JPATH_SITE, null, false, false)
// Fallback to the com_ipmapping cli file in the default language
|| $lang->load('com_issuetracker', JPATH_SITE, null, true);


/**
 * This script will autoclose issues which have not been responded to in the specified number of days speeding up your administrator.
 *
 * @package  Joomla.CLI
 * @since    2.5
 */
class Issuetrackerautoclose extends JApplicationCli
{
   /**
    * Entry point for the script
    *
    * @return  void
    *
    * @since   2.5
    */
   public function doExecute()
   {
      $db = JFactory::getDBO();
      // Get the query builder class from the database.
      $query = $db->getQuery(true);
      // Set up a query to select everything in the 'db' table.
      $query->select('version')
            ->from($db->qn('#__it_meta'))
            ->where('type = "component"');

      // Push the query builder object into the database connector.
      $db->setQuery($query);

      // Get all the returned rows from the query as an array of objects.
      $version = $db->loadResult();

      date_default_timezone_set('UTC');
      $date             = date(DATE_RFC2822);
      $year             = gmdate('Y');
      $phpversion       = PHP_VERSION;
      $phpenvironment   = PHP_SAPI;
      // $phpos            = PHP_OS;

         echo <<<ENDBLOCK
Issue Tracker $version CLI $version ($date)
Copyright (C) 2012-$year Macrotone Consulting Ltd
-------------------------------------------------------------------------------
Issue Tracker is Free Software, distributed under the terms of the GNU General
Public License version 3 or, at your option, any later version.
This program comes with ABSOLUTELY NO WARRANTY as per sections 15 & 16 of the
license. See http://www.gnu.org/licenses/gpl-3.0.html for details.
-------------------------------------------------------------------------------
You are using PHP $phpversion ($phpenvironment)
ENDBLOCK;
      echo "\n\n";

      jimport('joomla.application.component.helper');
      $component = JComponentHelper::getComponent('com_issuetracker');

      $params = $component->params;
      $logging = $params->get('enablelogging');

      // Get value from parameters for status  Awaiting customer.
      $cust_status   = $params->get('waiting_customer_status', 0);
      if ( $cust_status == 0 ) {
         if ($logging )
            IssueTrackerHelperLog::dblog(JText::_("COM_ISSUETRACKER_ERROR_NO_CUSTOMER_STATUS_MSG"),JLog::ERROR);
         exit();
      }
      $closed_status = $params->get('closed_status', '1');
      if ($closed_status == $cust_status) {
         if ($logging )
            IssueTrackerHelperLog::dblog(JText::_("COM_ISSUETRACKER_AUTOCLOSE_STATUSES_ARE_IDENTICAL_MSG"),JLog::ERROR);
         exit();
      }

      $query = "SELECT status_name FROM `#__it_status` WHERE id IN (".$closed_status.','.$cust_status.")";
      $db->setQuery($query);
      $scol          = $db->loadColumn();
      $closed_text   = $scol[0];
      $cust_text     = $scol[1];

      $overdue_days  = $params->get('overdue_days', 21);

      $olderthan  = $this->input->get('olderthan', 0);  // Use value on command line if given otherwise use component setting.
      if ( $olderthan == 0 ) $olderthan = $overdue_days;
      if (DEBUG) echo "Params: ".$olderthan."\n";

      $consider = date("Y-m-d", strtotime(-$olderthan . ' DAYS'));

      $this->out(JText::sprintf('COM_ISSUETRACKER_AUTOCLOSE_PERIOD_TEXT_MSG', $consider)."\n\n");

      if (DEBUG) echo "Params: ".$consider."\n";

      // Get all issues older than $consider days and in the 'waiting_user' category.
      // Criteria for the user is no progress update older than specifed date.
      $query  = "SELECT i.id, i.alias FROM `#__it_issues` AS i ";
      $query .= "LEFT JOIN (SELECT issue_id, modified_on, created_on, lineno ";
      $query .= "            FROM (SELECT t1.* FROM `#__it_progress` AS t1 ";
      $query .= "                  JOIN (SELECT issue_id, MAX(lineno) lineno FROM `#__it_progress` GROUP BY issue_id) AS t2 ";
      $query .= "                   ON t1.issue_id = t2.issue_id AND t1.lineno = t2.lineno) AS t3 ";
      $query .= "           ) AS p ON p.issue_id = i.id ";
      $query .= "WHERE i.status = ".intval($cust_status);
      $query .= "  AND (( (i.modified_on = '0000-00-00 00:00:00' OR i.modified_on is NULL ) && i.created_on <= '".$consider." 08:00:00' ) ";
      $query .= "      OR ( i.modified_on <= '".$consider." 08:00:00' ))  ";
      $query .= "  AND (( (p.modified_on = '0000-00-00 00:00:00' OR p.modified_on is NULL ) && p.created_on <= '".$consider." 08:00:00' ) ";
      $query .= "       OR ( p.modified_on <= '".$consider." 08:00:00' )); ";

      if (DEBUG) echo '    Query:'.$query."\n";
      $db->setQuery($query);
      $issuelist = $db->loadAssocList();

      if ( empty($issuelist) ) {
        if ($logging) IssueTrackerHelperLog::dblog(JText::_("COM_ISSUETRACKER_INFO_NO_AUTOCLOSE_ISSUES_MSG"),JLog::INFO);
        exit();
      }

      // Now loop through the issues.
      while (list($key, $val) = each( $issuelist)) {
         $issue_id = $issuelist[$key]['id'];
         $alias    = $issuelist[$key]['alias'];

         if (DEBUG) echo "Processing Issue: ".$issue_id." - ".$alias."\n";

         $query  = "UPDATE `#__it_issues` ";
         $query .= "SET status = ".intval($closed_status);         // modified on set by trigger.
         $query .= " WHERE `id` = ".$issue_id;

         if (DEBUG) echo "Close issue SQL: ".$query."\n";

         $db->setQuery($query);
         $result = $db->execute();

         if (!$result) {
            if ($logging )
               IssueTrackerHelperLog::dblog(JText::sprintf("COM_ISSUETRACKER_ERROR_CLOSING_ISSUE_MSG", $alias, $issue_id),JLog::ERROR);
            echo JText::sprintf("COM_ISSUETRACKER_ERROR_CLOSING_ISSUE_MSG", $alias. $issue_id)."\n";
         } else {
            // Update progress and resolution with auto close message.
            $ntext = JText::sprintf("COM_ISSUETRACKER_AUTO_CRON_STATUS_CHANGED_MSG", $alias, $issue_id, $cust_text, $closed_text);
            IssueTrackerHelper::add_progress_change($issue_id, $ntext);
            IssueTrackerHelper::add_resolution_change($issue_id, $ntext);

            IssueTrackerHelper::send_auto_close_msg($issue_id);

            if ( $logging )
               IssueTrackerHelperLog::dblog(JText::sprintf("COM_ISSUETRACKER_CLOSED_ISSUE_MSG", $alias, $issue_id));
         }
      }
   }
}

JApplicationCli::getInstance('Issuetrackerautoclose')->execute();