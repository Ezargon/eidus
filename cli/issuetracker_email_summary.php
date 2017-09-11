<?php
/*
 *
 * @Version       $Id: issuetracker_email_summary.php 1981 2015-03-16 09:52:48Z geoffc $
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
class Issuetrackeresummary extends JApplicationCli
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

      // Get the start and end dates
      $sdate  = $this->input->get('sdate', '', 'string');
      $edate  = $this->input->get('edate', '', 'string');
      // If not specified display the last months worth of data.
      if ( empty($sdate) ) $sdate = date("Y-m-d", strtotime("-1 months"));
      if ( empty($edate) ) $edate = date("Y-m-d");

      $this->out(JText::_('COM_ISSUETRACKER_REPORT_PERIOD_TEXT').$sdate.' -> '.$edate."\n\n");

      // Get list of email addresses to whom summary is to be sent.
      $query  = "SELECT distinct username, person_email ";
      $query .= "FROM `#__it_people` ";
      $query .= "WHERE ( issues_admin = 1 OR staff = 1 )";
      $query.= " AND email_notifications = 1";
      $db->setQuery($query);
      if (DEBUGEMAIL) echo '    Query:'.$query."\n";
      $emailaddrs = $db->loadAssocList();

      if ( empty($emailaddrs) && $logging ) {
         IssueTrackerHelperLog::dblog(JText::_("COM_ISSUETRACKER_WARNING_NO_REPORT_RECIPIENTS_MSG"),JLog::INFO);
         exit();
      }

      // Get summary report template if it exists
      $query = "SELECT subject, body FROM #__it_emails WHERE type = 'summary_report' AND state = 1";
      $db = JFactory::getDBO();
      $db->setQuery($query);
      $mdetails = $db->loadRow();

      // Generate report.
      $rows = IssuetrackerHelperCron::issuesummary($sdate, $edate);
      // if (DEBUGEMAIL) var_dump($rows);

      // $SiteName   = $params->get('emailSiteName', '');
      $fromadr    = $params->get('emailFrom', '');
      $sender     = $params->get('emailSender', '');
      // $link       = $params->get('emailLink', '');
      $replyto    = $params->get('emailReplyto', '');
      $replyname  = $params->get('emailReplyname','');
      // $subprefix  = $params->get('emailADMSubject', '');

      // $subject = "Summary Report";
      $subject = JText::_('COM_ISSUETRACKER_SUMMARY_REPORT_MSG');

      // Report header
      if ( empty($mdetails) ) {
         $message  =  '<div style="padding: 0 20px;">';
         $message .=  '<span style="display: block; font-size: 18px; font-weight: bold; margin: 25px 0 -15px 0;"> '. JText::_('COM_ISSUETRACKER_SUMMARY_REPORT_MSG') . ' - ' . $date . '</span><br /><br />';
         $message .= JText::_('COM_ISSUETRACKER_REPORT_PERIOD_TEXT').$sdate.' -> '.$edate.'<br />';
      } else {
         $message = JText::_('COM_ISSUETRACKER_REPORT_PERIOD_TEXT').$sdate.' -> '.$edate.'<br /><br />';
      }

      // Add table headers
      $message .=  "<table><tr>";
      $message .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_PROJECT_NAME')."</th>";
      $message .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_FIRST_OPENED_DATE')."</th>";
      $message .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_LAST_CLOSED_DATE')."</th>";
      $message .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_LAST_MODIFIED_DATE')."</th>";
      $message .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_TOTAL_ISSUES')."</th>";
      $message .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_OPEN_ISSUES')."</th>";
      $message .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_ONHOLD_ISSUES')."</th>";
      $message .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_INPROGRESS_ISSUES')."</th>";
      $message .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_CLOSED_ISSUES')."</th>";
      $message .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_OPEN_NOPRIOR')."</th>";
      $message .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_OPEN_HIGH')."</th>";
      $message .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_OPEN_MEDIUM')."</th>";
      $message .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_OPEN_LOW')."</th>";
      $message .=  "</tr>";

      // Format row results
      foreach ($rows as $row) {
         $message .= "<tr>";
         $message .= "<td>" . $row->project_name . "</td>";
         // $message .= "<td>" . $row->project_id . "</td>";
         $message .= "<td>" . $row->first_identified . "</td>";
         $message .= "<td>" . $row->last_closed . "</td>";
         $message .= "<td>" . $row->last_modified . "</td>";
         $message .= '<td style="text-align: center">' . $row->total_issues . "</td>";
         $message .= '<td style="text-align: center">' . $row->open_issues . "</td>";
         $message .= '<td style="text-align: center">' . $row->onhold_issues . "</td>";
         $message .= '<td style="text-align: center">' . $row->inprogress_issues . "</td>";
         $message .= '<td style="text-align: center">' . $row->closed_issues . "</td>";
         $message .= '<td style="text-align: center">' . $row->open_no_prior . "</td>";
         $message .= '<td style="text-align: center">' . $row->open_high_prior . "</td>";
         $message .= '<td style="text-align: center">' . $row->open_medium_prior . "</td>";
         $message .= '<td style="text-align: center">' . $row->open_low_prior . "</td>";
         $message .= "</tr>";
      }
      $message .= "</table>";

      if (empty($mdetails) ) {
         // Add date to bottom of report.
         $message .= '<div style="position: fixed; bottom: 0; height: 40px; width: 100%; margin: 20px 0 0 0; background: #000; color: #FFF; line-height: 40px;">' .
         'Report generated ' . $date . '</div>';
         $body    = $message;
      } else {
         $message .= '<br /><div style="height: 40px; width: 100%; margin: 20px 0 0 0; background: #000; color: #FFF; line-height: 40px;">' .
         'Report generated ' . $date . '</div>';
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
         IssueTrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_SUMMARY_REPORT_MSG'));

      // For efficiency build up the recipient list so we only send one email.
      $recipient = array();
      reset( $emailaddrs);
      while (list($key, $val) = each( $emailaddrs)) {
         // $username = $emailaddrs[$key]['username'];
         $email    = $emailaddrs[$key]['person_email'];
         if ( JMailHelper::isEmailAddress( $email ) ) {
            $recipient[] = $email;
         }
      }

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
      unset ($mail);
   }
}

JApplicationCli::getInstance('Issuetrackeresummary')->execute();