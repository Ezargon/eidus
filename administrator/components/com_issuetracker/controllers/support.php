<?php
/*
 *
 * @Version       $Id: support.php 2050 2015-08-09 09:48:51Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.5.0
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-08-09 10:48:51 +0100 (Sun, 09 Aug 2015) $
 *
 */


// no direct access
defined('_JEXEC') or die('Restricted access');

JLoader::import('joomla.application.component.controller');


/**
 * Class IssueTrackerControllerSupport
 */
class IssueTrackerControllerSupport extends IssueTrackerController {

    /**
     *
     */
    function display() {
        JFactory::getApplication()->input->set('view', 'support');
        parent::display();
    }
}