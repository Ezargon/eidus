<?php
/*
 *
 * @Version       $Id: issuetracker_email_fetch.php 2050 2015-08-09 09:48:51Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.8
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-08-09 10:48:51 +0100 (Sun, 09 Aug 2015) $
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
define('DEBUGIMAP', 0);
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

if (! class_exists('Akismet')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'classes'.DS.'Akismet.php');
}

// Load Library language
$lang = JFactory::getLanguage();

// Try the com_issuetracker cli file in the current language (without allowing the loading of the file in the default language)
$lang->load('com_issuetracker', JPATH_SITE, null, false, false)
// Fallback to the com_ipmapping cli file in the default language
|| $lang->load('com_issuetracker', JPATH_SITE, null, true);


/**
 * This script will fetch any issue tracker emails from a mail server and store
 * them in the database issue tracker tables, and send an acknowledgement email
 * to the sender, and an email to the issue assignee.
 *
 * @package  Joomla.CLI
 * @since    2.5
 */
class Issuetrackerimap extends JApplicationCli
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
      // Get the issue tracker version from the database.
      $query = $db->getQuery(true);
      $query->select('version')
            ->from($db->qn('#__it_meta'))
            ->where('type = "component"');
      $db->setQuery($query);
      $version = $db->loadResult();

      date_default_timezone_set('UTC');
      $date             = date(DATE_RFC2822);
      $year             = gmdate('Y');
      $phpversion       = PHP_VERSION;
      $phpenvironment   = PHP_SAPI;
      // $phpos            = PHP_OS;

         echo <<<ENDBLOCK
