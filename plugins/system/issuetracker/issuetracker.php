<?php
/*
 *
 * @Version       $Id: issuetracker.php 2040 2015-07-21 14:41:21Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.8
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-07-21 15:41:21 +0100 (Tue, 21 Jul 2015) $
 *
 */

defined('_JEXEC') or die( 'Restricted access' );

JLoader::import('joomla.plugin.plugin');
JLoader::import( 'joomla.html.parameter' );

/**
 * Plugin for Issue Tracker
 *
 * @package    Joomla
 * @subpackage Issue Tracker
 */

class plgSystemIssuetracker extends JPlugin
{
   /**
    * Store user method
    *
    * Method is called after user data is stored in the database
    *
    * @param   array    $user holds the new user data
    * @param   boolean     true if a new user is stored
    * @param   boolean     true if user was successfully stored in the database
    * @param   string      $msg message
    */
   function onUserAfterSave( $user, $isnew, $success, $msg)
   {
      if ( $success) {
         $date    = JFactory::getDate();
         $cuser   = JFactory::getUser();
         $db      = JFactory::getDBO();

         $pexists = false;
         $profile = JUserHelper::getProfile($user['id']);
         $phone = '';
         if ( array_key_exists('profile', $profile) ) {
            if ( isset($profile->profile['phone'] )) {
               $phone = $profile->profile['phone'];
               $pexists = true;
            }
         }

         if ( $isnew) {
            // add a record to #__it_people
            $params = JComponentHelper::getParams('com_issuetracker');

            $defrole = $params->get('def_role', 6);
            $defproject = $params->get('def_project', 10);
            $defnotify = $params->get('def_notify', 0);

            $sms_notify = 0;
            $email_notify = 0;
            switch ($defnotify) {
               case 0:
                  // Nothing to do, just return!
                  break;
               case 1:
                  $sms_notify = 0;
                  $email_notify = 1;
                  break;
               case 2:
                  $sms_notify = 1;
                  $email_notify = 0;
                  break;
               case 3:
                  $sms_notify = 1;
                  $email_notify = 1;
                  break;
            }

            $sql  = "INSERT INTO `#__it_people`";
            if ( $pexists ) {
               $sql .= " (user_id, person_email, person_name, registered, created_on, created_by, person_role, assigned_project, email_notifications, sms_notify, phone_number, username  )";
            } else {
               $sql .= " (user_id, person_email, person_name, registered, created_on, created_by, person_role, assigned_project, email_notifications, sms_notify, username  )";
            }
            $sql .= " VALUES ( '" . $user['id'] . "',";
            $sql .= " '" . $user['email'] . "',";
            $sql .= " '" . $user['name'] ."',";
            $sql .= " '1', ";
            $sql .= " '" . $date ."',";
            $sql .= " '" . $cuser->username ."',";
            $sql .= " '" . $defrole ."',";
            $sql .= " '" . $defproject ."',";
            $sql .= " '" . $email_notify ."',";
            $sql .= " '" . $sms_notify ."',";
            if ( $pexists )
               $sql .= " '" . $phone . "',";
            $sql .= " '" . $user['username'] . "')";
            $sql .= " ON DUPLICATE KEY ";
            $sql .= " UPDATE person_email = '".$user['email'] ."', ";
            $sql .= " person_role = '" . $defrole ."',";
            $sql .= " person_name = '" . $user['name'] ."',";
            $sql .= " username = '" . $user['username'] ."',";
            $sql .= " user_id = '". $user['id'] . "',";
            $sql .= " assigned_project = '" . $defproject ."',";
            if ( $pexists )
               $sql .= " phone_number = '" . $phone ."',";
            $sql .= " registered = 1 ";

            $db->setQuery( $sql);
            $db->execute();
         } else { // user is updated
            // update the user record in #__it_people
            $sql = "UPDATE " . $db->quoteName('#__it_people') . " SET " .
                   "registered='1', " .
                   "person_email=\"".$user['email'] . "\", " .
                   "person_name=\"" . $user['name'] ."\", " .
                   "modified_on=\"" . $date . "\" , " .
                   "modified_by=\"" . $cuser->username . "\" , ";
            if ( $pexists )
               $sql .=  "phone_number = \"" . $phone ."\" , " ;

            $sql .=  "username=\"" . $user['username'] . "\" " .
                   "WHERE " . $db->quoteName('user_id') . " = " . $user['id'];

            $db->setQuery( $sql);
            $db->execute();
         }
      }
   }


   /**
    * @param $user
    * @param $success
    * @param $msg
    */
   function onUserAfterDelete( $user, $success, $msg)
   {
      $db = JFactory::getDBO();

      // get Delete Mode setting from com_discussions parameters
      $params = JComponentHelper::getParams('com_issuetracker');

      $deleteMode = $params->get('delete', '0');
      $deleteUser = $params->get('deleteUser', '0');

      switch( $deleteMode) {
         case 1: { // raw
            $sql = 'DELETE FROM '.$db->quoteName('#__it_people') . ' WHERE ' .
                              $db->quoteName('user_id').' = '.$user['id'];

            $db->setQuery( $sql);
            $db->execute();

            break;
         }
         case 2: { // soft
            // 1. update issues table, set all issues of this user to specified userid
            // Change assigned to and identified by.
            $sql = 'UPDATE '.$db->quoteName('#__it_issues') .
                              ' SET ' .
                              $db->quoteName('assigned_to_person_id') . ' = '. $deleteUser .
                              ' WHERE ' .
                              $db->quoteName('assigned_to_person_id') . ' = ' . $user['id'];

            $db->setQuery( $sql);
            $db->execute();

            $sql = 'UPDATE '.$db->quoteName('#__it_issues') .
                              ' SET ' .
                              $db->quoteName('identified_by_person_id') . ' = '. $deleteUser .
                              ' WHERE ' .
                              $db->quoteName('identified_by_person_id') . ' = ' . $user['id'];

            $db->setQuery( $sql);
            $db->execute();

            // 2. now delete user from people table
            $sql = 'DELETE FROM '.$db->quoteName('#__it_people') . ' WHERE ' .
                              $db->quoteName('user_id').' = '.$user['id'];

            $db->setQuery( $sql);
            $db->execute();

            break;
         }
         default: { // 0 (=disabled) and other
            // do nothing, just keep user in table
            break;
         }
      }
   }
}