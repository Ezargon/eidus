<?php
/*
 *
 * @Version       $Id: documentation.php 2050 2015-08-09 09:48:51Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.4.2
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
 * Class IssueTrackerControllerDocumentation
 */
class IssueTrackerControllerDocumentation extends IssueTrackerController {

    /**
     * Display method for documentation
     */
    function display() {
        // JRequest::setVar('view', 'documentation');
        JFactory::getApplication()->input->set('view', 'documentation');
        parent::display();
    }

}