Issue Tracker $version CLI ($date)
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
      $dlogging = $params->get('enabledebuglogging');

      // Check whether we have any command line parameters specified.
      // If so, use these in preference to the component settings for default project, mailbox user/password.
      // Parameters are specified by using --uname=xyz etc on the command line.
      $sname   = $this->input->get('uname','','raw');
      $spwd    = $this->input->get('pwd');
      $sproj   = $this->input->get('proj');

      // Need to check if assignee is specified if not all bets are off.
      $imap_assignee  = $params->get('imap_def_assignee', 0);
      $def_assignee   = $params->get('def_assignee', 1);
      if ($imap_assignee == 0 && $def_assignee == 1) {
         if ( $logging )
            IssuetrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_NOISSUE_ASSIGNEE_SPECIFIED_MSG'), JLog::ERROR);
         exit(JText::_('COM_ISSUETRACKER_IMAP_NOISSUE_ASSIGNEE_SPECIFIED_MSG'));
      }

      //Check if imap is enabled
      if ( $params->get('imap_enabled')) {
         // Fetch the connection parameters
         $server        = $params->get('imap_server');
         $username      = $params->get('imap_username');
         $password      = $params->get('imap_password');
         $connecttype   = $params->get('imap_connecttype');
         $imapport      = $params->get('imap_port');
         $pop3port      = $params->get('pop_port');
         $ssl           = $params->get('imap_ssl');
         $sslport       = $params->get('imap_ssl_port');
         $connect       = null;

         $def_project   = $params->get('imap_def_project');
         if ( empty($def_project) ) $def_project = $params->get('def_project', 10);

         // If parameters given on command line then use these.
         if ( ! empty($sname) || ! empty($spwd) || ! empty($sproj) ) {
            if ( $logging )
               IssuetrackerHelperLog::dblog('Using command line parameters for connection.', JLog::INFO);
               // IssuetrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_IMAP_COMMANDLINE_SETTINGS_SPECIFIED_MSG',$sname,$spwd,$sproj), JLog::ERROR);
            if ( !empty($sname) )   $username      = $sname;
            if ( !empty($spwd) )    $password      = $spwd;
            if ( !empty($sproj) )   {
               // Check that specified project value actually exists,. If not use component default.
               $query  = "SELECT COUNT(*) FROM `#__it_projects` ";
               $query .= "WHERE id = ".$db->quote($sproj);
               $db->setQuery($query);
               $cnt = $db->loadResult();

               if ( $cnt ) {
                  $def_project   = $sproj;
               } else {
                  if ( $logging )
                     IssuetrackerHelperLog::dblog('Invalid project '.$sproj.' specified using component default '.$def_project.'.', JLog::WARNING);
                     // IssuetrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_IMAP_INVALID_PROJECT_SPECIFIED_MSG',$sproj,$def_project), JLog::WARNING);
               }
            }
            if (DEBUGIMAP) echo 'Using command line parameters: '.$sname.'/'.$spwd.' Project: '.$def_project."\n";
         }

         if ( empty($server) ) {
            if ( $logging )
               IssuetrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_IMAP_NO_SERVER_SPECIFIED_MSG'), JLog::ERROR);
            exit(JText::_('COM_ISSUETRACKER_IMAP_NO_SERVER_SPECIFIED_MSG'));
         }

         if ( empty($username) ) {
            if ( $logging )
               IssuetrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_IMAP_NO_USERNAME_SPECIFIED_MSG'), JLog::ERROR);
            exit(JText::_('COM_ISSUETRACKER_IMAP_NO_USERNAME_SPECIFIED_MSG'));
         }

         if ( empty($password) ) {
            if ( $logging )
               IssuetrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_IMAP_NO_PASSWORD_SPECIFIED_MSG'), JLog::ERROR);
            exit(JText::_('COM_ISSUETRACKER_IMAP_NO_PASSWORD_SPECIFIED_MSG'));
         }

         // Configure connection port/type substring based on connection type and ssl parameter
         // The parameter novalidate-cert will stop cert errors of self-signed certs
         if ($connecttype == 1) {         //imap
            if($ssl) $connect = $sslport.'/novalidate-cert/imap/ssl';
            else $connect = $imapport;
            if ( $params->get('require_novalidate') ) $connect .= '/novalidate-cert';
         } elseif($connecttype == 2) {    //pop3
            if($ssl) $connect = $sslport.'/novalidate-cert/pop3/ssl';
            else $connect = $pop3port.'/pop3';
         } else {
            if ( $logging )
               IssuetrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_IMAP_INVALID_CONNECT_TYPE_SPECIFIED_MSG'), JLog::ERROR);
            exit(JText::_('COM_ISSUETRACKER_IMAP_INVALID_CONNECT_TYPE_SPECIFIED_MSG'));
         }

         if (DEBUGIMAP) {
            echo 'Server:       '.$server."\n";
            echo 'Username:     '.$username."\n";
            echo 'Password:     '.$password."\n";
            echo 'Connect type: '.$connecttype."\n";
            echo 'SSL:          '.$ssl."\n";
            echo 'Ports:        '.$connect."\n";
         }

         // Open the connection to the mail server
         $mail = imap_open('{'.$server.':'.$connect.'}', $username, $password);
         if ($mail) {
            if (DEBUGIMAP) echo 'Server connection opened'."\n\n";
         } else {
            if ( $logging )
               IssuetrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_IMAP_CANNOT_CONNECT_MSG',$server,$connect), JLog::ERROR);
            exit(JText::sprintf('COM_ISSUETRACKER_IMAP_CANNOT_CONNECT_MSG',$server,$connect)); // Cannot connect so exit
         }

         // Get the UNSEEN messages
         $emails = imap_search($mail, 'UNSEEN');
         // $emails = imap_search($mail, 'ALL');

         if (!$emails) {
            if (DEBUGIMAP) echo 'No new messages found'."\n";
            if ( $logging ) {
               IssuetrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_NO_MAIL_FOUND_MSG'));
               IssuetrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_EFETCH_FINISHED_MSG'));
            }
            imap_close($mail);
            exit();
         } else {
            JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'tables');

            // Email messages are present, so process each message
            $totalmessages = 0;
            $newcases      = 0;
            $existingcases = 0;
            $failsave      = 0;
            $attachcnt     = 0;
            $spamcnt       = 0;

            $emailList = explode("\r\n",$params->get('email_list',''));

            // If restricting emails to registered users.
            $restrict   = $params->get('restrict_known_users');
            $remails    = array();
            if ( $restrict)
               $remails = IssuetrackerHelperCron::get_registered_emails();

            foreach ($emails as $message) {
               if (DEBUGIMAP) echo "\nMessage: ".$message."\n";
               $overview   = imap_fetch_overview($mail, $message, 0);
               $subj       = IssuetrackerHelperCron::decodeMimeString($overview[0]->subject);
               if (DEBUGIMAP) echo '  Subject:'.$subj."\n";

               $sender     = IssuetrackerHelperCron::getSenderAddress($mail, $message);
               $senderName = IssuetrackerHelperCron::getSenderName($mail, $message);

               // If restricting emails
               if ( $restrict && !empty($remails) ) {
                  if ( ! in_array($sender, $remails)) {
                     if ( $logging )
                        IssuetrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_NOT_KNOWN_EMAIL_ADDR_MSG',$subj,$sender), JLog::WARNING);
                     if (DEBUGIMAP) echo(JText::sprintf('COM_ISSUETRACKER_NOT_KNOWN_EMAIL_ADDR_MSG',$subj,$sender)."\n");
                     $spamcnt    += 1;
                     $failsave   += 1;
                     goto message_end;
                  }
               }

               // Check if this is a banned email address.
               // $emailList = explode("\r\n",$params->get('email_list',''));
               if (in_array($sender, $emailList)) {
                  if ( $logging )
                     IssuetrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_BANNED_EMAIL_ADDR_MSG',$subj,$sender), JLog::WARNING);
                  if (DEBUGIMAP) echo(JText::sprintf('COM_ISSUETRACKER_BANNED_EMAIL_ADDR_MSG',$subj,$sender)."\n");
                  $spamcnt    += 1;
                  $failsave   += 1;
                  goto message_end;
               }

               // Before we do anything else let us check that this is not SPAM.
               // Use the plain message text as the issue description
               $body = IssuetrackerHelperCron::getBody($mail, $message);

               $spam = 0;
               // Run spam checks on the message.
               $isSpam  = intval(IssuetrackerHelperCron::_isSpam($body, $sender));
               if ($isSpam) {
                  if ( $logging )
                     IssuetrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_IMAP_POSSIBLE_SPAM_MSG',$subj,$sender), JLog::WARNING);
                  if (DEBUGIMAP) echo(JText::sprintf('COM_ISSUETRACKER_IMAP_POSSIBLE_SPAM_MSG',$subj,$sender)."\n");
                  $spamcnt    += 1;
                  $failsave   += 1;
                  $spam       = 1;
               }
               if ( $spam == 1 ) goto message_end;

               // Run check against Akismet if configured.
               $use_akismet   = $params->get('akismet_api_key','');
               if ( ! empty($use_akismet) && ($body != "[EMAIL - NO MESSAGE BODY FOUND]") ) {
                  if ( IssuetrackerHelperCron::check_akismet($body, $params, $sender, $senderName) ) {
                     if ( $logging )
                        IssuetrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_AKISMET_DETECTED_SPAM_EMAIL_MSG',$subj,$sender), JLog::WARNING);
                     if (DEBUGIMAP) echo(JText::sprintf('COM_ISSUETRACKER_AKISMET_DETECTED_SPAM_EMAIL_MSG',$subj,$sender)."\n");
                     $spamcnt    += 1;
                     $failsave   += 1;
                     $spam       = 1;
                  }
               }
               if ( $spam == 1 ) goto message_end;

               // Check if this is an issue update or a new issue.
               $cstrength = $params->get('reply_detection',0);

               // We first check the email for our custom header values, in which case it is probably a reply.
               // However note that a lot of email clients strip out custom headers so we cannot rely on that solely.
               // We then check the email header to see if the string [Issue:###] exists in the subject line?
               // If so, then we can probably assume that this is a response to an existing issue.
               $custval = IssuetrackerHelperCron::getCustomHeaders($mail, $message);
               if ( $cstrength == 0 || $cstrength == 1 )
                  $pos = strpos($subj, '[Issue:');
               else
                  $pos = strpos($subj, '[');

               if ( $pos === false ) {
                  // No match was found in the subject headers.
                  $existing = 0;
               } else {
                  $existing = 1;
               }

               if ( $custval[0] != 0 ) {
                  // We have custom values so use them. We only check for the first (the alias) here, since it we have one we assume we have both.
                  $existing = 1;
               }

               if ( $existing == 0) {
                  if (DEBUGIMAP) {
                     echo '  New case'."\n";
                     echo '    Sender:'.$sender."\n";
                     echo '    SenderName:'.$senderName."\n";
                  }

                  // Check if this a registered user by looking at the email address.
                  $okay = true;
                  $query  = "SELECT COUNT(*) as count FROM `#__it_people` ";
                  $query .= "WHERE person_email=".$db->quote($sender);
                  $db->setQuery($query);
                  $cnt = $db->loadResult();

                  if ($cnt <= 0 ) {
                     if (DEBUGIMAP) echo("  Sender is not currently registered.\n");
                     if ( $logging )
                        IssuetrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_SENDER_NOT_REGISTERED_MSG',$sender), JLog::INFO);
                     if ($params->get('imap_guest_create')) {
                        if (DEBUGIMAP) echo("  Public creation by non registered users possible.\n");
                     } else {
                        if (DEBUGIMAP) echo("  Public creation by non registered users disallowed.\n");
                        if ( $logging )
                           IssuetrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_PUBLIC_CREATION_OF_ISSUES_DISALLOWED_BY_EMAIL_MSG'), JLog::WARNING);
                        $okay = false;
                     }
                  }

                  // Use the html message text as the issue description
                  $body = IssuetrackerHelperCron::getBody($mail, $message, 'TEXT/HTML');

                  if ($okay) {
                     // Build our issue record
                     $row = JTable::getInstance('Itissues', 'IssuetrackerTable');
                     if (!isset($row)) exit('Cannot open issue table for writing'); // We kill it here if we can't open the table

                     $row->id                      = ""; //auto-assigned by system
                     $row->priority                = $params->get('imap_def_priority');
                     if (empty($row->priority)) $row->priority = $params->get('def_priority');
                     $row->status                  = $params->get('imap_def_status');
                     if (empty($row->status)) $row->status = $params->get('def_status');
                     $row->related_project_id      = $def_project;
                     // $row->related_project_id      = $params->get('imap_def_project');
                     // if (empty($row->related_project_id)) $row->related_project_id = $params->get('def_project');
                     $row->assigned_to_person_id   = $params->get('imap_def_assignee');
                     if (empty($row->assigned_to_person_id) || $row->assigned_to_person_id == 0 ) $row->assigned_to_person_id = $params->get('def_assignee');
                     $row->identified_date         = date("Y/m/d H:i:s");

                     $issue_summary = $subj;
                     if ( strlen($issue_summary) <= 254 ) {
                        $row->issue_summary  = $issue_summary;
                     } else {
                        $row->issue_summary  = substr ($issue_summary, 0, 254);
                     }

                     $query  = "SELECT id from `#__it_people` WHERE person_email = '".IssuetrackerHelperCron::safe($sender)."'";
                     $db->setQuery($query);
                     if (DEBUGIMAP) print ("Query $query\n");
                     $res = $db->loadResult();
                     $def_identby   = $params->get('def_identifiedby','0');
                     $def_notify    = $params->get('def_notify', 0);

                     if ( empty($res) ) {
                        $row->identified_by_person_id = $def_identby;
                     } else {
                        $row->identified_by_person_id = $res;
                     }

                     $row->alias = IssuetrackerHelperCron::_generateNewAlias(10, $params->get('initial_site', 'Z'));
                     $row->issue_description = IssuetrackerHelperCron::clean_description($body);

                     // Populate the progress field with user details if not registered.
                     if ($row->identified_by_person_id == $def_identby) {
                        $cnewperson    = $params->get('create_new_person','0');
                        $def_role      = $params->get('def_role', '2');
                        // $def_project   = $params->get('def_project', '10');

                        $row->progress = null;
                        if ( $cnewperson == '0' ) {
                           $row->progress .= JText::_('COM_ISSUETRACKER_REPORTED_BY_TEXT') . $senderName . "<br />";
                           $row->progress .= JText::_('COM_ISSUETRACKER_EMAIL_TEXT') .  $sender;
                        } else {
                           $Uname = ucwords(str_replace(array('.','_','-','@'),'_',substr($sender,0)));
                           $gnotify = $def_notify;
                           $identby = IssuetrackerHelperCron::create_new_person ( $senderName, $Uname, $sender, $gnotify, $def_role, $def_project);
                           if ( $identby == '' || $identby == 0 ) {
                              if ( $logging )
                                 IssueTrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_PERSON_CREATE_ERROR_MSG',$senderName,$sender,$Uname),JLog::ERROR);
                              $identby = IssuetrackerHelperCron::_get_anon_user();
                              // Add details to progress field since we could not create the user.
                              $row->progress .= JText::_('COM_ISSUETRACKER_REPORTED_BY_TEXT') . $senderName . "<br />";
                              $row->progress .= JText::_('COM_ISSUETRACKER_EMAIL_TEXT') .  $sender . "<br />";
                           }
                           $row->identified_by_person_id = $identby;
                        }
                     }

                     if (!$row->check()) {
                        if ( $logging )
                           IssueTrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_ISSUE_CHECK_ERROR_MSG',$senderName,$sender),JLog::ERROR);
                        $failsave += 1;
                        goto newrec_end;
                     }

                     // The JTable store routine itself raises calls to set session cookies, and session cache.  Hmmm
                     if ($row->store()) {
                        // Check if using alternative alias formats.
                        $iformat = $params->get('iformat', '0');
                        $oalias  = $row->alias;
                        if ( $iformat > 0 ) {
                           $rid     = $row->id;
                           $len     = 10;
                           $nalias = IssueTrackerHelper::checkAlias ($rid, $oalias, $len, $iformat );
                           $row->alias = $nalias;
                        }

                        // What about progress records?
                        if ( ! empty($row->progress) ) {
                           // Use the registered user group, make unpublished and set to private.
                           $rgroup           = 2;
                           $progresspublic   = 0;
                           $pstate           = 0;
                           $lineno           = 1;
                           $progtext         = str_replace(array("'", '"'), array("\\'", '\\"'), $row->progress);

                           // Save record in the table.
                           $query = 'INSERT INTO `#__it_progress` (issue_id, alias, progress, public, state, lineno, access) ';
                           $query .= 'VALUES('.$row->id .',"'. $row->alias.'","'. $progtext .'",'. $progresspublic .','. $pstate .','. $lineno .','. $rgroup .')';
                           $db->setQuery( $query );
                           $db->execute();
                        }
                        $row->progress = ''; // Empty out our issue progress field.

                        //get the case details for the email messages
                        $query  = "SELECT * ";
                        $query .= "FROM #__it_issues ";
                        $query .= "WHERE id = '".$row->id."' ";
                        $query .= "AND DATE_FORMAT(identified_date,'%Y-%m-%d-%H-%i-%s')='".date('Y-m-d-H-i-s',strtotime($row->identified_date))."' ";
                        $query .= "AND issue_summary=".$db->quote($row->issue_summary);
                        $db->setQuery($query);
                        $case = $db->loadAssoc();

                        // Get the assignee details
                        $query  = "SELECT person_email as email, person_name as name, email_notifications as anotify ";
                        $query .= "FROM `#__it_people` ";
                        $query .= "WHERE id=".$case['assigned_to_person_id']." ORDER BY username";
                        $db->setQuery($query);
                        $assignee = $db->loadAssoc();

                        // Notify the sender that the case was received
                        if ( !DEBUGIMAP )
                           IssueTrackerHelper::send_email('user_new', $sender, $case);

                        // Notify assignee of new issue
                        if ( $assignee['anotify'] )
                           IssuetrackerHelper::send_email('ass_new', $assignee['email'], $case); //send new message

                        $newcases += 1;

                        if ($params->get('imap_attachments')) {
                           // Process attachments
                           $attachcnt += IssuetrackerHelperCron::process_attachments($mail, $message, $row, $params, $senderName);
                        }
                        if ($params->get('imap_deletemessages')) imap_delete($mail, $message);
                     } else {
                        if ( $logging )
                           IssuetrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_ERROR_SAVING_EMAIL_ISSUE_MSG'), JLog::ERROR);
                        // echo("Error saving case\n");
                        $failsave += 1;
                     } // End if row->store
                     newrec_end:                     // Ext point for row->check failure.
                  }
               } else {
                  // We have custom header settings, or our searched string is present - so this could be an existing case
                  $sender     = IssuetrackerHelperCron::getSenderAddress($mail, $message);

                  $closed_status = $params->get('closed_status', '1');

                  // Build up a db query to check if it is a known issue.
                  $query  = "SELECT i.id, identified_by_person_id, assigned_to_person_id, progress, i.alias ";
                  $query .= "FROM `#__it_issues` AS i ";

                  // If custvals is populated then we have found our headers values, otherwise search the header.
                  $iformat = $params->get('iformat', '0');
                  // Modify regex dependant upon alias format!
                  switch ($iformat) {
                     case 0:
                        // Current basic random number string 10 characters
                        if ( $cstrength == 0 || $cstrength == 1 )
                           $regex = '/\[Issue\:\s*([a-z0-9]{10,10})\s*\]/i';
                        else
                           $regex = '/\[[a-z:\s]{0,}([a-z0-9]{10,10})\s*\]/i';
                           // $regex = '/\[[\S\s]*([a-z0-9]{10,10})\s*\]/i';
                        break;
                     case 1:
                        // Leading character followed by zeros and then the number.
                        if ( $cstrength == 0 || $cstrength == 1 )
                          $regex = '/\[Issue\:\s*([a-z]{1,1}[0-9]{9,9})\s*\]/i';
                        else
                          $regex = '/\[[a-z:\s]{0,}([a-z]{1,1}[0-9]{9,9})\s*\]/i';
                          // $regex = '/\[\S\s]*([a-z]{1,1}[0-9]{9,9})\s*\]/i';
                        break;
                     case 2:
                        // Numeric string padded to right with blanks.
                        if ( $cstrength == 0 || $cstrength == 1 )
                           $regex = '/\[Issue\:\s*([0-9]{1,10})\s*\]/i';
                        else
                          $regex = '/\[[a-z:\s]*(\d+)\s*\]/i';
                          // $regex = '/\[\s*([0-9]{1,10})\s*\]/i';
                        break;
                     default:
                        // Current basic random number string 10 characters
                        if ( $cstrength == 0 || $cstrength == 1 )
                           $regex = '/\[Issue\:\s*([a-z0-9]{10,10})\s*\]/i';
                        else
                           $regex = '/\[[a-z:\s]{0,}([a-z0-9]{10,10})\s*\]/i';
                        break;
                  }

                  if (DEBUGIMAP)
                     echo 'Mail title testing update: '.$subj. ' '.$iformat.' '.$cstrength;
                  if ( $dlogging )
                     IssuetrackerHelperLog::dblog(JText::sprintf('Mail title testing update: %s %s %s',$subj, $iformat,$cstrength), JLog::DEBUG);

                  if ( $custval[0] == 0 || empty($custval[1]) ) {
                     // Try getting the issue (alias) from the subject header.
                     $hasIssue = preg_match($regex, $subj, $matches);

                     if ($hasIssue) {
                        $issuealias = $matches[1];
                        // We need to pad this for format 2.
                        if ( strlen($issuealias) != 10 )
                           $issuealias = str_pad($issuealias, 10, ' ', STR_PAD_RIGHT);
                     } else {
                        // Indicates alias not found. Effectively causes query to fail.
                        $issuealias = 0;
                        if (DEBUGIMAP) echo '    Regex found no alias in header. '.$subj.'\n';
                     }

                     $query .= "LEFT JOIN `#__it_people` AS p ";
                     $query .= "  ON i.identified_by_person_id = p.id  ";
                     // Add our where clause
                     $query .= "WHERE i.status != ".$closed_status;
                     $query .= " AND  i.alias=" . $db->quote($issuealias);
                     $query .= " AND  p.person_email = " . $db->quote($sender);
                  } else {
                     $issuealias = $custval[1];
                     $issueid    = $custval[0];
                     $query .= "WHERE i.status != ".$closed_status;
                     $query .= " AND  i.alias=" . $db->quote($issuealias);
                     $query .= " AND  i.id = ". $db->quote($issueid);
                  }

                  $db->setQuery($query);
                  if (DEBUGIMAP) echo '    Case Query:'.$query."\n";
                  $case = $db->loadRow();
                  $issueid = $case[0];

                  if (DEBUGIMAP) {
                     echo '  Existing issue'."\n";
                     echo '    Subject:  '.$subj."\n";
                     echo '    Issue Id: '.$case[0]."\n";
                     echo '    Alias:    '.$case[4]."\n";
                     echo '    Issue Alias: '.$issuealias."\n";
                  }

                  // Make sure we got a case back
                  if (!isset($case[0]) || count($case)<=0 ) {
                     if ( $logging )
                        IssuetrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_REPLY_EMAIL_NO_ISSUE_EXISTS_MSG',$issuealias), JLog::WARNING);
                     if (DEBUGIMAP) echo(JText::sprintf('COM_ISSUETRACKER_REPLY_EMAIL_NO_ISSUE_EXISTS_MSG',$issuealias).'\n');
                     $okay = false;
                  } else {
                     $okay = true;
                     // We have found the issue so process it
                     // $issueid = $case[0];

                     if (DEBUGIMAP) echo '    Issue ID:'.$issueid."\n";
                     $senderName = IssuetrackerHelperCron::getSenderName($mail, $message);

                     if (DEBUGIMAP) {
                        echo '    Sender: '.$sender."\n";
                        echo '    SenderName: '.$senderName."\n";
                        echo '    Issue Id: '.$issueid."\n";
                        echo '    Identifier Id: '.$case[1]."\n";
                        echo '    Assignee Id: '.$case[2]."\n";
                     }

                     // Check to see if the sender is either the issue identifier, or an assignee.
                     $query  = "SELECT COUNT(*) as count FROM `#__it_people` AS u ";
                     $query .= "WHERE person_email = '".$sender."' ";
                     $query .= "AND ( id = '".$case[1]."' ";
                     $query .= "OR user_id = '".$case[2]."' )";
                     $db->setQuery($query);
                     if (DEBUGIMAP) echo '    Sender Query:'.$query."\n";
                     $cnt = $db->loadResult();

                     if ($cnt <= 0 ) {
                        // if (DEBUGIMAP) echo("  Sender is not the identifier, assignee or administrator.\n");
                        if (DEBUGIMAP) echo(JText::_('COM_ISSUETRACKER_REPLY_EMAIL_FROM_UNKNOWN_SENDER_MSG').'\n');
                        if ( $logging )
                           IssuetrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_REPLY_EMAIL_FROM_UNKNOWN_SENDER_MSG'), JLog::WARNING);
                        // Exit if the sender is not authorized to update this issue.
                        $okay = false;
                     }
                  }

                  if ($okay) {
                     // If user is recognised, update the progress history using the message information
                     // Get username of sender
                     $query = "SELECT username FROM `#__it_people` WHERE person_email=" . $db->quote($sender);
                     if (DEBUGIMAP) echo '    Query:'.$query."\n";
                     $db->setQuery($query);
                     $username = $db->loadResult();

                     // If we did not get a returned name, then set it to the sendername
                     if ( empty($username) || strlen($username) <=0) $u = $senderName;
                     else $u = $username;
                     if (DEBUGIMAP) echo '    SenderUsername:'.$u."\n";

                     // Get the html text of the message body text for the progress update.
                     $body = IssuetrackerHelperCron::getBody($mail, $message, "TEXT/HTML");

                     $sprefix = $params->get('reply_prefix');
                     $msghandler = $params->get('updmsg_handler', 0);
                     $hasreply = 0;

                     if ( !empty($sprefix) ) {
                        $hasreply = IssuetrackerHelperCron::hasReplyAboveLine($body, $sprefix);
                        if ( $hasreply )
                           $body = IssuetrackerHelperCron::extractMessage($body, $sprefix);
                     }

//                    if ( IssuetrackerHelperCron::hasReplyAboveLine($body, $sprefix) ) {
                        // Now extract out string above our line.
                        // $cstring = $params->get('reply_line');
                        // $body = IssuetrackerHelperCron::extractMessage($body, $sprefix.' '.$cstring);
                        // if ( ! empty($sprefix) )
                        //    $body = IssuetrackerHelperCron::extractMessage($body, $sprefix);

                     if ( $msghandler == 1  ||
                           ( $msghandler == 0 && $hasreply == 1 ) )  {
                        if (IssuetrackerHelperCron::update_progress($issueid, $body, $case[4] ) ) {
                           // Get the issue details for the email messages
                           $query  = "SELECT * ";
                           $query .= "FROM `#__it_issues` ";
                           $query .= " WHERE id = ".$case[0];
                           if (DEBUGIMAP) echo '    Case Query:'.$query."\n";
                           $db->setQuery($query);
                           $case = $db->loadAssoc();   // Reuse variable.

                           // Get assignee details
                           $query  = "SELECT person_email, email_notifications , person_name ";
                           $query .= "FROM `#__it_people` ";
                           $query .= "WHERE person_email = ".$db->quote($sender);
                           if (DEBUGIMAP) echo '    Identifier Query:'.$query."\n";
                           $db->setQuery($query);
                           $identifier = $db->loadAssoc();

                           //get assignee details
                           $query  = "SELECT person_email, email_notifications , person_name ";
                           $query .= "FROM #__it_people ";
                           $query .= "WHERE user_id = '".$case['assigned_to_person_id']."'";
                           if (DEBUGIMAP) echo '    Assignee Query:'.$query."\n";
                           $db->setQuery($query);
                           $assignee = $db->loadAssoc();

                           // On an update, only notify user only if config is set to do so
                           if ($identifier['email_notifications']) {
                              IssuetrackerHelper::send_email('user_update', $sender, $case);
                           }

                           // Notify assignee of updates unless the assignee is the one doing the changes
                           if ( $assignee['email_notifications'] && ! ( $sender == $assignee['person_email'] ) )
                              IssuetrackerHelper::send_email('ass_update',$assignee['person_email'], $case);

                           if ($params->get('imap_deletemessages')) imap_delete($mail, $message);
                        } else {
                           if ( $logging )
                              IssuetrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_ERROR_SAVING_ISSUE_UPDATE_MSG',$issuealias), JLog::ERROR);
                           // echo("Error saving issue update\n");
                        } //end if updateprogress

                        if ($params->get('imap_attachments')) {
                           $attachcnt += IssuetrackerHelperCron::process_attachments($mail, $message, $case, $params, $senderName);
                        }
                     } else {
                        $okay = false;
                        if ( $logging )
                           IssuetrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_ERROR_NO_ABOVE_LINE_MSG',$issuealias), JLog::ERROR);
                        if (DEBUGIMAP) echo(JText::sprintf('COM_ISSUETRACKER_ERROR_NO_ABOVE_LINE_MSG',$issuealias).'\n');
                     }
                  }
                  if ($okay)
                     $existingcases += 1;
                  else
                     $failsave += 1;
               }
               message_end:                    // End message processing
               if (DEBUGIMAP) echo "\n\n";
               $totalmessages += 1;
            }
         }

         // Close the mailbox
         if ($params->get('imap_deletemessages') && $emails) imap_close($mail, CL_EXPUNGE);
         else imap_close($mail);

         if ( $logging )
            IssuetrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_IMAP_SUMMARY_MSG',$totalmessages,$newcases,$failsave,$existingcases,$attachcnt,$spamcnt), JLog::INFO);
         echo JText::sprintf('COM_ISSUETRACKER_IMAP_SUMMARY_MSG',$totalmessages,$newcases,$failsave,$existingcases,$attachcnt,$spamcnt)."\n";
      } else {
         if ( $logging )
            IssuetrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_ATTEMPT_TO_ACCESS_DISABLE_CRON_TASK_MSG', 'efetch'), JLog::WARNING);
      }

   }
}

JApplicationCli::getInstance('Issuetrackerimap')->execute();