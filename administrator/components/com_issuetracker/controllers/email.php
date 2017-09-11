<?php
/*
 *
 * @Version       $Id: email.php 1929 2015-02-08 16:25:12Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.4.0
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-02-08 16:25:12 +0000 (Sun, 08 Feb 2015) $
 *
 */

defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
JLoader::import('joomla.application.component.controllerform');

/**
 * Issuetracker Controller
 *
 * @package       Joomla.Components
 * @subpackage    Issuetracker
 */

class IssueTrackerControllerEmail extends JControllerForm
{

   /**
    *
    */
   function __construct() {
      $this->view_list = 'emails';
      parent::__construct();
   }

}