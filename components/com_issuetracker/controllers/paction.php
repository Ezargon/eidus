<?php
/*
 *
 * @Version       $Id: paction.php 1929 2015-02-08 16:25:12Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.3
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-02-08 16:25:12 +0000 (Sun, 08 Feb 2015) $
 *
 */

// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JLoader::import('joomla.application.component.controllerform');


// TODO Work in progress!

/**
 * @package    Joomla.Site
 * @subpackage com_issuetracker
 */
class IssuetrackerControllerPaction extends JControllerForm
{

   /**
    *
    */
   function __construct() {
      $this->view_list = 'pactions';
      parent::__construct();
   }

   /**
    * @param null $key
    * @return bool|void
    */
   function cancel($key=null)
   {
      parent::cancel($key);

      // Entry may be from itissues, if so change redirection.
      $model = $this->getModel();

      $tt = $model->getReturnPage();
      // print("Return page $tt<p>");
      if ( strpos($tt, 'itissues') !== false ) {
         $this->setRedirect( $model->getReturnPage());
      }

      return;
   }

   /**
    * @param null $key
    * @param null $urlVar
    * @return bool|void
    */
   function save($key = null, $urlVar = null )
   {
      $task       = $this->getTask();
      parent::save($key, $urlVar);

      // TODO Need to change redirection based on where we were called from.
      // $tt = $model->getReturnPage();
      // print("Return page $tt<p>");

      switch ( $task)
      {
         case 'apply':
         case 'save':
            // Entry may be from itissues or pactions views.
            $model = $this->getModel();
            $tt = $model->getReturnPage();
            if ( strpos($tt, 'itissues') !== false ) {
               $this->setRedirect( $model->getReturnPage());
            }
            break;
         case 'save2copy':
             // Should not ever get here!
             print("Task - $task<p>");
            die;
      }
   }
}