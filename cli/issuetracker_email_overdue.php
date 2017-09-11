<?php
/*
 *
 * @Version       $Id: issuetracker_email_overdue.php 1981 2015-03-16 09:52:48Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.7
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-03-16 09:52:48 +0000 (Mon, 16 Mar 2015) $
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
define('DEBUGEMAIL', 0);
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
 * This script will fetch the update information for all extensions and store
 * them in the database, speeding up your administrator.
 *
 * @package  Joomla.CLI
 * @since    2.5
 */
class Issuetrackereoverdue extends JApplicationCli
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
      echo "\n";

      jimport('joomla.application.component.helper');
      $component = JComponentHelper::getComponent('com_issuetracker');

      $params = $component->params;
      $logging = $params->get('enablelogging');

      // $SiteName   = $params->get('emailSiteName', '');
      $fromadr    = $params->get('emailFrom', '');
      $sender     = $params->get('emailSender', '');
      // $link       = $params->get('emailLink', '');
      $replyto    = $params->get('emailReplyto', '');
      $replyname  = $params->get('emailReplyname','');

      $subject = JText::sprintf('COM_ISSUETRACKER_OVERDUE_REPORT_MSG','');

      // Get list of assignees to whom the report should be sent.
      // To be assigned the person has to be a staff member
      $query  = "SELECT distinct username, person_email, user_id ";
      $query .= "FROM `#__it_people` p ";
      $query .= "LEFT JOIN `#__it_issues` i  ";
      $query .= "ON p.user_id = i.assigned_to_person_id  ";
      $query .= "WHERE ( issues_admin = 1 OR staff = 1 )";
      $query.= " AND email_notifications = 1";
      $db->setQuery($query);
      if (DEBUGEMAIL) echo '    Query:'.$query."\n";
      $emailaddrs = $db->loadAssocList();

      if ( empty($emailaddrs) && $logging ) {
         IssueTrackerHelperLog::dblog(JText::_("COM_ISSUETRACKER_WARNING_NO_REPORT_RECIPIENTS_MSG"),JLog::INFO);
         exit();
      }

      // Get overdue report template if it exists
      $query = "SELECT subject, body FROM #__it_emails WHERE type = 'overdue_report' AND state = 1";
      $db = JFactory::getDBO();
      $db->setQuery($query);
      $mdetails = $db->loadRow();

      // Report header and footer
      if ( empty($mdetails) ) {
         $rheader  =  '<div style="padding: 0 20px;">';
         $rheader .=  '<span style="display: block; font-size: 18px; font-weight: bold; margin: 25px 0 -15px 0;"> '. JText::sprintf('COM_ISSUETRACKER_OVERDUE_REPORT_MSG','') . ' ' . $date . '</span><br /><br />';

         // Prepare report footer.
         $rfooter  = '<div style="position: fixed; bottom: 0; height: 40px; width: 100%; margin: 20px 0 0 0; background: #000; color: #FFF; line-height: 40px;">' .
         'Report generated ' . $date . '</div>';
      } else {
         $rheader = '';
         $rfooter = '';
      }

      // Table headers
      $rheader .=  "<table><tr>";
      $rheader .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_ISSUE_NUMBER')."</th>";
      $rheader .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_ISSUE_SUMMARY')."</th>";
      $rheader .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_PRIORITY')."</th>";
      $rheader .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_IDENTIFIED_DATE')."</th>";
      $rheader .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_PROJECT_NAME')."</th>";
      $rheader .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_FIELD_STATUS_LABEL')."</th>";
      $rheader .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_ISSUE_OVERDUE_DAYS')."</th>";
      $rheader .=  "</tr>";

      // Create report for each recipient
      foreach ($emailaddrs as $eaddr) {
         // Generate report.
         $uid = $eaddr['user_id'];
         $message = '';

         $rows = IssuetrackerHelperCron::issueoverdue($uid);

         // Format row results
         foreach ($rows as $row) {
            $message .= "<tr>";
            $message .= '<td style="text-align: center">' . $row->alias . "</td>";
            $message .= "<td>" . $row->issue_summary . "</td>";
            $message .= '<td style="text-align: center">' . $row->priority . "</td>";
            $message .= '<td style="text-align: center">' . $row->ident_date . "</td>";
            $message .= "<td>" . $row->project_name . "</td>";
            $message .= '<td style="text-align: center">' . $row->status_name . "</td>";
            $message .= '<td style="text-align: center">' . $row->overdue . "</td>";
            $message .= "</tr>";
         }
         $message .= "</table>";

         if (empty($mdetails) ) {
            $body    = $rheader.$message.$rfooter;
         } else {
            $message = $rheader.$message;
            $body = str_replace('[REPORT]', $message, $mdetails[1]);
         }

         // Clean the email data
         if ( empty($mdetails) ) {
            $subject = JMailHelper::cleanSubject( $subject );
         } else {
            $subject = JMailHelper::cleanSubject( $mdetails[0] );
         }
         $body    = JMailHelper::cleanBody( $body );
         $fromadr = JMailHelper::cleanAddress( $fromadr );

         if ( $logging )
            IssueTrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_OVERDUE_REPORT_MSG',$eaddr['username']));

         $recipient = array();
         $recipient[] = $eaddr['person_email'];

         $mail = JFactory::getMailer();
         $mail->isHTML(true);
         $mail->Encoding = 'base64';
         $mail->addRecipient($recipient);
         if ( !empty($replyto) ) $mail->addReplyTo(array($replyto,$replyname));
         $mail->setSender(array($fromadr, $sender));
         $mail->setSubject($subject);
         $mail->setBody($body);

         if (!$mail->Send()) {
            if ( $logging )
               IssueTrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_MAIL_SENDING_ERROR'),JLog::ERROR);
            // echo "<pre>"; var_dump ($mail); echo "</pre>";
            // die ("In send email ");
            return;   // if there was trouble, return false for error checking in the caller
         }
         unset($mail);
      }
   }
}

JApplicationCli::getInstance('Issuetrackereoverdue')->execute();