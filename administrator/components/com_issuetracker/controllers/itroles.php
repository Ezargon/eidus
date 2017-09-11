<?php
/*
 *
 * @Version       $Id: itroles.php 1929 2015-02-08 16:25:12Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.4.0
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-02-08 16:25:12 +0000 (Sun, 08 Feb 2015) $
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Issuetracker Controller
 *
 * @package       Joomla.Components
 * @subpackage    Issuetracker
 */
JLoader::import('joomla.application.component.controllerform');

/**
 * Type controller class.
 */
class IssuetrackerControllerItroles extends JControllerForm
{

   /**
    *
    */
   function __construct() {
        $this->view_list = 'itroleslist';
        parent::__construct();
    }

}
